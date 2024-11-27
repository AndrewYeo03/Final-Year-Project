<?php
include '../header_footer/header_admin.php';
include '../connection.php';

// Step 1: Verify and get the passed student_id
if (isset($_GET['stud_id']) && !empty($_GET['stud_id'])) {
    $original_student_id = $_GET['stud_id'];
} else {
    echo "Invalid or missing student ID.";
    exit;
}

// Step 2: Query student information
$stmt = $conn->prepare("
    SELECT s.id AS student_record_id, u.id AS user_id, u.username, u.email, s.student_id 
    FROM students s 
    INNER JOIN users u ON s.user_id = u.id 
    WHERE s.student_id = ?
");
$stmt->bind_param("s", $original_student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    echo "No student information found for student_id = " . htmlspecialchars($original_student_id) . ".";
    exit;
}

//Initialize update flag
$update_success = false;

// Step 3: Process form submission and update student information
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $new_student_id = strtoupper($_POST['student_id']);

    //Verify email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>Invalid email format.</script>";
        exit;
    }

    //Check if the new student_id already exists (to prevent duplication)）
    if ($new_student_id !== $original_student_id) {
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE student_id = ?");
        $check_stmt->bind_param("s", $new_student_id);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            echo "<script>alert('The student ID \"$new_student_id\" is already in use.');</script>";
            exit;
        }
    }

    //Update user and student table
    $conn->begin_transaction();
    try {
        //Update user table
        $update_user_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $update_user_stmt->bind_param("ssi", $username, $email, $student['user_id']);
        $update_user_stmt->execute();
        $update_user_stmt->close();

        //Update student table
        $update_student_stmt = $conn->prepare("UPDATE students SET student_id = ? WHERE id = ?");
        $update_student_stmt->bind_param("si", $new_student_id, $student['student_record_id']);
        $update_student_stmt->execute();
        $update_student_stmt->close();

        //Committing a transaction
        $conn->commit();
        $update_success = true;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error updating student information: " . $e->getMessage();
        exit;
    }
}
?>

<div class="container-fluid px-4">
    <h2 class="mt-4">Edit Student</h2>
    <form method="POST">
        <div class="form-group">
            <label for="student_id">Student ID:</label>
            <input type="text" class="form-control" id="student_id" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>" required>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($student['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
    </form>
</div>

<?php include '../header_footer/footer.php'; ?>

<?php
// Step 4: After the update is successful, a prompt will be displayed and redirected
if ($update_success) {
    echo "
    <script type='text/javascript'>
        alert('Student information updated successfully!');
        window.location.href = 'studentsList.php';
    </script>";
}
?>
