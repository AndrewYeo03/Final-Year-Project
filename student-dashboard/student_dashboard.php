<?php
$titleName = "Student Dashboard - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

//Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("
    SELECT 
        s.id,
        s.student_id, 
        u.username, 
        u.email, 
        GROUP_CONCAT(student_classes.class_name SEPARATOR ', ') AS class_names
    FROM students s
    INNER JOIN users u ON s.user_id = u.id
    LEFT JOIN student_classes ON s.id = student_classes.student_id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();
$stmt->close();

//Calculate Total Assigned Scenarios
$stmtTotal = $conn->prepare("
    SELECT COALESCE(SUM(quantity), 0) AS total_scenarios
    FROM (
        SELECT cs.class_name, COUNT(cs.scenario_id) AS quantity
        FROM class_scenarios cs
        INNER JOIN student_classes sc ON cs.class_name = sc.class_name
        WHERE sc.student_id = ?
        GROUP BY cs.class_name
    ) AS class_scenario_counts
");
$stmtTotal->bind_param("i", $studentData['id']);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalScenarios = $resultTotal->fetch_assoc()['total_scenarios'] ?? 0;
$stmtTotal->close();

// Calculate Total Completed Scenarios
$stmtSubmitted = $conn->prepare("
    SELECT COUNT(DISTINCT cs.scenario_id) AS submitted_scenarios
    FROM class_scenarios cs
    INNER JOIN student_classes sc ON cs.class_name = sc.class_name
    LEFT JOIN exercise e ON e.scenario_id = cs.scenario_id
    LEFT JOIN submitted_files sf ON sf.exercise_id = e.exercise_id AND sf.student_id = ?
    LEFT JOIN submitted_videos sv ON sv.exercise_id = e.exercise_id AND sv.student_id = ?
    WHERE sc.student_id = ?
    AND sf.exercise_id IS NOT NULL
    AND sv.exercise_id IS NOT NULL
");
$stmtSubmitted->bind_param("iii", $studentData['id'], $studentData['id'], $studentData['id']);
$stmtSubmitted->execute();
$resultSubmitted = $stmtSubmitted->get_result();
$submittedScenarios = $resultSubmitted->fetch_assoc()['submitted_scenarios'];
$stmtSubmitted->close();

// Calculate Total Expired and Incomplete Scenarios
$stmtExpiredIncomplete = $conn->prepare("
    SELECT COUNT(DISTINCT cs.scenario_id) AS expired_incomplete_scenarios
    FROM class_scenarios cs
    INNER JOIN student_classes sc ON cs.class_name = sc.class_name
    INNER JOIN scenario s ON cs.scenario_id = s.scenario_id
    LEFT JOIN exercise e ON e.scenario_id = cs.scenario_id
    LEFT JOIN submitted_files sf ON sf.exercise_id = e.exercise_id AND sf.student_id = ?
    LEFT JOIN submitted_videos sv ON sv.exercise_id = e.exercise_id AND sv.student_id = ?
    WHERE sc.student_id = ?
    AND (
        s.due_date < CURDATE()
        AND (sf.exercise_id IS NULL AND sv.exercise_id IS NULL)
    )
");
$stmtExpiredIncomplete->bind_param("iii", $studentData['id'], $studentData['id'], $studentData['id']);
$stmtExpiredIncomplete->execute();
$resultExpiredIncomplete = $stmtExpiredIncomplete->get_result();
$expiredIncompleteScenarios = $resultExpiredIncomplete->fetch_assoc()['expired_incomplete_scenarios'];
$stmtExpiredIncomplete->close();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Student Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Welcome, <?php echo htmlspecialchars($username); ?></li>
    </ol>

    <!-- Row for Scenario Stats -->
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    Total Assigned Scenarios
                    <h3><?php echo $totalScenarios; ?></h3>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="allScenario.php">View All Scenarios</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    Total Completed Scenarios
                    <h3><?php echo $submittedScenarios; ?></h3>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Submitted</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    Total Expired and Incomplete Scenarios
                    <h3><?php echo $expiredIncompleteScenarios; ?></h3>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="expiredIncompleteScenario.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Info Section -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user me-1"></i>
            Student Profile
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h5>Full Name:</h5>
                    <p><?php echo htmlspecialchars($studentData['username']); ?></p>
                </div>
                <div class="col-md-4">
                    <h5>Email:</h5>
                    <p><?php echo htmlspecialchars($studentData['email']); ?></p>
                </div>
                <div class="col-md-4">
                    <h5>Student ID:</h5>
                    <p><?php echo htmlspecialchars($studentData['student_id']); ?></p>
                </div>
                <div class="col-md-4">
                    <h5>Class:</h5>
                    <p><?php echo htmlspecialchars($studentData['class_names'] ?: 'No class assigned'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php'; ?>