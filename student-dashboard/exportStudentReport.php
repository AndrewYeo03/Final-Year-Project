<?php
ob_start();

require '../vendor/autoload.php';
require '../connection.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
//Check if the user role is Student
if ($_SESSION['role_id'] != 1) {
    header("Location: ../unauthorized.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php'; 

// Fetch the logged-in student's ID
$username = $_SESSION['username'];
$studentQuery = "
    SELECT s.student_id 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE u.username = ?
";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($loggedInStudentId);
$stmt->fetch();
$stmt->close();

if (!$loggedInStudentId) {
    die("Error: Unable to fetch student ID. Please contact support.");
}

// Query to fetch student performance details for the logged-in student
$query = "
    SELECT 
        sc.title AS scenario_title, 
        ex.exercise_id, 
        ex.title AS exercise_name, 
        sf.file_content, 
        GROUP_CONCAT(aa.expected_command SEPARATOR '\n') AS expected_command, 
        s.student_id
    FROM submitted_files sf
    JOIN students s ON sf.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN exercise ex ON sf.exercise_id = ex.exercise_id
    JOIN scenario sc ON ex.scenario_id = sc.scenario_id
    JOIN actual_answers aa ON ex.exercise_id = aa.exercise_id
    WHERE sf.file_content IS NOT NULL AND s.student_id = ?
    GROUP BY ex.exercise_id, s.student_id
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loggedInStudentId);
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!$results) {
    die("No data available for the logged-in student.");
}

$scoringCriteria = [];
$criteriaQuery = "SELECT scenario_id, grade_range_min, grade_range_max, grade, status FROM scoring_criteria";
$criteriaResult = $conn->query($criteriaQuery);

if ($criteriaResult) {
    while ($row = $criteriaResult->fetch_assoc()) {
        $scoringCriteria[] = $row;
    }
    $criteriaResult->close();
} else {
    die("Failed to fetch scoring criteria: " . $conn->error);
}

$reportData = [];

// This function highlights the commands that fully match the expected ones
function highlightExactMatches($commandLogArray, $expectedCommandsArray) {
    $highlightedCommandLog = [];
    foreach ($commandLogArray as $command) {
        // Check if the command matches any of the expected commands exactly
        if (in_array($command, $expectedCommandsArray)) {
            // Highlight the command if it fully matches
            $highlightedCommandLog[] = "<span class='highlighted'>" . htmlspecialchars($command) . "</span>";
        } else {
            $highlightedCommandLog[] = htmlspecialchars($command);
        }
    }
    return implode("\n", $highlightedCommandLog);
}

foreach ($results as $row) {
    if (is_null($row['student_id'])) {
        echo "Skipping row with null student_id.<br>";
        continue;
    }

    $fileContent = $row['file_content'];
    $expectedCommand = $row['expected_command'];
    $student_id = $row['student_id']; 

    // Extract commands
    preg_match_all('/^\$(.*)$/m', $fileContent, $commands);
    preg_match_all('/^.*?>(.*)$/m', $fileContent, $outputs);

    $commandLogAnswer = implode("\n", array_map('trim', $commands[1])) . "\n" . implode("\n", array_map('trim', $outputs[1]));
    $commandLogArray = array_filter(array_map('trim', explode("\n", $commandLogAnswer)));
    $expectedCommandsArray = array_filter(array_map('trim', explode("\n", $expectedCommand)));

    // Highlight only the exact matches of the commands
    $highlightedCommandLogAnswer = highlightExactMatches($commandLogArray, $expectedCommandsArray);

    $matchedCommands = count(array_intersect($commandLogArray, $expectedCommandsArray));
    $totalExpected = count($expectedCommandsArray);
    $score = min(100, $totalExpected > 0 ? ($matchedCommands / $totalExpected) * 100 : 0);

    // Determine grade and status
    $grade = 'D';
    $status = 'FAIL';
    foreach ($scoringCriteria as $criteria) {
        if ($score >= $criteria['grade_range_min'] && $score <= $criteria['grade_range_max']) {
            $grade = $criteria['grade'];
            $status = $criteria['status'];
            break;
        }
    }

    // Update 'scores' table
    $insertQuery = "
        INSERT INTO scores (student_id, exercise_id, score, grade, status) 
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            score = VALUES(score), 
            grade = VALUES(grade), 
            status = VALUES(status)
    ";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("ssdss", $row['student_id'], $row['exercise_id'], $score, $grade, $status);
    
    if (!$insertStmt->execute()) {
        echo "Error updating score for student ID {$row['student_id']}: " . $insertStmt->error . "<br>";
    }

    $insertStmt->close();

    // Prepare report data
    $reportData[] = [
        'scenario_title' => $row['scenario_title'],
        'exercise_name' => $row['exercise_name'],
        'student_id' => $row['student_id'],
        'command_log_answer' => $highlightedCommandLogAnswer, // Use the highlighted version here
        'expected_command' => $expectedCommand,
        'score' => $score,
        'grade' => $grade,
        'status' => $status
    ];
}

echo "<pre>";
print_r($reportData);
echo "</pre>";

// Close the connection
$conn->close();

// Generate the PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// Add custom CSS for styling the table to better fit the PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Student Report</title>
    <style>
        /* Your styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
            font-size: 10px;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
        }
        pre {
            white-space: pre-wrap; 
            word-wrap: break-word; 
            font-size: 8px; 
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Student Report</h1>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Scenario Title</th>
                <th>Exercise Name</th>
                <th>Command Log Answer</th>
                <th>Expected Command</th>
                <th>Score</th>
                <th>Grade</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

foreach ($reportData as $data) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($data['scenario_title']) . '</td>';
    $html .= '<td>' . htmlspecialchars($data['exercise_name']) . '</td>';
    $html .= '<td><pre>' . $data['command_log_answer'] . '</pre></td>';
    $html .= '<td><pre>' . htmlspecialchars($data['expected_command']) . '</pre></td>';
    $html .= '<td class="text-center">' . htmlspecialchars($data['score']) . '</td>';
    $html .= '<td class="text-center">' . htmlspecialchars($data['grade']) . '</td>';
    $html .= '<td class="text-center">' . htmlspecialchars($data['status']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table></body></html>';

// Load and render the PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Ensure landscape orientation is set
ob_end_clean();
$dompdf->render();
$dompdf->stream('Student_Report.pdf', ['Attachment' => 1]);
?>
