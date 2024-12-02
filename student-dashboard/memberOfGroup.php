<?php
$titleName = "Member of Groups - TARUMT Cyber Range";
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
$currentStudent = $result->fetch_assoc();
$stmt->close();

// Store all classes the student is part of
$stmt = $conn->prepare("
    SELECT DISTINCT class_name
    FROM student_classes
    WHERE student_id = ?
");
$stmt->bind_param("i", $currentStudent['student_id']);
$stmt->execute();
$classResult = $stmt->get_result();
$classes = $classResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Determine the current class (default to the first class if not set)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_class'])) {
    $_SESSION['current_class'] = $_POST['selected_class'];
} elseif (!isset($_SESSION['current_class']) && count($classes) > 0) {
    $_SESSION['current_class'] = $classes[0]['class_name'];
}

// Get the currently selected class
$currentClass = $_SESSION['current_class'] ?? null;

// Retrieve all students in the selected class
if ($currentClass) {
    $stmt = $conn->prepare("
        SELECT students.student_id, users.username
        FROM students
        INNER JOIN users ON students.user_id = users.id
        INNER JOIN student_classes ON students.id = student_classes.student_id
        WHERE student_classes.class_name = ?
        ORDER BY students.student_id
    ");
    $stmt->bind_param("s", $currentClass);
    $stmt->execute();
    $students = $stmt->get_result();
}

// Handle adding a student to a class based on class code input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_code'])) {
    $classCode = $_POST['class_code'];

    // Check if the class code exists in the class table
    $checkClassQuery = "SELECT * FROM class WHERE class_code = ?";
    $stmt = $conn->prepare($checkClassQuery);
    $stmt->bind_param("s", $classCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Class code is valid, insert the student into the student_classes table
        $className = $result->fetch_assoc()['class_name'];
        $stmt = $conn->prepare("INSERT INTO student_classes (student_id, class_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $currentStudent['student_id'], $className);

        if ($stmt->execute()) {
            echo "<script>alert('Successfully joined the class!');</script>";
        } else {
            echo "<script>alert('Error: Unable to join the class.');</script>";
        }
    } else {
        echo "<script>alert('Invalid class code.');</script>";
    }
    $stmt->close();
}
?>

<style>
    .custom-dropdown {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
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
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Class Members - <?= htmlspecialchars($currentClass); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Students</li>
    </ol>

    <!-- Input field to join a class -->
    <form method="POST" class="mb-4">
        <div class="mb-3 row">
            <label for="class-code" class="form-label col-sm-2">Enter Class Code:</label>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-8">
                <input type="text" id="class-code" name="class_code" class="form-control" placeholder="Enter class code to join">
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary w-50">Join Class</button>
            </div>
        </div>
    </form>

    <!-- Dropdown to switch classes -->
    <form method="POST" class="mb-4">
        <div class="custom-dropdown">
            <select id="class-select" name="selected_class" class="form-select" onchange="this.form.submit()">
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class['class_name']); ?>"
                        <?= $class['class_name'] === $currentClass ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($class['class_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All <?= htmlspecialchars($currentClass); ?> Students
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Student ID</th>
                        <th>Username</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if ($students): ?>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['student_id']); ?></td>
                                <td><?= htmlspecialchars($student['username']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php'; ?>