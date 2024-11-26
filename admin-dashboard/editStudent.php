<?php
include '../header_footer/header_admin.php';
include '../connection.php';

// Step 1: 验证并获取传递的 student_id
if (isset($_GET['stud_id']) && !empty($_GET['stud_id'])) {
    $original_student_id = $_GET['stud_id'];
} else {
    echo "Invalid or missing student ID.";
    exit;
}

// Step 2: 查询学生信息
$stmt = $conn->prepare("
    SELECT s.id AS student_record_id, u.id AS user_id, u.username, u.email, s.student_id 
    FROM students s 
    INNER JOIN users u ON s.user_id = u.id 
    WHERE s.student_id = ?
");
$stmt->bind_param("s", $original_student_id); // 使用字符串类型绑定参数
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// 检查是否找到该学生
if (!$student) {
    echo "No student information found for student_id = " . htmlspecialchars($original_student_id) . ".";
    exit;
}

// 初始化更新标志
$update_success = false;

// Step 3: 处理表单提交，更新学生信息
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $new_student_id = strtoupper($_POST['student_id']);

    // 验证邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // 检查新 student_id 是否已存在（防止重复）
    if ($new_student_id !== $original_student_id) {
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE student_id = ?");
        $check_stmt->bind_param("s", $new_student_id);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            echo "The student ID '$new_student_id' is already in use.";
            exit;
        }
    }

    // 更新用户和学生信息
    $conn->begin_transaction();
    try {
        // 更新用户信息
        $update_user_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $update_user_stmt->bind_param("ssi", $username, $email, $student['user_id']);
        $update_user_stmt->execute();
        $update_user_stmt->close();

        // 更新学生信息
        $update_student_stmt = $conn->prepare("UPDATE students SET student_id = ? WHERE id = ?");
        $update_student_stmt->bind_param("si", $new_student_id, $student['student_record_id']);
        $update_student_stmt->execute();
        $update_student_stmt->close();

        // 提交事务
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
// Step 4: 更新成功后显示提示并重定向
if ($update_success) {
    echo "
    <script type='text/javascript'>
        alert('Student information updated successfully!');
        window.location.href = 'studentsList.php'; // 重定向到学生列表
    </script>";
}
?>
