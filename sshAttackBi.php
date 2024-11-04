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
    $user_input1 = $_POST['flagInput1'];
    $user_input2 = $_POST['flagInput2'];

    // Prepare SQL query to fetch flags from the database
    $sql = "SELECT flag_value FROM flag WHERE flag_id IN ('fOA1', 'fOA2')";
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
    if ($user_input1 === $flags[0] && $user_input2 === $flags[1]) {
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
                                <pre><code>nmap &lt;network_address&gt; -p 22 --open</code></pre>
                                <p><strong>Make Use of Possible Username and Password Lists:</strong></p>
                                <p>Utilize the provided lists of potential usernames and passwords to use for brute force attacks.<br>
                                Example commands:</p>
                                <pre><code>cat > users.txt</code></pre>
                                <pre><code>cat > passwords.txt</code></pre>
                                <p><strong>If using Metasploit Framework:</strong></p>
                                <p>Once in the Metasploit console, load the SSH login module:</p>
                                <pre><code>use auxiliary/scanner/ssh/ssh_login</code></pre>
                                <p><strong>To automate the brute force attack using the provided Python script:</strong></p>
                                <p>Install necessary Python libraries:</p>
                                <pre><code>Install necessary Python libraries</code></pre>
                                <p>Execute the script to perform the brute force attack:</p>
                                <pre><code>python async-ssh-bruteforcer.py &lt;target-ip&gt; -u &lt;username&gt; -w &lt;wordlist&gt; -p &lt;port&gt;</code></pre>
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
                        <h2 class="mt-4 question-title" style="padding: 0px 10px;">Exercise B (i): Automating SSH Brute Force Attack<span style="float: right; font-weight: normal; font-size:large;">Suggested Duration: 30 Minutes</span></h2>
                        <div class="main-content">
                            <div class="learning-objectives">
                                <h2>Learning Objectives</h2>
                                <ul>
                                    <li>Understand the fundamentals of automating SSH brute force attacks.</li>
                                    <li>Gain hands-on experience with Python scripting to perform brute force attacks.</li>
                                    <li>Explore common libraries for automation such as asyncssh, paramiko, and termcolor.</li>
                                    <li>Learn to implement automated scripts for identifying valid SSH login credentials.</li>
                                </ul>
                            </div>

                            <div class="question">
                                <h2>Question</h2>
                                <p>In this exercise, you are required to <code>automate a brute force attack</code> on an SSH server using a <code>Python script</code>. Write your own script or modify an existing one to systematically attempt login credentials until access is granted. Utilize libraries like asyncssh and termcolor to enhance the functionality and readability of your script. Once access is achieved, submit your findings here.</p>
                                
                                <p><strong>You can use the provided script here:</strong> <a href="BruteForceAutomationScript.txt" download class="download-link">BruteForceAutomationScript.txt</a></p>
                            </div>
                        </div>


                        <!-- Submission Flag Area-->
                        <div class="flag-container">
                        <h2 class="flag-title">Submission of flag</h2>
                        <form method="POST" action="sshAttackBi.php">
                            <label for="flagInput1">Enter Username:</label>
                            <input type="text" name="flagInput1" id="flagInput1" placeholder="Enter username" style="width: 100%; padding: 8px;">

                            <label for="flagInput2">Enter Password:</label>
                            <input type="text" name="flagInput2" id="flagInput2" placeholder="Enter password" style="width: 100%; padding: 8px;">
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
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
