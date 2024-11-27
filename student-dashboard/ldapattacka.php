<?php
$titleName = "Exploitation of Lightweight Directory Access Protocol (LDAP) - TARUMT Cyber Range";
include '../connection.php';
include '../header_footer/header_student.php';

$exercise_id = 'ldapOA';
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
                $message = "Flags are correct. Redirecting...";
                $is_success = true;
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
                $message = "Flags are correct. Redirecting...";
                $is_success = true;
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
</head>


<div class="container-fluid px-4">
    <h1 class="mt-4">Exploitation of Lightweight Directory Access Protocol (LDAP) Protocol</h1>
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

        <form method="POST" action="ldapattacka.php">
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
        <?php if (!empty($message)): ?>
            <script>
                // Pass the PHP message and success flag to JavaScript
                showAlert("<?php echo $message; ?>", <?php echo $is_success ? 'true' : 'false'; ?>);
            </script>
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

        document.getElementById('submitButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission until confirmation

            // Capture input values
            const usernameInput = document.getElementById('flagInput1').value.trim();
            const passwordInput = document.getElementById('flagInput2').value.trim();
            const flagInput = document.getElementById('flagOnlyInput').value.trim();

            let message = "Please confirm your submission:\n\n";

            // Build the confirmation message based on mode
            if (document.getElementById('toggleMode').checked) {
                message += "Flag: " + (flagInput ? flagInput : "(Empty - May result in no marks)") + "\n";
            } else {
                message += "Username: " + (usernameInput ? usernameInput : "(Empty - May result in no marks)") + "\n";
                message += "Password: " + (passwordInput ? passwordInput : "(Empty - May result in no marks)") + "\n";
            }

            // Display the confirmation popup
            const userConfirmed = confirm(message);

            // If the user confirms, submit the form
            if (userConfirmed) {
                event.target.closest('form').submit(); // Submit the form programmatically
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <?php include '../header_footer/footer.php' ?>