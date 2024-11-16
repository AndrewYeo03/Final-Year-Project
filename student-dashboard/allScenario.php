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

// If student data exists, retrieve assigned scenarios
$scenarios = [];
if ($studentData) {
    $studentId = $studentData['student_id'];

    // Query the classes the student belongs to
    $stmt = $conn->prepare("
    SELECT sc.class_name
    FROM student_classes scs
    INNER JOIN class sc ON scs.class_name = sc.class_name
    WHERE scs.student_id = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // For each class the student belongs to, get the scenarios
    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row['class_name'];
    }
    $stmt->close();

    // Retrieve all scenarios assigned to the student's classes
    if (!empty($classes)) {
        $classNames = implode("','", $classes);  // Create a comma-separated list of class names
        $stmt = $conn->prepare("
        SELECT sc.title AS scenario_title, sc.scenario_id, sc.assigned_date, sc.due_date, u.username AS instructor_name
        FROM scenario sc
        INNER JOIN class_scenarios cs ON sc.scenario_id = cs.scenario_id
        INNER JOIN class c ON cs.class_name = c.class_name
        INNER JOIN instructors i ON sc.instructor_id = i.id
        INNER JOIN users u ON i.user_id = u.id
        WHERE cs.class_name IN ('$classNames')
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $scenarios = [];
        while ($row = $result->fetch_assoc()) {
            $scenarios[] = $row;
        }
        $stmt->close();
    }
}
?>

<style>
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
        <li class="breadcrumb-item active">All Scenario</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Assigned Scenario
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Scenario Name</th>
                        <th>Instructor Name</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Scenario Name</th>
                        <th>Instructor Name</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($scenarios as $scenario): ?>
                        <tr>
                            <td><?= htmlspecialchars($scenario['scenario_title']); ?></td>
                            <td><?= htmlspecialchars($scenario['instructor_name']); ?></td>
                            <td><?= htmlspecialchars($scenario['assigned_date']); ?></td>
                            <td><?= htmlspecialchars($scenario['due_date']); ?></td>
                            <td>
                                <a href="startScenario.php?scenario_id=<?= $scenario['scenario_id']; ?>" class="blue">
                                    Start Scenario
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php'; ?>
