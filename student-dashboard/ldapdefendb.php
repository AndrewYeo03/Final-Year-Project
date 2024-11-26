<?php
$titleName = "Exploitation of Lightweight Directory Access Protocol (LDAP) - TARUMT Cyber Range";
include '../connection.php';
include '../header_footer/header_student.php';


$exercise_id = 'ldapDB';
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

// Initialize error message variable
$error_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the flag only field is submitted
    if (!empty($_POST['directoryName']) && !empty($_POST['resultShown'])) {
        $user_input1 = trim($_POST['directoryName']);
        $user_input2 = trim($_POST['resultShown']);

        // Prepare SQL query to fetch username and password flags
        $sql = "SELECT flag_id, flag_value FROM flag WHERE flag_id IN ('fldapDB1', 'fldapDB2') ORDER BY flag_id";
        $result = $conn->query($sql);

        $flags = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $flags[$row['flag_id']] = trim($row['flag_value']);
            }
        }

        // Validate username and password flags
        if (isset($flags['fldapDB1']) && isset($flags['fldapDB2'])) {
            if ($user_input1 === $flags['fldapDB1'] && $user_input2 === $flags['fldapDB2']) {
                $message = "Flags are correct. Redirecting...";
                $is_success = true;
            } else {
                $error_message = "Answer is incorrect. Please try again.";
            }
        }
    } else {
        $error_message = "Please fill out the answers.";
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


<div class="container-fluid px-4">
    <h1 class="mt-4">Exploitation of Lightweight Directory Access Protocol (LDAP)</h1>
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
        </div>
    </div>


    <!-- Submission Flag Area-->
    <div class="flag-container">
        <h2 class="flag-title">Try it out!</h2>
        <form method="POST" action="ldapdefendb.php">
            <label for="flagInput1" style="font-size: 18px;">1. Where does the default SSL Directory located at?</label>
            <div class="input-group">
                <input type="text" name="directoryName" id="directoryName" placeholder="Enter the directory name">
            </div>

            <label for="flagInput2" style="font-size: 18px; font-weight: bold; margin-bottom: 20px; margin-top: 20px; color: #333;">2. Type in <code>ldapwhoami -H ldap:// -x -ZZ</code> to force a SSL/TLS upgrade, what did you see as the result?</label>
            <div class="input-group">
                <input type="text" name="resultShown" id="resultShown" placeholder="Enter the result">
            </div>

            <!-- Submit Button -->
            <button id="submitButton">Submit</button>
        </form><br>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

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
                const directoryName = document.getElementById('directoryName').value.trim();
                const resultShown = document.getElementById('resultShown').value.trim();

                let message = "Please confirm your submission:\n\n";

                // Build the confirmation message
                message += "Directory Name: " + (directoryName ? directoryName : "(Empty - May result in no marks)") + "\n";
                message += "Result: " + (resultShown ? resultShown : "(Empty - May result in no marks)") + "\n";

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