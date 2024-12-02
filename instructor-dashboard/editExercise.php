<?php
$titleName = "Edit Exercise";
include '../connection.php';
include '../header_footer/header_instructor.php';

// Get exercise_id from URL
if (isset($_GET['exercise_id'])) {
    $exercise_id = $_GET['exercise_id'];

    // Fetch exercise details
    $exerciseQuery = "SELECT * FROM exercise WHERE exercise_id = ?";
    $stmt = $conn->prepare($exerciseQuery);
    $stmt->bind_param("s", $exercise_id); // Use string for exercise_id
    $stmt->execute();
    $result = $stmt->get_result();
    $exercise = $result->fetch_assoc();

    if (!$exercise) {
        echo "<script>alert('Exercise not found.'); window.location.href='scenarioManagement.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('No exercise selected.'); window.location.href='scenarioManagement.php';</script>";
    exit;
}

// Update exercise details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new values from the POST request
    $title = $_POST['title'];
    $learningObj_1 = $_POST['learningObj_1'];
    $learningObj_2 = $_POST['learningObj_2'];
    $learningObj_3 = $_POST['learningObj_3'];
    $learningObj_4 = $_POST['learningObj_4'];
    $scenarioQues = $_POST['scenarioQues'] ?: NULL; // Optional field
    $hints = $_POST['hints'];
    $link = $_POST['link'];
    $duration = $_POST['duration'] . " minutes"; // Concatenate "minutes"
    $exerciseOrder = $_POST['exerciseOrder'];
    $exerciseType = $_POST['exerciseType'];
    $difficulty_level = $_POST['difficulty_level'];

    // Update query with the new fields
    $updateQuery = "UPDATE exercise SET title = ?, learningObj_1 = ?, learningObj_2 = ?, learningObj_3 = ?, learningObj_4 = ?, scenarioQues = ?, hints = ?, link = ?, duration = ?, exerciseOrder = ?, exerciseType = ?, difficulty_level = ? WHERE exercise_id = ?";
    $stmt = $conn->prepare($updateQuery);

    // Bind the parameters
    $stmt->bind_param("sssssssssisss", $title, $learningObj_1, $learningObj_2, $learningObj_3, $learningObj_4, $scenarioQues, $hints, $link, $duration, $exerciseOrder, $exerciseType, $difficulty_level, $exercise_id);

    if ($stmt->execute()) {
        echo "<script>alert('Exercise updated successfully.'); window.location.href='viewScenario.php?scenario_id=" . $exercise['scenario_id'] . "';</script>";
    } else {
        echo "<script>alert('Failed to update exercise.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titleName; ?></title>
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .mainContent {
            padding: 30px 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .form-group .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            margin-right: 10px;
        }

        .page-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .page-title span {
            display: inline-block;
            color: #000000;
            border-bottom: 2px solid #000000;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="mainContent">
    <div class="page-title">
            <span><?php echo $titleName; ?></span>
        </div>
        <form method="POST">
            <div class="form-group">
                <label for="title">Exercise Title</label>
                <input type="text" id="title" name="title" value="<?php echo $exercise['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="learningObj_1">Learning Objective 1</label>
                <input type="text" id="learningObj_1" name="learningObj_1" value="<?php echo $exercise['learningObj_1']; ?>" required>
            </div>
            <div class="form-group">
                <label for="learningObj_2">Learning Objective 2</label>
                <input type="text" id="learningObj_2" name="learningObj_2" value="<?php echo $exercise['learningObj_2']; ?>">
            </div>
            <div class="form-group">
                <label for="learningObj_3">Learning Objective 3</label>
                <input type="text" id="learningObj_3" name="learningObj_3" value="<?php echo $exercise['learningObj_3']; ?>">
            </div>
            <div class="form-group">
                <label for="learningObj_4">Learning Objective 4</label>
                <input type="text" id="learningObj_4" name="learningObj_4" value="<?php echo $exercise['learningObj_4']; ?>">
            </div>
            <div class="form-group">
                <label for="duration">Exercise Order</label>
                <input type="number" id="exerciseOrder" name="exerciseOrder" value="<?php echo (int) $exercise['exerciseOrder']; ?>" required>
            </div>

            <div class="form-group">
                <label for="duration">Duration (minutes)</label>
                <input type="number" id="duration" name="duration" value="<?php echo (int) $exercise['duration']; ?>" required>
            </div>
            <div class="form-group">
    <label for="scenarioQues">Scenario Question (Optional)</label>
    <input type="text" id="scenarioQues" name="scenarioQues" value="<?php echo $exercise['scenarioQues']; ?>" placeholder="Enter scenario question">
</div>
<div class="form-group">
    <label for="hints">Hints</label>
    <input type="text" id="hints" name="hints" value="<?php echo $exercise['hints']; ?>" placeholder="Enter hints" required>
</div>
<div class="form-group">
    <label for="link">Page Path Link</label>
    <input type="text" id="link" name="link" value="<?php echo $exercise['link']; ?>" placeholder="Enter link (e.g., pageName.php)" required>
</div>
<div class="form-group">
    <label for="exerciseType">Exercise Type</label>
    <select id="exerciseType" name="exerciseType" required>
        <option value="Offensive Exercise" <?php echo ($exercise['exerciseType'] == 'Offensive Exercise') ? 'selected' : ''; ?>>1 - Offensive</option>
        <option value="Defensive Exercise" <?php echo ($exercise['exerciseType'] == 'Defensive Exercise') ? 'selected' : ''; ?>>2 - Defensive</option>
    </select>
</div>
<div class="form-group">
    <label for="difficulty_level">Difficulty Level</label>
    <select id="difficulty_level" name="difficulty_level" required>
        <option value="Beginner" <?php echo ($exercise['difficulty_level'] == 'Beginner') ? 'selected' : ''; ?>>1 - Beginner</option>
        <option value="Intermediate" <?php echo ($exercise['difficulty_level'] == 'Intermediate') ? 'selected' : ''; ?>>2 - Intermediate</option>
        <option value="Advanced" <?php echo ($exercise['difficulty_level'] == 'Advanced') ? 'selected' : ''; ?>>3 - Advanced</option>
    </select>
</div>
            <button type="submit" class="btn btn-submit">Update Exercise</button>
            <a href="viewScenario.php?scenario_id=<?php echo $exercise['scenario_id']; ?>" class="btn btn-back">Cancel</a>
        </form>
    </div>
</body>
<?php include '../header_footer/footer.php'; ?>
</html>
