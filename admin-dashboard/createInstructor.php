<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty = $_POST['faculty'];
    $instructor_id = $_POST['instructor_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Secure password hashing

    // Validate the first name and last name (only alphabetic characters and spaces)
    if (!preg_match("/^[A-Za-z ]+$/", $firstname)) {
        $_SESSION['error'] = "First name can only contain alphabets and spaces.";
        header("Location: createInstructor.php");
        exit;
    }

    if (!preg_match("/^[A-Za-z ]+$/", $lastname)) {
        $_SESSION['error'] = "Last name can only contain alphabets and spaces.";
        header("Location: createInstructor.php");
        exit;
    }

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: createInstructor.php");
        exit;
    }

    // Assume role_id for 'Instructor' is known (for example, role_id = 2 for instructors)
    $role_id = 2; // Adjust this based on your roles table

    // Check if email or username already exists
    $checkSql = "SELECT id FROM users WHERE email = ? OR username = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $email, $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $_SESSION['error'] = "The email or username is already in use. Please use a different one.";
        header("Location: createInstructor.php");
        exit;
    } else {
        // Insert user into the 'users' table with the role_id
        $sql = "INSERT INTO users (username, password, email, role_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $email, $role_id);

        // Execute the user insertion
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id; // Get the ID of the inserted user

            // After inserting into the users table and getting the user_id
            $sql2 = "INSERT INTO instructors (user_id, email, instructor_id, username, faculty, firstname, lastname, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("isssssss", $user_id, $email, $instructor_id, $username, $faculty, $firstname, $lastname, $password);

            if ($stmt2->execute()) {
                $_SESSION['success'] = "Instructor added successfully!";
                header("Location: instructorsList.php"); // Redirect after successful submission
                exit;
            } else {
                $_SESSION['error'] = "Error in instructor table: " . $conn->error;
                header("Location: createInstructor.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Error in users table: " . $conn->error;
            header("Location: createInstructor.php");
            exit;
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
        <title>Add Instructor - TARUMT Cyber Range</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">TARUMT Cyber Range</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
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
                            <div class="card">
                                <div class="card-body">
                        
    <div class="card-title"><h1>Add Instructor Page</h1></div>
    <form method="POST" action="createInstructor.php">
    <div class="form-floating mb-3">
        <input id="RegisterUsername" name="username" type="text" class="form-control" placeholder="Username" required="">
        <label for="RegisterUsername">Username</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterPassword" name="password" type="password" class="form-control" placeholder="Password" required="">
        <label for="RegisterPassword">Password</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterEmail" name="email" type="email" class="form-control" placeholder="Email" required="">
        <label for="RegisterEmail">Email</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterFaculty" name="faculty" type="text" class="form-control" placeholder="Faculty" required="">
        <label for="RegisterFaculty">Faculty</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterFaculty" name="instructor_id" type="text" class="form-control" placeholder="Instructor ID" required="">
        <label for="RegisterFaculty">Instructor ID</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisterFirst" name="firstname" type="text" class="form-control" placeholder="First Name" required="">
        <label for="RegisterFirst">First Name</label>
    </div>
    <div class="form-floating mb-3">
        <input id="RegisteredLast" name="lastname" type="text" class="form-control" placeholder="Last Name" required="">
        <label for="RegisteredLast">Last Name</label>
    </div>
    <div class="form-floating mb-3">
        <input type="submit" value="Submit" class="btn btn-primary"/>
    </div>
</form>

                                </div>
                            </div>
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
