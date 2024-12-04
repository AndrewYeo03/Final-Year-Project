<?php
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

// Fetch all scoring criteria
$query = "SELECT sc.id, s.title AS scenario_title, sc.grade_range_min, sc.grade_range_max, sc.grade, sc.status, s.scenario_id
FROM scoring_criteria sc 
JOIN scenario s ON sc.scenario_id = s.scenario_id";
$result = $conn->query($query);

if (!$result) {
    die("Error executing query: " . $conn->error);
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
        <div id="layoutSidenav_content">
        <main class="container">
        <div class="container mt-4">
        <h1>Scoring Criteria</h1>
        <table id="dataTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Scenario</th>
                    <th>Grade Range (Min-Max)</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $current_scenario = null; // To track the current scenario
    $first_row = true; // To track if it's the first row of a group

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Check if the current row belongs to the same scenario group
            if (isset($row['scenario_id'])) {
                if ($current_scenario != $row['scenario_title']) {
                    if (!$first_row) {
                        echo "</tr>";
                    }
                    $current_scenario = $row['scenario_title'];
                    $first_row = true;
                }
    
                if ($first_row) {
                    echo "<tr>
                            <td rowspan='4'>{$row['scenario_title']}</td>
                            <td>{$row['grade_range_min']} - {$row['grade_range_max']}</td>
                            <td>{$row['grade']}</td>
                            <td>{$row['status']}</td>
                            <td rowspan='4'>
                                <a href='editScore.php?scenario_id={$row['scenario_id']}' class='btn btn-sm btn-warning'>Edit</a>
                                <a href='deleteScore.php?scenario_id={$row['scenario_id']}' class='btn btn-danger btn-sm' onclick='return confirmDelete();'>Delete</a>
                            </td>
                        </tr>";
                    $first_row = false;
                } else {
                    echo "<tr>
                            <td>{$row['grade_range_min']} - {$row['grade_range_max']}</td>
                            <td>{$row['grade']}</td>
                            <td>{$row['status']}</td>
                        </tr>";
                }
            } else {
                echo "<span class='text-danger'>No ID</span>";
            }
        }
    } else { 
        echo "<tr><td colspan='5' class='text-center'>No Scoring Criteria Found</td></tr>";
    }
    
    if (!$first_row) {
        echo "</tr>";
    }
    ?>
    </tbody>
        </table>
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
function confirmDelete() {
    return confirm('Are you sure you want to delete this scenario and its scores?');
}
</script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.querySelector('.sb-sidenav');
        sidebar.classList.toggle('collapsed');
    });
</script>
</body>

</html>