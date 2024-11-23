<?php
$titleName = "Add New Exercise";
include '../connection.php';
include '../header_footer/header_instructor.php';

// Check if `scenario_id` is passed in the URL
if (isset($_GET['scenario_id'])) {
    $scenario_id = $_GET['scenario_id'];

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $exercise_id = $_POST['exercise_id'];  // This will be treated as a string
        $title = $_POST['title'];
        $learningObj_1 = $_POST['learningObj_1'];
        $learningObj_2 = $_POST['learningObj_2'];
        $learningObj_3 = $_POST['learningObj_3'];
        $learningObj_4 = $_POST['learningObj_4'];
        $duration = $_POST['duration'] . " minutes";  // Append "minutes"
        $difficulty_level = $_POST['difficulty_level'];

        // Check if exercise_id already exists
        $checkQuery = "SELECT * FROM exercise WHERE exercise_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $exercise_id);  // Bind as string
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('Exercise ID already exists. Please choose a different ID.');</script>";
        } else {
            // Insert into database
            $insertQuery = "INSERT INTO exercise (exercise_id, scenario_id, title, learningObj_1, learningObj_2, learningObj_3, learningObj_4, duration, difficulty_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sssssisss", $exercise_id, $scenario_id, $title, $learningObj_1, $learningObj_2, $learningObj_3, $learningObj_4, $duration, $difficulty_level);  // Bind exercise_id as string

            if ($stmt->execute()) {
                echo "<script>alert('Exercise added successfully.'); window.location.href='viewScenario.php?scenario_id=$scenario_id';</script>";
            } else {
                echo "<script>alert('Failed to add exercise.');</script>";
            }
        }
    }
} else {
    echo "<script>alert('No scenario selected.'); window.location.href='scenarioManagement.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titleName; ?></title>
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .mainContent {
            padding: 30px 30px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="mainContent">
        <h1><?php echo $titleName; ?></h1>
        <form method="POST">
            <label for="exercise_id">Exercise ID</label>
            <input type="text" id="exercise_id" name="exercise_id" placeholder="Enter unique Exercise ID" required>

            <label for="title">Exercise Title</label>
            <input type="text" id="title" name="title" placeholder="Enter exercise title" required>

            <label for="learningObj_1">Learning Objective 1</label>
            <input type="text" id="learningObj_1" name="learningObj_1" placeholder="Enter learning objective 1" required>

            <label for="learningObj_2">Learning Objective 2</label>
            <input type="text" id="learningObj_2" name="learningObj_2" placeholder="Enter learning objective 2">

            <label for="learningObj_3">Learning Objective 3</label>
            <input type="text" id="learningObj_3" name="learningObj_3" placeholder="Enter learning objective 3">

            <label for="learningObj_4">Learning Objective 4</label>
            <input type="text" id="learningObj_4" name="learningObj_4" placeholder="Enter learning objective 4">

            <label for="duration">Duration (in minutes)</label>
            <input type="number" id="duration" name="duration" placeholder="Enter duration in minutes" required>

            <label for="difficulty_level">Difficulty Level</label>
            <select id="difficulty_level" name="difficulty_level" required>
                <option value="">Select difficulty level</option>
                <option value="1">1 - Beginner</option>
                <option value="2">2 - Intermediate</option>
                <option value="3">3 - Advanced</option>
            </select>

            <button type="submit" class="btn btn-submit">Add Exercise</button>
            <a href="viewScenario.php?scenario_id=<?php echo $scenario_id; ?>" class="btn btn-back">Back to Exercise List</a>
        </form>
    </div>
</body>
<?php include '../header_footer/footer.php' ?>
</html>
