<?php
$titleName = "Edit Scenario";
include '../connection.php';
include '../header_footer/header_instructor.php';

$username = $_SESSION['username'];

$stmt = $conn->prepare("
    SELECT i.id AS id
    FROM instructors i
    INNER JOIN users u ON i.user_id = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$instructorData = $result->fetch_assoc();
$stmt->close();

if (!$instructorData) {
    echo "Error: Instructor data not found.";
    exit();
}

$instructorId = $instructorData['id'];
$scenario_id = $_GET['scenario_id'];

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

$selectedInstructorId = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : $scenario['instructor_id'];

$instructorQuery = "
    SELECT instructors.id, instructors.instructor_id, users.username 
    FROM instructors 
    JOIN users ON instructors.user_id = users.id
";
$instructorResult = $conn->query($instructorQuery);

$stmt = $conn->prepare("
    SELECT c.class_name
    FROM instructor_classes ic
    JOIN class c ON ic.class_name = c.class_name
    WHERE ic.instructor_id = ?
");
$stmt->bind_param("i", $selectedInstructorId);
$stmt->execute();
$classResult = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_date = $_POST['assigned_date'];
    $due_date = $_POST['due_date'];
    $instructor_id = $_POST['instructor_id'];
    $className = $_POST['class_name'];

    $updateQuery = "UPDATE scenario SET title = ?, description = ?, instructor_id = ?, assigned_date = ?, due_date = ? WHERE scenario_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssissi", $title, $description, $instructor_id, $assigned_date, $due_date, $scenario_id);

    if ($stmt->execute()) {
        $stmt = $conn->prepare("INSERT INTO class_scenarios (class_name, scenario_id) VALUES (?, ?)");
        $stmt->bind_param("si", $className, $scenario_id);
        if ($stmt->execute()) {
            echo "<script>alert('Scenario updated and assigned class successfully.'); window.location.href='scenarioManagement.php';</script>";
        } else {
            echo "<script>alert('Error updating scenario.'); window.location.href='editScenario.php?scenario_id=$scenario_id';</script>";
        }
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
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            width: 100%;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
        }

        .btn-back {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
    <script>
        function reloadWithInstructor() {
            const instructorId = document.getElementById('instructor_id').value;
            const url = new URL(window.location.href);
            url.searchParams.set('instructor_id', instructorId);
            window.location.href = url;
        }

        function validateDates() {
            var assignedDate = document.getElementById('assigned_date').value;
            var dueDate = document.getElementById('due_date').value;

            if (new Date(dueDate) < new Date(assignedDate)) {
                alert('Due Date cannot be earlier than Assigned Date.');
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="form-container">
        <h1><?php echo $titleName; ?></h1>
        <form method="POST" onsubmit="return validateDates()">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($scenario['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($scenario['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="instructor_id">Instructor:</label>
                <select id="instructor_id" name="instructor_id" onchange="reloadWithInstructor()" required>
                    <option value="">Select Instructor</option>
                    <?php while ($instructor = $instructorResult->fetch_assoc()): ?>
                        <option value="<?php echo $instructor['id']; ?>" <?php echo $instructor['id'] == $selectedInstructorId ? 'selected' : ''; ?>>
                            <?php echo $instructor['username']; ?> (<?php echo $instructor['instructor_id']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="class_name">Assign Class:</label>
                <select id="class_name" name="class_name" required>
                    <option value="">Select Class</option>
                    <?php while ($class = $classResult->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($class['class_name']); ?>">
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="assigned_date">Assigned Date:</label>
                <input type="date" id="assigned_date" name="assigned_date" value="<?php echo $scenario['assigned_date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date:</label>
                <input type="date" id="due_date" name="due_date" value="<?php echo $scenario['due_date']; ?>" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-submit">Update Scenario</button>
                <a href="scenarioManagement.php" class="btn btn-back">Back to Scenario List</a>
            </div>
        </form>
    </div>
</body>
<?php include '../header_footer/footer.php'; ?>
</html>
