<?php
$titleName = "Exploitation of SSH (Secure Shell) Protocol - TARUMT Cyber Range";
include '../connection.php';
include '../header_footer/header_student.php';

$exercise_id = 'sshDC';
$_SESSION['current_exercise_id'] = $exercise_id;
// Query to fetch the exercise details
$sql = "SELECT * FROM `exercise` WHERE `exercise_id` = '$exercise_id'";
$result = $conn->query($sql);

// Check if the exercise exists
if ($result->num_rows > 0) {
    // Fetch the exercise details
    $row = $result->fetch_assoc();

    // Assign values to variables for easier use
    $exerciseTitle = $row['title'];
    $exerciseType = $row['exerciseType'];
    $difficultyLevel = $row['difficulty_level'];
    $duration = $row['duration'];
    $hints = $row['hints'];
    $learningObj1 = $row['learningObj_1'];
    $learningObj2 = $row['learningObj_2'];
    $learningObj3 = $row['learningObj_3'];
    $scenarioQues = $row['scenarioQues'];
    $question = $row['question'];
} else {
    echo "No exercise found for ID: $exercise_id";
    exit();
}


// Initialize message variables
$message = '';
$is_success = false;

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
        // Flags match
        $message = "Flags are correct. Redirecting...";
        $is_success = true;
    } else {
        // Flags do not match, set an error message
        $message = "Flags are incorrect. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="../css/questionLayout.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script>
        // Function to display an alert message
        function showAlert(message, isSuccess) {
            alert(message);
            if (isSuccess) {
                // Redirect to submitVideoLink.php if the flags are correct
                window.location.href = "submitVideoLink.php";
            }
        }
    </script>
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

        /* Parent container for centering the iframe container */
        .iframe-container-wrapper {
            display: flex;
            justify-content: center;
            /* Center the iframe container horizontally */
            align-items: center;
            /* Center the iframe container vertically (if needed) */
        }

        /* VNC iframe container */
        .iframe-container {
            display: flex;
            justify-content: space-between;
            /* Distribute space between left and right */
            width: 100%;
            max-width: 1200px;
            /* Optional: Limit the overall width */
            margin-top: 5px;
            margin-bottom: 5px;
        }

        /* Individual iframe sections (Left and Right) */
        .iframe-left,
        .iframe-right {
            width: 48%;
            /* Make each iframe take up almost 50% of the container */
            margin: 0 1%;
            /* Space between iframes */
        }

        /* Iframe styling */
        iframe {
            width: 100%;
            /* Full width of the parent div */
            height: 400px;
            /* Fixed height */
            padding-left: 0%;
            padding-right: 0%;
        }
    </style>
</head>

<div class="container-fluid px-4">
    <h1 class="mt-4">Exploitation of SSH (Secure Shell) Protocol</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?php echo $exerciseType; ?></li>
        <li class="breadcrumb-item active">Difficulty Level: <?php echo $difficultyLevel; ?></li>
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
            <?php echo $hints; ?>
        </div>
    </div>

    <?php
    function generateNavMenu($exercise_id)
    {
        global $conn; // Use the $conn from connection.php

        // Step 1: Get the scenario_id for the current exercise_id
        $sql = "SELECT scenario_id FROM exercise WHERE exercise_id = '$exercise_id' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $scenario_id = $row['scenario_id'];

            // Step 2: Get all exercises with the same scenario_id, ordered by exerciseOrder
            $sql = "SELECT exercise_id, link, exerciseOrder FROM exercise WHERE scenario_id = '$scenario_id' ORDER BY exerciseOrder ASC";
            $result = $conn->query($sql);

            $navLinks = [];
            while ($row = $result->fetch_assoc()) {
                $navLinks[] = $row;
            }

            // Step 3: Generate the HTML for the navigation menu
            echo "<nav class='nav1'><ul>";

            // Loop through the exercises and create menu items
            foreach ($navLinks as $link) {
                $isActive = ($link['exercise_id'] == $exercise_id) ? ' active' : '';
                echo "<li><a href='" . $link['link'] . "' class='btn1$isActive'>" . $link['exerciseOrder'] . "</a></li>";
            }

            echo "</ul></nav>";

            // Step 4: Determine the current exercise order for the next and back buttons
            $currentIndex = null;
            foreach ($navLinks as $index => $link) {
                if ($link['exercise_id'] == $exercise_id) {
                    $currentIndex = $index;
                    break;
                }
            }

            // Next and Back buttons
            if ($currentIndex !== null) {
                if ($currentIndex > 0) {
                    $prevLink = $navLinks[$currentIndex - 1];
                    echo "<a href='" . $prevLink['link'] . "' class='btn back-btn'>Back</a>";
                }

                if ($currentIndex < count($navLinks) - 1) {
                    $nextLink = $navLinks[$currentIndex + 1];
                    echo "<a href='" . $nextLink['link'] . "' class='btn next-btn'>Next</a>";
                }
            }
        } else {
            echo "Exercise not found.";
        }
    }

    generateNavMenu($exercise_id);
    ?>

    <!-- Main Content/ Description of Scenario -->
    <h2 class="mt-4 question-title" style="padding: 0px 10px;"><?php echo $exerciseTitle; ?><span style="float: right; font-weight: normal; font-size:large;">Suggested Duration: <?php echo $duration; ?></span></h2>
    <div class="main-content">
        <div class="learning-objectives">
            <h2>Learning Objectives</h2>
            <ul>
                <li><?php echo $learningObj1; ?></li>
                <li><?php echo $learningObj2; ?></li>
                <li><?php echo $learningObj3; ?></li>
            </ul>
        </div>

        <div class="scenario">
            <h2>Example Scenario</h2>
            <p><?php echo $scenarioQues; ?></p>
        </div>

        <div class="question">
            <h2>Your task</h2>
            <p><?php echo $question; ?></p>
            <div class="vncTitle">
                <h2>Let's Try Using This Virtual Machine Here!</h2>
                <!-- VNC Viewer iframe Section -->
                <div class="iframe-container-wrapper">
                    <div class="iframe-container">
                        <!-- Left Iframe -->
                        <div class="iframe-left">
                            <h3 class="iframe-title-attacker">[Attacker's Host]</h3>
                            <iframe src="http://192.168.43.130:6080/vnc.html?host=192.168.43.130&port=6080" frameborder="0" allow="fullscreen"></iframe>
                        </div>

                        <!-- Right Iframe -->
                        <div class="iframe-right">
                            <h3 class="iframe-title-defender">[Defender's Host]</h3>
                            <iframe src="http://192.168.43.133:6080/vnc.html?host=192.168.43.133&port=6080" frameborder="0" allow="fullscreen"></iframe>
                        </div>
                    </div>
                </div>


            </div>
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

    </div>
    <?php if (!empty($message)): ?>
        <script>
            // Pass the PHP message and success flag to JavaScript
            showAlert("<?php echo $message; ?>", <?php echo $is_success ? 'true' : 'false'; ?>);
        </script>
    <?php endif; ?>
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

<?php include '../header_footer/footer.php' ?>