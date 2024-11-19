<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php';

if (isset($_GET['id'])) {
    $instructor_id = $_GET['id']; // Get the instructor_id from the URL parameter

    // Correct the query to use 'id' column to fetch student details by the instructor's ID
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $instructor_id); // Bind the correct ID
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $username = $row['username'];
        $email = $row['email'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $class_name = $row['class_name'];
    } else {
        echo "Student not found.";
        exit;
    }
} else {
    echo "No student ID provided.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $class_name = $_POST['class_name'];

    // Correct the query to update the student using the 'id' column
    $update_query = "UPDATE students SET username = ?, email = ?, firstname = ?, lastname = ?, class_name = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'sssssi', $username, $email, $firstname, $lastname, $class_name, $instructor_id); // Use $instructor_id

    if (mysqli_stmt_execute($stmt)) {
        echo "Student updated successfully.";
        header("Location: studentsList.php");
        exit;
    } else {
        echo "Error updating student.";
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
    <title>TARUMT Cyber Range</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">TARUMT Cyber Range</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
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
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseStudents" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Student
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseStudents" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="#">Member Of Groups</a>
                                <a class="nav-link" href="allScenario.php">Scenarios</a>
                            </nav>
                        </div>
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
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="false" aria-controls="collapsePages">
    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
    Administrator
    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
</a>
<div class="collapse" id="collapseAdmin" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        <!-- Manage Instructors -->
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#manageInstructors" aria-expanded="false" aria-controls="manageInstructors">
            Manage Instructors
            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
        </a>
        <div class="collapse" id="manageInstructors" aria-labelledby="headingTwo" data-bs-parent="#collapseAdmin">
            <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="createInstructor.php">Add Instructor</a>
                <a class="nav-link" href="instructorsList.php">Instructor List</a>
            </nav>
        </div>
        
        <!-- Manage Students -->
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#manageStudents" aria-expanded="false" aria-controls="manageStudents">
            Manage Students
            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
        </a>
        <div class="collapse" id="manageStudents" aria-labelledby="headingThree" data-bs-parent="#collapseAdmin">
            <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="createStudent.php">Add Student</a>
                <a class="nav-link" href="studentsList.php">Student List</a>
            </nav>
        </div>

        <!-- Manage Groups -->
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#manageGroups" aria-expanded="false" aria-controls="manageGroups">
            Manage Groups
            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
        </a>
        <div class="collapse" id="manageGroups" aria-labelledby="headingFour" data-bs-parent="#collapseAdmin">
            <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="createGroup.php">Create Group</a>
                <a class="nav-link" href="groupsList.php">Group List</a>
            </nav>
        </div>
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
                    <h1 class="mt-4">Edit Student</h1>
                    <form method="POST" action="">
    <div class="form-floating mb-3">
        <input id="RegisterUsername" name="username" type="text" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        <label for="RegisterUsername">Username</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterEmail" name="email" type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
        <label for="RegisterEmail">Email</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterFirst" name="firstname" type="text" class="form-control" value="<?php echo htmlspecialchars($firstname); ?>" required>
        <label for="RegisterFirst">First Name</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterLast" name="lastname" type="text" class="form-control" value="<?php echo htmlspecialchars($lastname); ?>" required>
        <label for="RegisterLast">Last Name</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterFaculty" name="class_name" type="text" class="form-control" value="<?php echo htmlspecialchars($class_name); ?>" required>
        <label for="RegisterFaculty">Faculty</label>
    </div>
    <div class="form-floating mb-3">
        <input type="submit" value="Update Student" class="btn btn-primary"/>
    </div>
</form>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; TARUMT Cyber Range 2023</div>
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
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
