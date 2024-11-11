<?php
session_start();
include 'connection.php';

// List of exercise pages
$exercises = [
    "ldapattacka.php",        // Exercise 1
    "ldapdefenda.php", // Exercise 2
    "ldapattackb.php", // Exercise 3
];

// Initialize the current exercise if not set
if (!isset($_SESSION['current_exercise'])) {
    $_SESSION['current_exercise'] = 0; // Start from the first exercise
}

// Current exercise index
$current_exercise_index = $_SESSION['current_exercise'];

// Initialize error message variable
$error_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the flag only field is submitted
    if (!empty($_POST['flagOnlyInput'])) {
        // Process flag-only submission
        $flagOnlyInput = trim($_POST['flagOnlyInput']);

        // SQL query to fetch the specific flag for the 'flag only' option
        $sql = "SELECT flag_value FROM flag WHERE flag_id = 'fldapOC'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $flagValue = trim($row['flag_value']);

            if ($flagOnlyInput === $flagValue) {
                // Flag matches, navigate to submitVideoLink.php
                header("Location: submitVideoLink.php");
                exit();
            } else {
                $error_message = "Flag is incorrect. Please try again.";
            }
        } else {
            $error_message = "Flag not found in the database.";
        }
    } else {
        $error_message = "Please fill out either the username and password or the flag.";
    }
}
?>

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Exploitation of Lightweight Directory Access Protocol (LDAP) - TARUMT Cyber Range</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/questionLayout.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Andrew Yeo
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Exploitation of Lightweight Directory Access Protocol (LDAP) Protocol</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Offensive Exercise </li>
                        <li class="breadcrumb-item active">Difficulty Level: Intermediate</li>
                    </ol>
                    <!-- Top nav bar -->
                    <div class="top-nav">
                        <button id="openHintBox" class="open-button">Show Hints</button>
                    </div>
                    <!-- Overlay -->
                    <div id="overlay" class="overlay"></div>

                    <!-- Hint Box -->
                    <div id="hintBox" class="hint-box">
                        <button id="closeHintBox" class="close-button">&times;</button>
                        <div class="hint-content">
                            <p><strong>Identify the source IP address of the LDAP Client</strong></p>
                            <p>Check again the packet you captured in Wireshark</p>
                            <p><strong>Change the IP address of Kali Linux into the same as the LDAP Client</strong></p>
                            <p>If the LDAP Client can connect to the Server, why not try the same IP? Try using ip addr command to temporary add a IP address</p>
                            <pre><code>- ip addr add [ip-address] dev [interface-name]</code></pre>
                        </div>
                    </div>

                    <!-- Nav Menu -->
                    <div class="nav-menu">
                        <a href="#" class="back-button"><i class="fas fa-arrow-left"></i></a>
                        <a href="ldapattacka.php" class="nav-link" data-number="1">1</a>
                        <a href="ldapdefenda.php" class="nav-link" data-number="2">2</a>
                        <a href="ldapattackb.php" class="nav-link" data-number="2">3</a>
                        <a href="#" class="next-button"><i class="fas fa-arrow-right"></i></a>
                    </div>

                    <!-- Main Content/ Description of Scenario -->
                    <h2 class="mt-4 question-title" style="padding: 0px 10px;">Offensive Exercise B : LDAP Injection on a LDAP Server with Simple Firewall<span style="float: right; font-weight: normal; font-size:large;">Suggested Duration: 20 Minutes</span></h2>
                    <div class="main-content">
                        <div class="learning-objectives">
                            <h2>Learning Objectives</h2>
                            <ul>
                                <li>Recognize the weakness of implementing security measures that is too simple such as basic firewall.</li>
                                <li>Learn how to overcome firewall that is not carefully planned.</li>
                            </ul>
                        </div>

                        <div class="question">
                            <h2>Question</h2>
                            <p>In this exercise, you are required to <code>conduct a LDAP Injection attack</code> on an LDAP server using manual input command. It is different with <code>Exercise A</code> as it has already applied some simple firewall rules you have completed in <code>Defensive Exercise A</code>. Your objective is to explore the process of overcoming the security measure. After your attack successful, submit the new flag.</p>
                        </div>
                    </div>


                    <div class="flag-container">
                        <h2 class="flag-title">Submission of Flag</h2>
                        <form method="POST" action="ldapattackb.php">
                            <div id="flagField">
                                <label for="flagOnlyInput">Enter Flag:</label>
                                <input type="text" name="flagOnlyInput" id="flagOnlyInput" placeholder="Enter flag" style="width: 100%; padding: 8px;">
                            </div>
                            <button type="submit" id="submitButton" style="margin-top: 10px; padding: 8px 16px;">Submit</button>
                        </form><br>

                        <?php if (!empty($error_message)): ?>
                            <p style="color: red;"><?php echo $error_message; ?></p>
                        <?php endif; ?>
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

    <script>
        document.getElementById('openHintBox').addEventListener('click', function() {
            document.getElementById('hintBox').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('layoutSidenav_nav').style.zIndex = '900'; // Temporarily lower z-index of sidebar
        });

        document.getElementById('closeHintBox').addEventListener('click', function() {
            document.getElementById('hintBox').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('layoutSidenav_nav').style.zIndex = '1000'; // Restore z-index of sidebar
        });

        document.addEventListener('DOMContentLoaded', function() {
            const exercises = ["ldapattacka.php", "ldapdefenda.php", "ldapattackb.php"]; // Corrected order of exercises

            const currentPage = window.location.pathname.split("/").pop();
            const currentIndex = exercises.indexOf(currentPage);

            document.querySelector('.back-button').addEventListener('click', function() {
                if (currentIndex > 0) {
                    window.location.href = exercises[currentIndex - 1];
                } else {
                    alert("You are on the first exercise."); // Optional alert
                }
            });

            document.querySelector('.next-button').addEventListener('click', function() {
                if (currentIndex < exercises.length - 1) {
                    window.location.href = exercises[currentIndex + 1];
                } else {
                    alert("You are on the last exercise."); // Optional alert
                }
            });
        });


        document.getElementById('submitButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission until confirmation

            // Capture input values
            const flagInput = document.getElementById('flagOnlyInput').value.trim();

            let message = "Please confirm your submission:\n\n";

            // Build the confirmation message based on mode

            message += "Flag: " + (flagInput ? flagInput : "(Empty - May result in no marks)") + "\n";

            // Display the confirmation popup
            const userConfirmed = confirm(message);

            // If the user confirms, submit the form
            if (userConfirmed) {
                event.target.closest('form').submit(); // Submit the form programmatically
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>