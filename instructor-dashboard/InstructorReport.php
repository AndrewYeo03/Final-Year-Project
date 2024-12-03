<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php';

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to highlight matching commands in the command log
function highlightMatchingCommands($commandLog, $expectedCommands) {
    // Loop through each expected command and wrap it in a span if it fully matches
    foreach ($expectedCommands as $expected) {
        // Use regex to match the exact command, ensuring it’s a full match
        $highlighted = "<span class='matched-command'>{$expected}</span>";
        // Check if the exact command is found and replace it
        $commandLog = preg_replace("/^" . preg_quote($expected, '/') . "$/m", $highlighted, $commandLog);
    }

    // Highlight commands that contain 'calculate' in green (this is case insensitive)
    $commandLog = preg_replace('/(.*calculate.*)/i', "<span style='color:green;'>$1</span>", $commandLog);

    return $commandLog;
}

// Fetch instructor details
$username = $_SESSION['username'];
$instructorQuery = "
    SELECT i.id AS instructor_id
    FROM instructors i
    JOIN users u ON i.user_id = u.id
    WHERE u.username = ?
";
$stmt = $conn->prepare($instructorQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($instructorId);
$stmt->fetch();
$stmt->close();

if (!$instructorId) {
    die("Error: Unable to fetch instructor details. Please contact support.");
}

// Fetch the classes taught by the instructor
$classQuery = "
    SELECT ic.class_name
    FROM instructor_classes ic
    WHERE ic.instructor_id = ?
";
$stmt = $conn->prepare($classQuery);
$stmt->bind_param("i", $instructorId);
$stmt->execute();
$classResult = $stmt->get_result();
$classes = $classResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle class selection
$selectedClass = isset($_GET['class_name']) ? $_GET['class_name'] : (count($classes) > 0 ? $classes[0]['class_name'] : '');

if ($selectedClass) {
    // Query to fetch student performance details for the selected class, with expected_command filtered by exercise_id
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

    // Extract commands from the file content
    preg_match_all('/^\$(.*)$/m', $fileContent, $commands);
    preg_match_all('/^.*?>(.*)$/m', $fileContent, $outputs);

    $commandLogAnswer = implode("\n", array_map('trim', $commands[1])) . "\n" . implode("\n", array_map('trim', $outputs[1]));
    $commandLogArray = array_filter(array_map('trim', explode("\n", $commandLogAnswer)));
    $expectedCommandsArray = array_filter(array_map('trim', explode("\n", $expectedCommand)));

    // Highlight the commands that match
    $commandLogAnswer = highlightMatchingCommands($commandLogAnswer, $expectedCommandsArray);

    $matchedCommands = count(array_intersect($commandLogArray, $expectedCommandsArray));
    $totalExpected = count($expectedCommandsArray);
    $score = min(100, $totalExpected > 0 ? ($matchedCommands / $totalExpected) * 100 : 0);

    // Re-evaluate grade and status
    $grade = 'D';
    $status = 'FAIL';
    foreach ($scoringCriteria as $criteria) {
        if ($score >= $criteria['grade_range_min'] && $score <= $criteria['grade_range_max']) {
            $grade = $criteria['grade'];
            $status = $criteria['status'];
            break;
        }
    }

    // Always update 'scores' table to reflect latest 'scoring_criteria'
    $insertQuery = "
        INSERT INTO scores (student_id, exercise_id, score, grade, status) 
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            score = VALUES(score), 
            grade = VALUES(grade), 
            status = VALUES(status)
    ";
    $insertStmt = $mysqli->prepare($insertQuery);
    $insertStmt->bind_param("ssdss", $row['student_id'], $row['exercise_id'], $score, $grade, $status);

    if (!$insertStmt->execute()) {
        echo "Error updating score for student ID {$row['student_id']}: " . $insertStmt->error . "<br>";
    }

    $insertStmt->close();

    // Prepare report data
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Instructor Report</title>
    <link rel="icon" href="../pictures/school_logo.ico" type="image/x-icon"/>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="../instructor-dashboard/instructor_dashboard.php">TAR UMT Cyber Range</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="../editProfile.php">Edit Profile</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="../instructor-dashboard/instructor_dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Interface</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInstructors" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Instructor
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseInstructors" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseInstructors" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                    Manage Student & Class
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseInstructors" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="createClass.php">Create Class</a>
                                        <a class="nav-link" href="existingClass.php">Existing Classes</a>
                                        <a class="nav-link" href="archivedClass.php">Archived Classes</a>
                                        <a class="nav-link" href="studentResponse.php">Student Response</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                    Manage Exercises
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="scenarioManagement.php">Manage Scenario</a>
                                        <a class="nav-link" href="addAnswer.php">Add Answer to Scenario</a>
                                        <a class="nav-link" href="scoringCriteria.php">Define Scoring Criteria</a>
                                        <a class="nav-link" href="instructorReport.php">Student Report</a>
                                        <a class="nav-link" href="studentResponse.php">Student Response</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Instructor Report</h1>
                    <form method="GET" action="instructorReport.php" class="mb-4">
                        <label for="class_name">Select Class:</label>
                        <select name="class_name" id="class_name" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['class_name']); ?>" <?php echo $selectedClass == $class['class_name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Student Performance Report
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                            <thead>
        <tr>
            <th>Scenario Title</th>
            <th>Exercise Name</th>
            <th>Student Name</th>
            <th>Student ID</th>
            <th class="column-commands">Command Log Answer</th>
            <th class="column-answers">Actual Answer</th>
            <th>Score</th>
            <th>Grade</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
// Display the report table

foreach ($reportData as $data) {
    echo "<tr>
        <td>{$data['scenario_title']}</td>
        <td>{$data['exercise_name']}</td>
        <td>{$data['student_name']}</td>
        <td>{$data['student_id']}</td>
        <td><pre>{$data['command_log_answer']}</pre></td>
        <td><pre>{$data['expected_command']}</pre></td>
        <td>{$data['score']}</td>
        <td>{$data['grade']}</td>
        <td>{$data['status']}</td>
    </tr>";
}

echo "</tbody></table>";

$mysqli->close();

?>
</tbody>
                            </table>
                            <a href="exportInstructorReport.php?class_name=<?php echo urlencode($selectedClass); ?>" class="btn btn-primary">Download as PDF</a>
                            </div>
                    </div>
                </div>
            </main>
        

        <footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; TARUMT Cyber Range <?php echo date('Y'); ?></div>
            <div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>
</div>
</div>

<style>
    #layoutSidenav {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
    }

    #layoutSidenav_content {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
    }

    footer {
        flex-shrink: 0;
    }

    #layoutSidenav .sb-sidenav {
        width: 240px;
        transition: width 0.3s;
    }

    #layoutSidenav .sb-sidenav.collapsed {
        width: 58px;
    }

    #layoutSidenav_content {
        margin-left: 240px;
        transition: margin-left 0.3s;
    }

    #layoutSidenav .sb-sidenav.collapsed+#layoutSidenav_content {
        margin-left: 58px;
    }
    /* Ensure the columns have adequate width and padding */
    table th, table td {
        padding: 10px;
        text-align: left;
        word-wrap: break-word;
    }

    /* Specific adjustments to improve readability */
    td {
        max-width: 200px;
        white-space: normal; /* Allow for wrapping of long text */
    }

    .column-commands {
        width: 25%; /* Adjust column width for command log */
    }

    .column-answers {
        width: 3%; /* Adjust column width for actual answers */
    }
    .matched-command {
    background-color: #d4edda;  /* Light green background */
    color: #155724;             /* Dark green text */
    font-weight: bold;
    padding: 2px 4px;
    border-radius: 4px;
}
    .table th, 
    .table td {
        border: 1px solid #ddd;
    }
    .table {
        border-collapse: collapse;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../js/scripts.js"></script>
<!-- JS files required by DataTables -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script src="../js/datatables-simple-demo.js"></script>
<script>
        document.getElementById('class_name').addEventListener('change', function () {
            this.form.submit();
        });
    </script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.querySelector('.sb-sidenav');
        sidebar.classList.toggle('collapsed');
    });
</script>
</body>

</html>
