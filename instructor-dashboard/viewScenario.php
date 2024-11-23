<?php
$titleName = "Scenario Details";
include '../connection.php';
include '../header_footer/header_instructor.php';

// Get scenario_id from URL
if (isset($_GET['scenario_id'])) {
    $scenario_id = $_GET['scenario_id'];

    // Fetch scenario details
    $scenarioQuery = "SELECT * FROM scenario WHERE scenario_id = ?";
    $stmt = $conn->prepare($scenarioQuery);
    $stmt->bind_param("i", $scenario_id);
    $stmt->execute();
    $scenarioResult = $stmt->get_result();
    $scenario = $scenarioResult->fetch_assoc();

    // Fetch exercises related to the scenario
    $exerciseQuery = "SELECT * FROM exercise WHERE scenario_id = ?";
    $stmt = $conn->prepare($exerciseQuery);
    $stmt->bind_param("i", $scenario_id);
    $stmt->execute();
    $exerciseResult = $stmt->get_result();
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
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .mainContent {
            padding: 30px 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-add {
            background-color: #28a745;
            color: white;
            margin-bottom: 15px;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-edit {
            background-color: #ffc107;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
    <script>
        function deleteExercise(exercise_id) {
            if (confirm("Are you sure you want to delete this exercise?")) {
                $.ajax({
                    url: 'deleteExercise.php',  // The PHP file that will handle the deletion
                    type: 'POST',
                    data: {
                        exercise_id: exercise_id
                    },
                    success: function(response) {
                        if (response === 'success') {
                            alert("Exercise deleted successfully.");
                            location.reload();  // Reload the page to update the exercise list
                        } else {
                            alert("Failed to delete exercise.");
                        }
                    },
                    error: function() {
                        alert("An error occurred while deleting the exercise.");
                    }
                });
            }
        }
    </script>
</head>

<body>
    <div class="mainContent">
        <h1><?php echo $titleName; ?></h1>
        <?php if ($scenario): ?>
            <p><strong>Title:</strong> <?php echo $scenario['title']; ?></p>
            <p><strong>Description:</strong> <?php echo $scenario['description']; ?></p>
            <p><strong>Assigned Date:</strong> <?php echo $scenario['assigned_date']; ?></p>
            <p><strong>Due Date:</strong> <?php echo $scenario['due_date']; ?></p>

            <h2>Exercises List</h2>
            <a href="addExercise.php?scenario_id=<?php echo $scenario_id; ?>" class="btn btn-add">Add New Exercise</a>
            <table>
                <thead>
                    <tr>
                        <th>Exercise ID</th>
                        <th>Title</th>
                        <th>Learning Objective 1</th>
                        <th>Learning Objective 2</th>
                        <th>Learning Objective 3</th>
                        <th>Learning Objective 4</th>
                        <th>Duration</th>
                        <th>Difficulty Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($exerciseResult->num_rows > 0): ?>
                        <?php while ($exercise = $exerciseResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $exercise['exercise_id']; ?></td>
                                <td><?php echo $exercise['title']; ?></td>
                                <td><?php echo $exercise['learningObj_1']; ?></td>
                                <td><?php echo $exercise['learningObj_2']; ?></td>
                                <td><?php echo $exercise['learningObj_3']; ?></td>
                                <td><?php echo $exercise['learningObj_4']; ?></td>
                                <td><?php echo $exercise['duration']; ?></td>
                                <td><?php echo $exercise['difficulty_level']; ?></td>
                                <td>
                                    <a href="editExercise.php?exercise_id=<?php echo $exercise['exercise_id']; ?>" class="btn btn-edit">Edit</a>
                                    <button class="btn btn-delete" onclick="deleteExercise('<?php echo $exercise['exercise_id']; ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No exercises found for this scenario.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="scenarioManagement.php" class="btn btn-back">Back to Scenarios</a>
        <?php else: ?>
            <p>Scenario not found.</p>
            <a href="scenarioManagement.php" class="btn btn-back">Back to Scenarios</a>
        <?php endif; ?>
    </div>
</body>
<?php include '../header_footer/footer.php' ?>
</html>
