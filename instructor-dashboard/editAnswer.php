<?php

// Start output buffering
ob_start();

session_start();
//Check if the user role is Instructor
if ($_SESSION['role_id'] != 2) {
    header("Location: ../unauthorized.php");
    exit();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php';

// Check if an ID is passed in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid answer ID.";
    exit();
}

$actual_answer_id = (int) $_GET['id'];

// Query to fetch the current answer data
$query = "
    SELECT 
        aa.id AS actual_answer_id, 
        sc.title AS scenario_name, 
        ex.title AS exercise_name, 
        aa.expected_command
    FROM 
        actual_answers aa
    JOIN 
        exercise ex ON aa.exercise_id = ex.exercise_id
    JOIN 
        scenario sc ON ex.scenario_id = sc.scenario_id
    WHERE 
        aa.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $actual_answer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Answer not found.";
    exit();
}

$row = $result->fetch_assoc();

// Check if form was submitted and handle the update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_expected_command = trim($_POST['expected_command']);

    // Check if there is a change
    if ($new_expected_command !== $row['expected_command']) {
        $update_query = "UPDATE actual_answers SET expected_command = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $new_expected_command, $actual_answer_id);
        if ($update_stmt->execute()) {
            // Redirect to the answer list page after a successful update
            header("Location: answerList.php");
            exit();
        } else {
            // Display the failure message, but this output is captured by output buffering
            echo "<div class='alert alert-danger'>Failed to update answer. Please try again.</div>";
        }
    } else {
        // Display the no change message, but this output is captured by output buffering
        echo "<div class='alert alert-warning'>No changes were made.</div>";
    }
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
    <title><?php echo htmlspecialchars($titleName); ?></title>
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
<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Edit Answer for Scenario</h1>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i> Edit Answer Details
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['actual_answer_id']); ?>">
                            <div class="mb-3">
                                <label for="scenario_name" class="form-label">Scenario Name</label>
                                <input type="text" class="form-control" id="scenario_name" value="<?php echo htmlspecialchars($row['scenario_name']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="exercise_name" class="form-label">Exercise Name</label>
                                <input type="text" class="form-control" id="exercise_name" value="<?php echo htmlspecialchars($row['exercise_name']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="expected_command" class="form-label">Actual Answer</label>
                                <textarea class="form-control" id="expected_command" name="expected_command" rows="10"><?php echo htmlspecialchars($row['expected_command']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Answer</button>
                        </form>
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
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../js/scripts.js"></script>

<!-- JS files required by DataTables -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script src="../js/datatables-simple-demo.js"></script>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.querySelector('.sb-sidenav');
        sidebar.classList.toggle('collapsed');
    });
</script>
</body>
</html> 

<?php 
// End output buffering and flush the output
ob_end_flush(); 
?>
