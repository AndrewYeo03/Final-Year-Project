<?php
session_start();
// Check if the user role is Instructor
//if ($_SESSION['role_id'] != 2) {
    //header("Location: ../unauthorized.php");
    //exit();
//}

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include 'connection.php';

// Initialize the noDataMessage variable
$noDataMessage = "";

if (isset($_GET['scenario_id'])) {
    $scenario_id = $_GET['scenario_id'];

    $sql = "SELECT * FROM scoring_criteria WHERE scenario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $scenario_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $noDataMessage = "No scoring criteria found for this scenario.";
        } else {
            $noDataMessage = "";
        }
    } else {
        echo "Error executing query: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['grade_range_min']) && isset($_POST['grade_range_max']) && isset($_POST['grade']) && isset($_POST['status'])) {
        $grade_range_min = $_POST['grade_range_min'];
        $grade_range_max = $_POST['grade_range_max'];
        $grade = $_POST['grade'];
        $status = $_POST['status'];

        $update_sql = "UPDATE scoring_criteria SET grade_range_min = ?, grade_range_max = ?, grade = ?, status = ? WHERE scenario_id = ? AND id = ?";
        $update_stmt = $conn->prepare($update_sql);

        $changes_made = false;

        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $min = $grade_range_min[$i];
            $max = $grade_range_max[$i];
            $gradeValue = $grade[$i];
            $statusValue = $status[$i];
            $idValue = $row['id'];

            // Compare if the values actually changed
            if ($row['grade_range_min'] != $min || $row['grade_range_max'] != $max || $row['grade'] != $gradeValue || $row['status'] != $statusValue) {
                $update_stmt->bind_param("iissii", $min, $max, $gradeValue, $statusValue, $scenario_id, $idValue);

                if ($update_stmt->execute()) {
                    if ($update_stmt->affected_rows > 0) {
                        $changes_made = true;
                    }
                } else {
                    echo "Error executing query for row $i: " . $update_stmt->error;
                }
            }
            $i++;
        }

        if ($changes_made) {
            echo "<script>alert('Scores updated successfully');</script>";
            echo "<script>window.location.href='scoringResult.php';</script>";
        } else {
            echo "<script>alert('No changes were made');</script>";
            echo "<script>window.location.href='scoringResult.php';</script>";
        }
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
    <title>Instructor Dashboard - TARUMT Cyber Range</title>
    <link rel="icon" href="../pictures/school_logo.ico" type="image/x-icon"/>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">TARUMT Cyber Range</a>
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
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
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
                        <a class="nav-link" href="index.php">
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
                                    Manage Student & Group
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseInstructors" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="#">Add Student</a>
                                        <a class="nav-link" href="#">Create Group</a>
                                        <a class="nav-link" href="#">Owned Group</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                    Manage Scenario
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="#">Create Scenario</a>
                                        <a class="nav-link" href="#">Manage Scenario</a>
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
                <!-- Main Content -->
                <div class="container mt-5">
                    <h1 class="mb-4">Reset Scoring Criteria</h1>

                    <form method="POST">
                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Grade Range Min(%)</th>
                                    <th>Grade Range Max(%)</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if (isset($result) && $result->num_rows > 0) {
                                        $i = 0;
                                        while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><input type="number" name="grade_range_min[<?= $i ?>]" class="form-control" value="<?= $row['grade_range_min'] ?>" required></td>
                                    <td><input type="number" name="grade_range_max[<?= $i ?>]" class="form-control" value="<?= $row['grade_range_max'] ?>" required></td>
                                    <td>
                                        <select name="grade[<?= $i ?>]" class="form-select" required>
                                            <option value="A" <?= $row['grade'] == 'A' ? 'selected' : '' ?>>A</option>
                                            <option value="B" <?= $row['grade'] == 'B' ? 'selected' : '' ?>>B</option>
                                            <option value="C" <?= $row['grade'] == 'C' ? 'selected' : '' ?>>C</option>
                                            <option value="D" <?= $row['grade'] == 'D' ? 'selected' : '' ?>>D</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="status[<?= $i ?>]" class="form-select" required>
                                            <option value="pass" <?= $row['status'] == 'pass' ? 'selected' : '' ?>>PASS</option>
                                            <option value="fail" <?= $row['status'] == 'fail' ? 'selected' : '' ?>>FAIL</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php 
                                        $i++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>$noDataMessage</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary mt-4">Update Score</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

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
</body>

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
    .status-box.pass {
        background-color: green;
        color: white;
    }
    .status-box.fail {
        background-color: red;
        color: white;
    }
    table th:nth-child(4), table td:nth-child(4) {
        width: 100px;  /* Set a smaller width for the status column */
        padding: 5px;  /* Reduce padding for more compact appearance */
        text-align: center;  /* Center-align the content */
    }
    .bullet {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-left: 10px;  /* Adds some space between dropdown and the bullet */
    }
</style>
<script>
    function updateStatusBullet(selectElement, index) {
    var bullet = document.getElementById("bullet-" + index);
    var status = selectElement.value;

    if (status === "pass") {
        bullet.style.backgroundColor = "green"; // Green for PASS
    } else if (status === "fail") {
        bullet.style.backgroundColor = "red"; // Red for FAIL
    }
}

// Initial color update when the page loads
window.onload = function() {
    var statusSelects = document.querySelectorAll("select[name^='status']");
    statusSelects.forEach((select, index) => {
        updateStatusBullet(select, index); // Update the bullet color based on the initial status
    });
};

</script>

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