<?php
$titleName = "Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

// Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("
    SELECT s.id AS student_id
    FROM students s
    INNER JOIN users u ON s.user_id = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();
$stmt->close();

// Retrieve classes and scenarios based on selected class
$classes = [];
$scenarios = [];

// Query classes the student belongs to
if ($studentData) {
    $studentId = $studentData['student_id'];

    $stmt = $conn->prepare("
        SELECT sc.class_name
        FROM student_classes scs
        INNER JOIN class sc ON scs.class_name = sc.class_name
        WHERE scs.student_id = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $classes[] = $row['class_name'];
    }
    $stmt->close();

    // Determine the current class from POST or default to 'all'
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_class'])) {
        $selectedClass = $_POST['selected_class'];
    } else {
        $selectedClass = 'all';
    }

    // Retrieve scenarios for the selected class
    if (!empty($classes)) {
        $classNames = implode("','", $classes);
        $query = "
            SELECT sc.title AS scenario_title, sc.scenario_id, sc.assigned_date, sc.due_date, u.username AS instructor_name, cs.class_name
            FROM scenario sc
            INNER JOIN class_scenarios cs ON sc.scenario_id = cs.scenario_id
            INNER JOIN class c ON cs.class_name = c.class_name
            INNER JOIN instructors i ON sc.instructor_id = i.id
            INNER JOIN users u ON i.user_id = u.id
        ";
        if ($selectedClass !== 'all') {
            $query .= " WHERE cs.class_name = ?";
        } else {
            $query .= " WHERE cs.class_name IN ('$classNames')";
        }

        $stmt = $conn->prepare($query);
        if ($selectedClass !== 'all') {
            $stmt->bind_param("s", $selectedClass);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $scenarios[] = $row;
        }
        $stmt->close();
    }
}
?>

<style>
    .dropdown-container {
        margin-bottom: 20px;
        display: flex;
    }

    .custom-dropdown {
        position: relative;
        width: 100%;
    }

    .custom-dropdown select {
        width: 100%;
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 8px;
        background-color: #f9f9f9;
        font-size: 16px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
    }

    .custom-dropdown select:focus {
        border-color: #007bff;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    .custom-dropdown::after {
        content: '▼';
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 14px;
        color: #333;
    }

    .blue {
        background-color: blue;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .blue:hover {
        background-color: green;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Scenario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">All Scenarios</li>
    </ol>
    <div class="dropdown-container">
        <form method="POST" class="custom-dropdown">
            <select id="classDropdown" name="selected_class" onchange="this.form.submit()">
                <option value="all" <?= $selectedClass === 'all' ? 'selected' : '' ?>>All Classes</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class); ?>" <?= $selectedClass === $class ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Assigned Scenarios
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Scenario Name</th>
                        <th>Class</th>
                        <th>Instructor Name</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Scenario Name</th>
                        <th>Class</th>
                        <th>Instructor Name</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($scenarios as $scenario): ?>
                        <?php
                        // Convert due_date to a timestamp and get the current time
                        $dueDate = strtotime($scenario['due_date']);
                        $currentDate = time();
                        $isDueDatePassed = $dueDate < $currentDate;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($scenario['scenario_title']); ?></td>
                            <td><?= htmlspecialchars($scenario['class_name']); ?></td>
                            <td><?= htmlspecialchars($scenario['instructor_name']); ?></td>
                            <td><?= htmlspecialchars($scenario['assigned_date']); ?></td>
                            <td><?= htmlspecialchars($scenario['due_date']); ?></td>
                            <td>
                                <button
                                    onclick="window.location.href='startScenario.php?scenario_id=<?= $scenario['scenario_id']; ?>'"
                                    <?= $isDueDatePassed ? 'disabled' : ''; ?>
                                    style="<?= $isDueDatePassed ? 'color: gray; cursor: not-allowed;' : ''; ?> " class="blue">
                                    Start Scenario
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php'; ?>