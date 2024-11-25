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

        .scenario-details-card {
            max-width: 800px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }

        .title {
            text-align: center;
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .card-header {
            background-color: #f4f4f4;
            border-bottom: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: #007bff;
        }

        .card-body {
            padding: 15px;
            line-height: 1.6;
            color: #555;
        }

        .card-body p {
            margin: 10px 0;
        }

        .card-body strong {
            color: #333;
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

        .page-title {
            font-size: 3rem;
            /* Increased font size */
            font-weight: bold;
            margin-bottom: 10px;
            padding: 10px 15px;
        }

        .page-title span {
            display: inline-block;
            color: #000000;
            border-bottom: 2px solid #000000;
            padding-bottom: 5px;
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
        <div class="scenario-details-card">
            <h1 class="title"><?php echo $titleName; ?></h1>
            <?php if ($scenario): ?>
                <div class="card">
                    <div class="card-header">
                        <h2>Scenario Details</h2>
                    </div>
                    <div class="card-body">
                        <p><strong>Title:</strong> <?php echo $scenario['title']; ?></p>
                        <p><strong>Description:</strong> <?php echo $scenario['description']; ?></p>
                        <p><strong>Assigned Date:</strong> <?php echo date("d M Y", strtotime($scenario['assigned_date'])); ?></p>
                        <p><strong>Due Date:</strong> <?php echo date("d M Y", strtotime($scenario['due_date'])); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <p>Scenario not found.</p>
            <?php endif; ?>
        </div>

        <div class="page-title">
            <span><?php echo $titleName; ?></span>
        </div>
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
    </div>
</body>
<?php include '../header_footer/footer.php' ?>
</html>
