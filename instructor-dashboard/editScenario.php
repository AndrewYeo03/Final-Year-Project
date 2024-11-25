<?php
$titleName = "Edit Scenario";
include '../connection.php';
include '../header_footer/header_instructor.php';

// Fetch the scenario ID from the URL
$scenario_id = $_GET['scenario_id'];

// Fetch the scenario details from the database
$scenarioQuery = "SELECT title, description, instructor_id, assigned_date, due_date FROM scenario WHERE scenario_id = ?";
$stmt = $conn->prepare($scenarioQuery);
$stmt->bind_param("i", $scenario_id);
$stmt->execute();
$result = $stmt->get_result();
$scenario = $result->fetch_assoc();

if (!$scenario) {
    echo "<script>alert('Scenario not found.'); window.location.href='scenarioManagement.php';</script>";
    exit;
}

// Fetch the list of instructors for the dropdown
$instructorQuery = "
    SELECT instructors.id, instructors.instructor_id, users.username 
    FROM instructors 
    JOIN users ON instructors.user_id = users.id
";
$instructorResult = $conn->query($instructorQuery);

// Handle the form submission for updating the scenario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_date = $_POST['assigned_date'];
    $due_date = $_POST['due_date'];
    $instructor_id = $_POST['instructor_id'];

    // Update the scenario in the database
    $updateQuery = "UPDATE scenario SET title = ?, description = ?, instructor_id = ?, assigned_date = ?, due_date = ? WHERE scenario_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssissi", $title, $description, $instructor_id, $assigned_date, $due_date, $scenario_id);

    if ($stmt->execute()) {
        echo "<script>alert('Scenario updated successfully.'); window.location.href='scenarioManagement.php';</script>";
    } else {
        echo "<script>alert('Error updating scenario.'); window.location.href='editScenario.php?scenario_id=$scenario_id';</script>";
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
    <script>
        // JavaScript function to check if Due Date is not earlier than Assigned Date
        function validateDates() {
            var assignedDate = document.getElementById('assigned_date').value;
            var dueDate = document.getElementById('due_date').value;

            // If Due Date is earlier than Assigned Date, alert the user and prevent form submission
            if (new Date(dueDate) < new Date(assignedDate)) {
                alert('Due Date cannot be earlier than Assigned Date.');
                return false;
            }
            return true;
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .mainContent {
            padding: 40px;
            max-width: 900px;
            margin: 50px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .mainContent input,
        .mainContent textarea,
        .mainContent select {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
            color: #333;
        }

        textarea {
            resize: vertical;
        }

        button {
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 12px;
            text-align: center;
            display: inline-block;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 20px;
            font-size: 16px;
            width: 100%;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .mainContent {
                padding: 20px;
            }

            form {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="mainContent">
        <h1><?php echo $titleName; ?></h1>
        <form method="POST" onsubmit="return validateDates()">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($scenario['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($scenario['description']); ?></textarea>

            <label for="instructor_id">Instructor:</label>
            <select id="instructor_id" name="instructor_id" required>
                <option value="">Select Instructor</option>
                <?php while ($instructor = $instructorResult->fetch_assoc()): ?>
                    <option value="<?php echo $instructor['id']; ?>" <?php echo $instructor['id'] == $scenario['instructor_id'] ? 'selected' : ''; ?>>
                        <?php echo $instructor['username']; ?> (<?php echo $instructor['instructor_id']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="assigned_date">Assigned Date:</label>
            <input type="date" id="assigned_date" name="assigned_date" value="<?php echo $scenario['assigned_date']; ?>" required>

            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" value="<?php echo $scenario['due_date']; ?>" required>

            <button type="submit">Update Scenario</button>
        </form>

        <a href="scenarioManagement.php" class="btn-back">Back to Scenario List</a>
    </div>
</body>
<?php include '../header_footer/footer.php' ?>

</html>
