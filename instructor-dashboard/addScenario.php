<?php
$titleName = "Add New Scenario";
include '../connection.php';
include '../header_footer/header_instructor.php';

// Fetch the list of instructors to populate the instructor dropdown
$instructorQuery = "
    SELECT instructors.id, instructors.instructor_id, users.username 
    FROM instructors 
    JOIN users ON instructors.user_id = users.id
";
$instructorResult = $conn->query($instructorQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_date = $_POST['assigned_date'];
    $due_date = $_POST['due_date'];
    $instructor_id = $_POST['instructor_id'];

    // Insert the new scenario into the database
    $insertQuery = "INSERT INTO scenario (title, description, instructor_id, assigned_date, due_date) 
                    VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssiss", $title, $description, $instructor_id, $assigned_date, $due_date);

    if ($stmt->execute()) {
        echo "<script>alert('Scenario added successfully.'); window.location.href='scenarioManagement.php';</script>";
    } else {
        echo "<script>alert('Error adding scenario.'); window.location.href='addScenario.php';</script>";
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

        input,
        textarea,
        select {
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
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
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
            <input type="text" id="title" name="title" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required></textarea>

            <label for="instructor_id">Instructor:</label>
            <select id="instructor_id" name="instructor_id" required>
                <option value="">Select Instructor</option>
                <?php while ($instructor = $instructorResult->fetch_assoc()): ?>
                    <option value="<?php echo $instructor['id']; ?>">
                        <?php echo $instructor['username']; ?> (<?php echo $instructor['instructor_id']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="assigned_date">Assigned Date:</label>
            <input type="date" id="assigned_date" name="assigned_date" required>

            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" required>

            <button type="submit">Add Scenario</button>
        </form>

        <a href="scenarioManagement.php" class="btn-back">Back to Scenario List</a>
    </div>
</body>
<?php include '../header_footer/footer.php' ?>

</html>
