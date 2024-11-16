<?php
$titleName = "Member of Groups - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

// Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("
    SELECT students.student_id, users.username, student_classes.class_name
    FROM students
    INNER JOIN users ON students.user_id = users.id
    INNER JOIN student_classes ON students.id = student_classes.student_id
    WHERE users.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$currentStudent = $result->fetch_assoc(); // Current logged-in student
$class_name = $currentStudent['class_name'];
$stmt->close();

// Retrieve all students in the same class
$stmt = $conn->prepare("
    SELECT students.student_id, users.username
    FROM students
    INNER JOIN users ON students.user_id = users.id
    INNER JOIN student_classes ON students.id = student_classes.student_id
    WHERE student_classes.class_name = ?
    ORDER BY students.student_id
");
$stmt->bind_param("s", $class_name);
$stmt->execute();
$students = $stmt->get_result();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Class Members - <?php echo htmlspecialchars($class_name); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Students</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All <?php echo htmlspecialchars($class_name); ?> Students
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
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php' ?>
