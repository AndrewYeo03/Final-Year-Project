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

//Check if the session has expired
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $_SESSION['timeout_duration']) {
    //Clear the session and redirect to the login page
    session_unset();
    session_destroy();
    echo "<script>alert('Session expired. Please log in again.');</script>";
    header("Location: ../login.php");
    exit();
} else {
    //If the session has not expired, update login_time
    $_SESSION['login_time'] = time();
}

// Fetch exercises based on scenario_id
if (isset($_GET['fetch_exercises']) && isset($_GET['scenario_id'])) {
    $scenario_id = intval($_GET['scenario_id']);
    $query = "SELECT exercise_id, title FROM exercise WHERE scenario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $scenario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $exercises = [];
    while ($row = $result->fetch_assoc()) {
        $exercises[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($exercises);
    exit;
}

// Insert expected command
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $scenario_id = intval($_POST['scenario_id']);
    $exercise_id = $_POST['exercise_id'];
    $expected_command = trim($_POST['expected_command']);

    // Validate the exercise belongs to the scenario
    $validation_query = "SELECT COUNT(*) AS count FROM exercise WHERE exercise_id = ? AND scenario_id = ?";
    $stmt = $conn->prepare($validation_query);
    $stmt->bind_param("si", $exercise_id, $scenario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];

    if ($count > 0) {
        // Insert into actual_answers
        $query = "INSERT INTO actual_answers (exercise_id, expected_command) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $exercise_id, $expected_command);
        if ($stmt->execute()) {
            echo "<script>alert('Actual answer saved successfully!');</script>";
            echo "<script>window.location.href = 'answerList.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error saving actual answer: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid exercise for the selected scenario.');</script>";
    }
}

// Fetch all scenarios
$scenario_query = "SELECT * FROM scenario";
$scenario_result = $conn->query($scenario_query);

// Fetch all exercises to display in the dropdown
$exercise_query = "SELECT * FROM exercise";
$exercise_result = mysqli_query($conn, $exercise_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Add Actual Answer for Scenario</title>
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
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                    Manage Scenario
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="#">Create Scenario</a>
                                        <a class="nav-link" href="#">Manage Scenario</a>
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
                    <h1 class="mt-4">Add Answer to Scenario</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Choose Scenario
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                    <form method="POST">
                    <div class="mb-3">
    <label for="scenario" class="form-label">Select Scenario</label>
    <select name="scenario_id" class="form-select" required>
        <option value="" disabled selected>Select a Scenario</option>
        <?php while ($row = $scenario_result->fetch_assoc()) { ?>
            <option value="<?= htmlspecialchars($row['scenario_id']) ?>"><?= htmlspecialchars($row['title']) ?></option>
        <?php } ?>
    </select>
</div>
<div class="mb-3">
    <label for="exercise" class="form-label">Select Exercise</label>
    <select name="exercise_id" class="form-select" required>
        <option value="" disabled selected>Select an Exercise</option>
        <!-- Dynamically loaded options -->
    </select>
</div>
<div class="mb-3">
    <label for="expected_command" class="form-label">Expected Answer</label>
    <textarea name="expected_command" class="form-control" rows="4" required></textarea>
</div>
<button type="submit" class="btn btn-primary mt-4">Save</button>
                    </form>
                    </table>
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
<script>
document.querySelector('select[name="scenario_id"]').addEventListener('change', function () {
    const scenarioId = this.value;

    fetch(`addAnswer.php?fetch_exercises=true&scenario_id=${scenarioId}`)
        .then(response => response.json())
        .then(exercises => {
            const exerciseDropdown = document.querySelector('select[name="exercise_id"]');
            exerciseDropdown.innerHTML = '<option value="" disabled selected>Select an Exercise</option>';
            
            exercises.forEach(exercise => {
                const option = document.createElement('option');
                option.value = exercise.exercise_id;
                option.textContent = exercise.title;
                exerciseDropdown.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching exercises:', error));
});
</script>

</body>

</html> 