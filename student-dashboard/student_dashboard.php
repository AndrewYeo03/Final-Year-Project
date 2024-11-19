<?php
$titleName = "Student Dashboard - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

//Static data (simulate getting from database)
$submittedScenarios = 0;
$expiredScenarios = 0;

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

$stmtTotal = $conn->prepare("
    SELECT SUM(quantity) AS total_scenarios
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
$totalScenarios = $resultTotal->fetch_assoc()['total_scenarios'];
$stmtTotal->close();
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
                    Total Scenarios
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
                    Submitted Scenarios
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
                    Expired Scenarios
                    <h3><?php echo $expiredScenarios; ?></h3>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Expired</a>
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
