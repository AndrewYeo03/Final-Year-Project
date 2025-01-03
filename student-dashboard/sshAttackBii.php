<?php
$titleName = "Exploitation of SSH (Secure Shell) Protocol - TARUMT Cyber Range";
include '../connection.php';
include '../header_footer/header_student.php';

$exercise_id = 'sshOB2';
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
    $hints = $row['hints'];
    $duration = $row['duration'];
    $learningObj1 = $row['learningObj_1'];
    $learningObj2 = $row['learningObj_2'];
    $learningObj3 = $row['learningObj_3'];
    $learningObj4 = $row['learningObj_4'];
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
    // Get user input
    $user_input1 = $_POST['passcode'];

    // Prepare SQL query to fetch the flag from the database
    $sql = "SELECT flag_value FROM flag WHERE flag_id = 'fOA3'";
    $result = $conn->query($sql);

    // Fetch the flag value from the database
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $flag_value = $row['flag_value'];

        // Compare user input with the flag value from the database
        if ($user_input1 === $flag_value) {
            // Passcode matches
            $message = "Passcode is correct. Redirecting...";
            $is_success = true;
        } else {
            // Passcode does not match
            $message = "Passcode is incorrect. Please try again.";
        }
    } else {
        // No flag found in the database
        $message = "Error: Flag not found in the database.";
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
                // Redirect to another page if the passcode is correct
                window.location.href = "submitVideoLink.php";
            }
        }
    </script>
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
                <li><?php echo $learningObj4; ?></li>
            </ul>
        </div>

        <div class="question">
            <h2>Question</h2>
            <p><?php echo $question; ?></p>
            <div class="vncTitle">
                <h2>Let's Try Using This Virtual Machine Here!</h2>

                <!-- Button Controls -->
                <div class="vnc-controls">
                    <a href="sshAttackAi.php?action=start" class="vnc-btn vnc-start">Start VNC Server</a>
                    <a href="sshAttackAi.php?action=stop" class="vnc-btn vnc-stop">Stop VNC Server</a>
                </div>

                <!-- VNC Viewer iframe -->
                <h3 style="color: #ff0000;
    margin-bottom: 2px;
    margin-left: 15%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.15);
    font-size: 26px;
    font-weight: bold;font-style: italic;">[Attacker's Machine]</h3><iframe src="http://192.168.43.130:6080/vnc.html?host=192.168.43.130&port=6080"
                    width="800" height="600"
                    frameborder="0" allow="fullscreen">
                </iframe>
            </div>
        </div>
    </div>


    <!-- Submission Flag Area-->
    <div class="flag-container">
        <h2 class="flag-title">Passcode Submission</h2>
        <form method="POST" action="sshAttackBii.php">
            <label for="passcode">Enter Passcode:</label>
            <input type="text" name="passcode" id="passcode" placeholder="Enter passcode" style="width: 100%; padding: 8px;">
            <button type="submit" id="submitButton" style="margin-top: 10px; padding: 8px 16px;">Submit</button>
        </form>
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