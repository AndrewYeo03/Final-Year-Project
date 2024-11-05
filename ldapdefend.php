<?php
session_start(); // Start the session
include 'connection.php';

// Initialize error message variable
$error_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs
    $user_input1 = $_POST['pathname'];
    $passwordAuth = $_POST['passwordAuth'];

    // Prepare SQL query to fetch flags from the database
    $sql = "SELECT flag_value FROM flag WHERE flag_id IN ('fDA')";
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
    if ($user_input1 === $flags[0] && $passwordAuth === "No") {
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
    <title>Exploitation of Lightweight Directory Access Protocol (LDAP) - TARUMT Cyber Range</title>
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
            width: 50%;
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
    </style>
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
                    <h1 class="mt-4">Exploitation of Lightweight Directory Access Protocol (LDAP)</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Defensive Exercise </li>
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
                            <p><strong>Encrypt the packet so that LDAP packets in Wireshark cannot be read!</strong></p>
                            <pre>Consider the use of TLS/SSL to encrypt LDAP traffic<code style="font-size: 0.875em; color: var(--bs-code-color); word-wrap: break-word;"></code></pre>
                        </div>
                    </div>

                    <!-- Main Content/ Description of Scenario -->
                    <h2 class="mt-4 question-title" style="padding: 0px 10px;">Exercise A: Deny LDAP Packet Interception<span style="float: right; font-weight: normal; font-size:large;">Suggested Duration: 10 Minutes</span></h2>
                    <div class="main-content">
                        <div class="learning-objectives">
                            <h2>Learning Objectives</h2>
                            <ul>
                                <li>Understand the benefits of hiding packets that contains credential</li>
                                <li>Gain hands-on experience with securing an LDAP server against Man-in-the-middle attacks.</li>
                            </ul>
                        </div>

                        <div class="scenario">
                            <h2>Example Scenario</h2>
                            <p>You are a security consultant hired by a company concerned about the security of their LDAP server. During your security audit, you discover that the LDAP packets sent is readable, which is vulnerable to interception. The company has asked you to implement defend strategies to strengthen server security.</p>
                        </div>

                        <div class="question">
                            <h2>Your task</h2>
                            <p>Your task is to secure the LDAP server by <code>preventing criminals from reading content in LDAP packets</code>.</p>
                        </div>
                    </div>


                    <!-- Submission Flag Area-->
                    <div class="flag-container">
                        <h2 class="flag-title">Try it out!</h2>
                        <form method="POST" action="sshDefendA.php">
                            <label for="flagInput1" style="font-size: 18px;">1. </label>
                            <!-- SSH Configuration File Name -->
                            <div class="input-group">
                                <label><code style="font-size: 18px;">nano </code></label>
                                <input type="text" name="pathname" id="flagInput1" placeholder="Enter pathname">
                            </div>

                            <label style="font-size: 18px; font-weight: bold; margin-bottom: 20px; margin-top: 20px; color: #333;">2. </label>
                            <!-- Password Authentication Options -->
                            <div class="input-group">
                                <label><code style="font-size: 18px;">:</code></label>
                                <label><input type="radio" name="passwordAuth" id="flagInput2" value="Yes"> Yes</label>
                                <label><input type="radio" name="passwordAuth" id="flagInput3" value="No"> No</label>
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

        //Submission of flag
        const correctUsername = "yiyangtan0519"; // Replace this with linking to database in future
        const correctPassword = "abc123"; //Replace this with linking to database in future

        document.getElementById('submitButton').addEventListener('click', function() {
            const userFlag1 = document.getElementById('flagInput1').value;
            const userFlag2 = document.getElementById('flagInput2').value;
            const feedback = document.getElementById('feedback');

            if (userFlag1 === correctUsername && userFlag2 === correctPassword) {
                feedback.textContent = "Correct answer!";
                feedback.className = "feedback correct";
            } else {
                feedback.textContent = "Flag is incorrect. Please try again.";
                feedback.className = "feedback incorrect";
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>