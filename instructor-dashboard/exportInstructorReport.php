<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../vendor/autoload.php'; // Ensure Dompdf is included

use Dompdf\Dompdf;
use Dompdf\Options;

// Include connection to the database
include '../connection.php';

$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch instructor details
$username = $_SESSION['username'];
$instructorQuery = "
    SELECT i.id AS instructor_id
    FROM instructors i
    JOIN users u ON i.user_id = u.id
    WHERE u.username = ?
";
$stmt = $mysqli->prepare($instructorQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($instructorId);
$stmt->fetch();
$stmt->close();

if (!$instructorId) {
    die("Error: Unable to fetch instructor details. Please contact support.");
}

$classQuery = "
    SELECT ic.class_name
    FROM instructor_classes ic
    WHERE ic.instructor_id = ?
";
$stmt = $mysqli->prepare($classQuery);
$stmt->bind_param("i", $instructorId);
$stmt->execute();
$classResult = $stmt->get_result();
$classes = $classResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$selectedClass = isset($_GET['class_name']) ? $_GET['class_name'] : (count($classes) > 0 ? $classes[0]['class_name'] : '');

if ($selectedClass) {
    $query = "
        SELECT 
            s1.title AS scenario_title, 
            ex.exercise_id, 
            ex.title AS exercise_name, 
            s.student_id, 
            CONCAT(u.username, ' ', u.email) AS student_name, 
            sf.file_content, 
            GROUP_CONCAT(DISTINCT aa.expected_command ORDER BY aa.id ASC SEPARATOR '\n') AS expected_command
        FROM submitted_files sf
        JOIN students s ON sf.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN student_classes sc ON s.id = sc.student_id
        JOIN exercise ex ON sf.exercise_id = ex.exercise_id
        JOIN scenario s1 ON ex.scenario_id = s1.scenario_id
        JOIN actual_answers aa ON ex.exercise_id = aa.exercise_id
        WHERE sf.file_content IS NOT NULL AND sc.class_name = ?
        GROUP BY ex.exercise_id, s.student_id
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $selectedClass);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $results = [];
}

$scoringCriteria = [];
$criteriaQuery = "SELECT scenario_id, grade_range_min, grade_range_max, grade, status FROM scoring_criteria";
$criteriaResult = $mysqli->query($criteriaQuery);

if ($criteriaResult) {
    while ($row = $criteriaResult->fetch_assoc()) {
        $scoringCriteria[] = $row;
    }
    $criteriaResult->close();
} else {
    die("Failed to fetch scoring criteria: " . $mysqli->error);
}

$reportData = [];

foreach ($results as $row) {
    $fileContent = $row['file_content'];
    $expectedCommand = $row['expected_command'];

    preg_match_all('/^\$(.*)$/m', $fileContent, $commands);
    preg_match_all('/^.*?>(.*)$/m', $fileContent, $outputs);

    $commandLogAnswer = implode("\n", array_map('trim', $commands[1])) . "\n" . implode("\n", array_map('trim', $outputs[1]));
    $commandLogArray = array_filter(array_map('trim', explode("\n", $commandLogAnswer)));
    $expectedCommandsArray = array_filter(array_map('trim', explode("\n", $expectedCommand)));

    $score = min(100, count($expectedCommandsArray) > 0 ? (count(array_intersect($commandLogArray, $expectedCommandsArray)) / count($expectedCommandsArray)) * 100 : 0);

    $grade = 'D';
    $status = 'FAIL';
    foreach ($scoringCriteria as $criteria) {
        if ($score >= $criteria['grade_range_min'] && $score <= $criteria['grade_range_max']) {
            $grade = $criteria['grade'];
            $status = $criteria['status'];
            break;
        }
    }

    $reportData[] = [
        'scenario_title' => $row['scenario_title'],
        'exercise_name' => $row['exercise_name'],
        'student_name' => $row['student_name'],
        'student_id' => $row['student_id'],
        'command_log_answer' => $commandLogAnswer,
        'expected_command' => $expectedCommand,
        'score' => $score,
        'grade' => $grade,
        'status' => $status
    ];
}

$mysqli->close();

// Generate the PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// Add custom CSS for styling the table to better fit the PDF
$html = '<html><body>';
$html .= '<h1 style="text-align: center;">Instructor Report</h1>';
$html .= '<style>
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Ensure consistent column width */
    }
    th, td {
        border: 1px solid black;
        padding: 4px; /* Reduced padding for more content space */
        text-align: left;
        font-size: 10px; /* Smaller font size */
        word-wrap: break-word;
    }
    th {
        background-color: #f2f2f2;
    }
    pre {
        white-space: pre-wrap; 
        word-wrap: break-word; 
        font-size: 8px; /* Smaller font size for pre-formatted text */
    }
</style>';

$html .= '<table>';
$html .= '<thead><tr><th>Scenario Title</th><th>Exercise Name</th><th>Student Name</th><th>Student ID</th><th>Command Log Answer</th><th>Actual Answer</th><th>Score</th><th>Grade</th><th>Status</th></tr></thead>';
$html .= '<tbody>';

foreach ($reportData as $data) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($data['scenario_title']) . '</td>';
    $html .= '<td>' . htmlspecialchars($data['exercise_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($data['student_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($data['student_id']) . '</td>';
    $html .= '<td><pre>' . htmlspecialchars($data['command_log_answer']) . '</pre></td>';
    $html .= '<td><pre>' . htmlspecialchars($data['expected_command']) . '</pre></td>';
    $html .= '<td>' . htmlspecialchars($data['score']) . '</td>';
    $html .= '<td>' . htmlspecialchars($data['grade']) . '</td>';
    $html .= '<td>' . htmlspecialchars($data['status']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';
$html .= '</body></html>';

// Load and render the PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Ensure landscape orientation is set
$dompdf->render();
$dompdf->stream("instructor_report_" . date('Y-m-d') . ".pdf", array("Attachment" => 1));

?>
