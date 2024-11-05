<?php
session_start(); // Start the session
include 'connection.php';

// List of exercise pages
$exercises = [
    "sshAttackAi.php",        // Exercise 1
    "sshAttackAii.php", // Exercise 2
    "sshAttackBi.php",  // Exercise 3
    "sshAttackBii.php", // Exercise 4
    "sshDefendA.php",   // Exercise 5
    "sshDefendB.php",   // Exercise 6
    "sshDefendC.php"    // Exercise 7
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
    // Get user inputs
    $user_input1 = $_POST['passcode'];

    // Prepare SQL query to fetch flags from the database
    $sql = "SELECT flag_value FROM flag WHERE flag_id IN ('fOA3')";
    $result = $conn->query($sql);

    // Initialize an associative array to store flag values
    $flags = [];

    // Fetch the flag values from the database
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Store flag values in the array
            $flags[] = $row['flag_value'];
        }
    }

    // Compare user inputs with the flags from the database
    if ($user_input1 === $flags[0]) {
        // Flags match, navigate to submitVideoLink.php
        header("Location: submitVideoLink.php");
        exit();
    } else {
        // Flags do not match, set an error message
        $error_message = "Flags are incorrect. Please try again.";
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
        <title>Exploitation of SSH (Secure Shell) Protocol - TARUMT Cyber Range</title>
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
                        <h1 class="mt-4">Exploitation of SSH (Secure Shell) Protocol</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Offensive Exercise </li>
                            <li class="breadcrumb-item active">Difficulty Level: Advanced</li>
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
                                <p><strong>Use Nmap to Identify Targets:</strong></p>
                                <p>Start by scanning your network to identify which hosts have port 22 (SSH) open.<br>
                                For example, use the following command to discover active hosts with SSH open:</p>
                                <pre><code>nmap 192.168.43.0/24 -p 22 --open</code></pre>

                                <p><strong>Prepare Username and Password Lists:</strong></p>
                                <p>Create your lists of potential usernames and passwords to use for brute force attacks.<br>
                                Example commands:</p>
                                <pre><code>cat > users.txt</code></pre>
                                <pre><code>cat > passwords.txt</code></pre>

                                <p><strong>Set Up the Backdoor with SSH Key Authentication:</strong></p>
                                <p>After gaining access, copy your public key to the target machine to establish persistent access. Since SSH supports various authentication mechanisms, we'll focus on key-based authentication.<br>
                                Generate your SSH key pair on the attacker machine:</p>
                                <pre><code>ssh-keygen -t rsa -b 2048 -f ~/.ssh/id_rsa</code></pre>

                                <p>View your public key:</p>
                                <pre><code>cat ~/.ssh/id_rsa.pub</code></pre>

                                <p>Copy this public key for use on the target machine.</p>
                                <p><strong>Create .ssh Directory on the Target and Paste Your Public Key:</strong></p>
                                <p>On the target machine, create the necessary directories and paste your public key.<br>
                                Command to create the directory:</p>
                                <pre><code>mkdir -p ~/.ssh</code></pre>
                                <p>Remember to change the directory and file permissions to ensure authentication succeeds:</p>
                                <pre><code>chmod 700 ~/.ssh</code></pre>
                                <p>Paste your public key in the victim’s machine in <code>~/.ssh/authorized_keys</code>:</p>
                                <pre><code>echo "&lt;PasteYourPublicKeyHere&gt;" >> ~/.ssh/authorized_keys</code></pre>

                                <p><strong>SSH Into the Target Without a Password:</strong></p>
                                <p>With your public key in place, you should be able to SSH into the target machine without needing a password from your attacker machine.<br>
                                For example, you can SSH into the target machine by:</p>
                                <pre><code>ssh -i ~/.ssh/id_rsa TargetHostName@TargetHostIP</code></pre>
                                <p>or</p>
                                <pre><code>ssh TargetHostName@TargetHostIP</code></pre>
                            </div>
                        </div>

                        <!-- Nav Menu -->
                        <div class="nav-menu">
                            <a href="#" class="back-button"><i class="fas fa-arrow-left"></i></a>
                            <a href="sshAttackAi.php" class="nav-link" data-number="1">1</a>
                            <a href="sshAttackAii.php" class="nav-link" data-number="2">2</a>
                            <a href="sshAttackBi.php" class="nav-link" data-number="2">3</a>
                            <a href="sshAttackBii.php" class="nav-link" data-number="2">4</a>
                            <a href="sshDefendA.php" class="nav-link" data-number="2">5</a>
                            <a href="sshDefendB.php" class="nav-link" data-number="2">6</a>
                            <a href="sshDefendC.php" class="nav-link" data-number="2">7</a>
                            <a href="#" class="next-button"><i class="fas fa-arrow-right"></i></a>
                        </div>

                        <!-- Main Content/ Description of Scenario -->
                        <h2 class="mt-4 question-title" style="padding: 0px 10px;">Exercise B (ii): Creating Backdoor Using Reverse SSH Tunnel<span style="float: right; font-weight: normal; font-size:large;">Suggested Duration: 20 Minutes</span></h2>
                        <div class="main-content">
                            <div class="learning-objectives">
                                <h2>Learning Objectives</h2>
                                <ul>
                                    <li>Understand the concept of reverse SSH tunneling and how it can be used to maintain persistence.</li>
                                    <li>Gain experience in configuring SSH tunnels for remote access on compromised systems.</li>
                                    <li>Learn techniques for establishing a backdoor in a secured environment.</li>
                                    <li>Explore the ethical and defensive implications of persistent access techniques.</li>
                                </ul>
                            </div>

                            <div class="question">
                                <h2>Question</h2>
                                <p>In this advanced exercise, after successfully brute-forcing the SSH login credentials, you are required to create a backdoor by <code>setting up a reverse SSH tunnel</code> from the compromised server to your attack machine. Due to firewall restrictions, external connections to internal machines are blocked, which would typically prevent direct access. However, we can bypass this limitation using an <code>SSH port forwarding tunnel</code>, commonly used by system administrators to access servers externally in a secure manner. By establishing a reverse SSH tunnel, you can initiate a secure connection from the compromised server back to your machine, enabling persistent access through an encrypted channel despite firewall restrictions.This tunnel will simulate persistence, enabling future access to the compromised system.</p>
                            </div>
                        </div>


                        <!-- Submission Flag Area-->
                        <div class="flag-container">
                            <h2 class="flag-title">Submission of flag</h2>
                            <form method="POST" action="sshAttackBii.php">
                            <label for="flagInput1">Enter Passcode (Auto generate when configuring remote access success):</label>
                            <input type="text" name="passcode" id="flagInput1" placeholder="Enter passcode" style="width: 100%; padding: 8px;">
                            <button id="submitButton" style="margin-top: 10px; padding: 8px 16px;">Submit</button>
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
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>