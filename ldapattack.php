<?php
session_start();
include 'connection.php';

// Initialize error message variable
$error_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the flag only field is submitted
    if (!empty($_POST['flagOnlyInput'])) {
        // Process flag-only submission
        $flagOnlyInput = trim($_POST['flagOnlyInput']);

        // SQL query to fetch the specific flag for the 'flag only' option
        $sql = "SELECT flag_value FROM flag WHERE flag_id = 'fldapOB'";
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
    }
    // Check if username and password fields are submitted
    elseif (!empty($_POST['flagInput1']) && !empty($_POST['flagInput2'])) {
        $user_input1 = trim($_POST['flagInput1']);
        $user_input2 = trim($_POST['flagInput2']);

        // Prepare SQL query to fetch username and password flags
        $sql = "SELECT flag_id, flag_value FROM flag WHERE flag_id IN ('fldapOA1', 'fldapOA2') ORDER BY flag_id";
        $result = $conn->query($sql);

        $flags = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $flags[$row['flag_id']] = trim($row['flag_value']);
            }
        }

        // Validate username and password flags
        if (isset($flags['fldapOA1']) && isset($flags['fldapOA2'])) {
            if ($user_input1 === $flags['fldapOA1'] && $user_input2 === $flags['fldapOA2']) {
                header("Location: submitVideoLink.php");
                exit();
            } else {
                $error_message = "Username or password is incorrect. Please try again.";
            }
        } else {
            $error_message = "Username and password flags not found in the database.";
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
        <a class="navbar-brand ps-3" href="index.html">TARUMT Cyber Range</a>
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
                        <a class="nav-link" href="index.html">
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
                                <a class="nav-link" href="allScenario.html">Scenarios</a>
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
                        <li class="breadcrumb-item active">Difficulty Level: Beginner</li>
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
                            <p><strong>Identify the interface used by LDAP: </strong></p>
                            <p>Pay attention to the network interface you select in Wireshark to capture the correct traffic</p>
                            <p><strong>Explore LDAP commands: </strong></p>
                            <p>Remember that LDAP queries can be made using simple command-line tools</p>
                            <pre><code>- man ldapsearch</code></pre>
                        </div>
                    </div>

                    <!-- Main Content/ Description of Scenario -->
                    <h2 class="mt-4 question-title" style="padding: 0px 10px;">Exercise A : LDAP Injection on a LDAP Server<span style="float: right; font-weight: normal; font-size:large;">Suggested Duration: 20 Minutes</span></h2>
                    <div class="main-content">
                        <div class="learning-objectives">
                            <h2>Learning Objectives</h2>
                            <ul>
                                <li>Understand the risks associated with unencrypted LDAP traffic</li>
                                <li>Learn how to capture and analyze LDAP traffic using tools like Wireshark.</li>
                                <li>Gain hands-on experience with executing LDAP queries using intercepted credentials.</li>
                                <li>Recognize the importance of implementing security measures such as encryption and access controls to protect directory services.</li>
                            </ul>
                        </div>

                        <div class="question">
                            <h2>Question</h2>
                            <p>In this exercise, you are required to <code>conduct a LDAP Injection attack</code> on an LDAP server using manual input command. You can utilize tools such as <code>Wireshark</code> to gain unauthorized access to the target machine. Your objective is to explore the process of LDAP attack by identifying valid credentials and establishing access. After your attack successful, submit your results here!</p>
                            <p>If you cannot complete the LDAP Injection, you can still submit the username and password retrieve during the process by toggling the submission space!</p>
                        </div>
                    </div>


                    <div class="flag-container">
                        <h2 class="flag-title">Submission of Flag</h2>

                        <!-- Toggle Switch -->
                        <div class="toggle-container">
                            <label class="toggle-switch">
                                <input type="checkbox" id="toggleMode" onclick="toggleFormMode()">
                                <span class="slider"></span>
                            </label>
                            <span id="toggleLabel">Submit Username/Password</span>
                        </div>

                        <form method="POST" action="ldapattack.php">
                            <!-- Username and Password Input Fields -->
                            <div id="usernamePasswordFields">
                                <label for="flagInput1">Enter Username:</label>
                                <input type="text" name="flagInput1" id="flagInput1" placeholder="Enter username" style="width: 100%; padding: 8px;">

                                <label for="flagInput2">Enter Password:</label>
                                <input type="text" name="flagInput2" id="flagInput2" placeholder="Enter password" style="width: 100%; padding: 8px;">
                            </div>

                            <!-- Flag Only Input Field -->
                            <div id="flagField" style="display: none;">
                                <label for="flagOnlyInput">Enter Flag:</label>
                                <input type="text" name="flagOnlyInput" id="flagOnlyInput" placeholder="Enter flag" style="width: 100%; padding: 8px;">
                            </div>

                            <button type="submit" id="submitButton" style="margin-top: 10px; padding: 8px 16px;">Submit</button>
                        </form><br>

                        <?php if (!empty($error_message)): ?>
                            <p style="color: red;"><?php echo $error_message; ?></p>
                        <?php endif; ?>
                    </div>

                    <script>
                        function toggleFormMode() {
                            const isFlagOnly = document.getElementById('toggleMode').checked;
                            document.getElementById('usernamePasswordFields').style.display = isFlagOnly ? 'none' : 'block';
                            document.getElementById('flagField').style.display = isFlagOnly ? 'block' : 'none';
                            document.getElementById('toggleLabel').innerText = isFlagOnly ? 'Submit Flag Only' : 'Submit Username/Password';
                        }
                    </script>


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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>