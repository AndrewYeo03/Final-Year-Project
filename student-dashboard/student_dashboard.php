<?php
$titleName = "Student Dashboard - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

//Static data (simulate getting from database)
$totalScenarios = 10;
$submittedScenarios = 7;
$expiredScenarios = 2;

//Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM students INNER JOIN users ON students.user_id = users.id WHERE users.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();
$stmt->close();
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
                    <a class="small text-white stretched-link" href="#">View All Scenarios</a>
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
                    <p><?php echo htmlspecialchars($studentData['class_name']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Suggested Modules -->
    <div class="row">
        <!-- Course Progress -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Course Progress
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">70%</div>
                    </div>
                    <p>Keep it up! You’re progressing well.</p>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Recent Activities
                </div>
                <div class="card-body">
                    <ul>
                        <li>Submitted "Network Security Scenario" on 2024-11-05</li>
                        <li>Started "Linux Fundamentals" Scenario on 2024-11-04</li>
                        <li>Expired "Web Exploitation" Scenario on 2024-10-30</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-bell me-1"></i>
                    Notifications
                </div>
                <div class="card-body">
                    <ul>
                        <li>New Scenario: "Advanced Pentesting" available now!</li>
                        <li>System Maintenance scheduled for 2024-11-15.</li>
                        <li>Update: "Network Fundamentals" Scenario has new tasks.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php' ?>