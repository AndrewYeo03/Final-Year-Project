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
    $user_input1 = $_POST['maxretry'];
    $user_input2 = $_POST['bantime'];
    $user_input3 = $_POST['findtime'];


    // Prepare SQL query to fetch flags from the database
    $sql = "SELECT flag_value FROM flag WHERE flag_id IN ('fDC1', 'fDC2', 'fDC3')";
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
    if ($user_input1 === $flags[0] && $user_input2 === $flags[1] && $user_input3 === $flags[2]) {
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
    <style>
        .flag-container {
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .flag-title {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .input-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        /* Radio Button Styles */
        input[type="radio"] {
            margin-right: 5px;
        }

        .input-group label {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            color: #333;
        }

        #submitButton {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #submitButton:hover {
            background-color: #45a049;
        }

        .feedback {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            color: #d9534f;
            /* Optional: red color for error messages */
        }

        .command-box {
            background-color: #f1f1f1;
            max-width: 35%;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin: 15px 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">TARUMT Cyber Range</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..."
                    aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i
                        class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
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
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseStudents" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Student
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseStudents" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
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
                    <h1 class="mt-4">Exploitation of SSH (Secure Shell) Protocol</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Defensive Exercise </li>
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
                            <p><strong>Install Fail2Ban:</strong></p>
                            <p>Remember to update your packages and install fail2ban:</p>
                            <pre><code>sudo apt update</code></pre>
                            <pre><code>apt-get install fail2ban -y</code></pre>

                            <p><strong>Create the Configuration File:</strong></p>
                            <p>Instead of editing the default configuration <code>/etc/fail2ban/jail.conf</code>, create
                                a local override file for customization. This way, updates won’t overwrite your changes:
                            </p>
                            <pre><code>sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local</code></pre>

                            <p><strong>Enable Fail2ban to start on boot:</strong></p>
                            <pre><code>sudo systemctl enable --now fail2ban </code></pre>

                            <p>Check current status of the Fail2ban service:</p>
                            <pre><code>sudo systemctl status -l fail2ban </code></pre>

                            <p><strong>Configure and Edit SSH Jail Configuration:</strong></p>
                            <p>Open the Fail2ban configuration file for SSH jail settings:</p>
                            <pre><code>sudo nano /etc/fail2ban/jail.local</code></pre>

                            <p><strong>Additional info:</strong></p>
                            <p>Review the log file (monitoring Fail2ban's actions):</p>
                            <pre><code>sudo tail -f /var/log/fail2ban.log</code></pre>
                            <p>See if any IPs have been banned:</p>
                            <pre><code>sudo fail2ban-client status sshd</code></pre>
                            <p>Unblock a banned IP address:</p>
                            <pre><code>sudo fail2ban-client unban &lt;ip_address&gt;</code></pre>
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
                    <h2 class="mt-4 question-title" style="padding: 0px 10px;">Exercise C: Implement Fail2ban Intrusion
                        Prevention Tool<span style="float: right; font-weight: normal; font-size:large;">Suggested
                            Duration: 20 Minutes</span></h2>
                    <div class="main-content">
                        <div class="learning-objectives">
                            <h2>Learning Objectives</h2>
                            <ul>
                                <li>Understand the use of Fail2ban as a defense mechanism against SSH brute force
                                    attacks.</li>
                                <li>Gain experience configuring Fail2ban to automatically detect and block suspicious
                                    IPs.</li>
                                <li>Learn to set up alerts and whitelist trusted IPs to enhance SSH security without
                                    impacting trusted users.</li>
                            </ul>
                        </div>

                        <div class="scenario">
                            <h2>Example Scenario</h2>
                            <p>The company’s SSH server has experienced multiple unauthorized login attempts originating
                                from various IP addresses. To secure the server, you have been assigned to implement
                                Fail2ban, an intrusion prevention tool that can automatically detect and block IPs
                                showing suspicious behavior, such as repeated failed login attempts within a short
                                period.</p>
                        </div>

                        <div class="question">
                            <h2>Your task</h2>
                            <p>Install and configure <code>Fail2ban</code> on the SSH server to monitor failed login
                                attempts. Set it to ban any IP address with <code>3 failed login attempts</code> within
                                a <code>1-minute period</code>. Set the <code>ban duration for 24 hours</code> to
                                prevent persistent brute-force attacks. Further configurations can be added by yourself
                                to enhance the security.</p>
                        </div>
                    </div>

                    <!-- Submission Flag Area-->
                    <div class="flag-container">
                        <h2 class="flag-title">Try it out!</h2>
                        <form method="POST" action="sshDefendC.php">
                        <label
                            style="font-size: 18px; font-weight: bold; margin-bottom: 20px; margin-top: 20px; color: #333;">How
                            these configuration settings needs to be set?</label>
                        <div class="command-box">
                            <pre><code style="color: var(--bs-code-color); word-wrap: break-word;">[sshd]<br>enabled = true<br>port = ssh<br>filter = sshd<br>logpath = /var/log/auth.log<br>maxretry = ???<br>bantime = ???<br>findtime = ???</code></pre>
                        </div>
                        <div class="input-group">
                            <label>Changes to: </label>
                            <label><code>maxretry = <input type="num" name="maxretry" id="ans1"></code></label>
                            <label><code>bantime = <input type="num" name="bantime" id="ans2"></code></label>
                            <label><code>findtime = <input type="num" name="findtime" id="ans3"></code></label>
                        </div>


                        <!-- Submit Button -->
                        <button id="submitButton">Submit</button>
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
        document.getElementById('openHintBox').addEventListener('click', function () {
            document.getElementById('hintBox').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('layoutSidenav_nav').style.zIndex = '900'; // Temporarily lower z-index of sidebar
        });

        document.getElementById('closeHintBox').addEventListener('click', function () {
            document.getElementById('hintBox').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('layoutSidenav_nav').style.zIndex = '1000'; // Restore z-index of sidebar
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>