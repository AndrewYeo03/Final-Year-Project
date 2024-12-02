<?php 
session_start();
// Check if the user role is Instructor
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

// Query to fetch the actual answers and related exercise data
$query = "
    SELECT 
        aa.id AS actual_answer_id, 
        sc.title AS scenario_name, 
        ex.title AS exercise_name, 
        aa.expected_command,
        ex.scenario_id
    FROM 
        actual_answers aa
    JOIN 
        exercise ex ON aa.exercise_id = ex.exercise_id
    JOIN 
        scenario sc ON ex.scenario_id = sc.scenario_id
";

$result = $conn->query($query);

if (!$result) {
    echo "Error fetching answers: " . $conn->error;
    exit();
}

$scenario_exercises = [];

while ($row = $result->fetch_assoc()) {
    // Group exercises by scenario
    $scenario_exercises[$row['scenario_name']][] = $row;
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
    <title>Actual Answer List for Scenario</title>
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
                    <h1 class="mt-4">Scenario Answer List</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i> Scenario and Exercise Answers
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Scenario Name</th>
                                        <th>Exercise Name</th>
                                        <th>Actual Answer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
    <?php
    foreach ($scenario_exercises as $scenario_name => $exercises) {
        $exercise_count = count($exercises);
        $last_exercise_name = ''; // Initialize a variable to track the last exercise name

        foreach ($exercises as $index => $row) {
            ?>
            <tr>
                <!-- Display scenario_name only if it's the first row for this scenario -->
                <?php if ($index == 0) { ?>
                    <td rowspan="<?= $exercise_count ?>">
                        <?= htmlspecialchars($row['scenario_name']) ?>
                    </td>
                <?php } ?>

                <!-- Display exercise_name only if it's a new exercise name -->
                <?php if ($row['exercise_name'] != $last_exercise_name) { ?>
                    <td>
                        <?= htmlspecialchars($row['exercise_name']) ?>
                    </td>
                    <?php
                    $last_exercise_name = $row['exercise_name'];
                } else { ?>
                    <td></td> <!-- Empty cell for exercise_name in case it's a duplicate -->
                <?php } ?>

<!-- Display the actual answer -->
<td>
    <?php
    $lines = explode("\n", htmlspecialchars($row['expected_command']));  // Split answer by newline
    foreach ($lines as $line) {
        echo nl2br($line) . "<br>";  // Display each line with a line break
    }
    ?>
</td>

                <!-- Action buttons for editing and deleting -->
                <td style="display: flex; gap: 10px;">
    <a href="editAnswer.php?id=<?= $row['actual_answer_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
    <a href="deleteAnswer.php?id=<?= $row['actual_answer_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this answer?');">Delete</a>
</td>
            </tr>
            <?php
        }
    }
    ?>
</tbody>
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
    .table {
    width: 100%;
    border-collapse: collapse; /* Ensure borders are collapsed between cells */
}
.table th, .table td , table td a{
    border: 1px solid #dee2e6; /* Adds borders between columns and rows */
    padding: 8px; /* Adjust padding to make the cells more readable */
    text-align: left; /* Align text to the left */
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
document.addEventListener("DOMContentLoaded", () => {
    const scenarioDropdown = document.querySelector("select[name='scenario_id']");
    const exerciseDropdown = document.querySelector("select[name='exercise_id']");

    scenarioDropdown.addEventListener("change", function () {
        const scenarioId = this.value;

        // Clear existing exercises
        exerciseDropdown.innerHTML = '<option value="" disabled selected>Select an Exercise</option>';

        // Fetch exercises for the selected scenario
        fetch(`addAnswer.php?scenario_id=${scenarioId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length) {
                    data.forEach(exercise => {
                        const option = document.createElement("option");
                        option.value = exercise.exercise_id;
                        option.textContent = exercise.title;
                        exerciseDropdown.appendChild(option);
                    });
                } else {
                    const option = document.createElement("option");
                    option.value = "";
                    option.textContent = "No exercises available";
                    exerciseDropdown.appendChild(option);
                }
            })
            .catch(error => console.error("Error fetching exercises:", error));
    });
});

</script>

</body>

</html>