<?php
$titleName = "Archived Student List - TAR UMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

// 检查用户登录状态
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// 获取存档学生的列表
$stmt = $conn->prepare("
    SELECT s.student_id, u.username 
    FROM students s
    JOIN users u ON s.user_id = u.id
    WHERE s.is_archived = 1
");
$stmt->execute();
$result = $stmt->get_result();
$archivedStudents = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Archived Students</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Archived Students</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-times me-1"></i>
            Archived Students
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($archivedStudents)): ?>
                        <?php foreach ($archivedStudents as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['student_id']); ?></td>
                                <td><?= htmlspecialchars($student['username']); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-success unarchive-student-btn" 
                                        data-student-id="<?= htmlspecialchars($student['student_id']); ?>"
                                        data-student="<?= htmlspecialchars($student['username']); ?>">
                                        Unarchive
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No archived students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("datatablesSimple").addEventListener("click", function (e) {
        if (e.target.classList.contains("unarchive-student-btn")) {
            const studentId = e.target.dataset.studentId;
            const studentName = e.target.dataset.student;

            const confirmUnarchive = confirm(`Are you sure you want to unarchive the student "${studentName}"?`);
            if (!confirmUnarchive) return;

            fetch("unarchive_student.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `studentId=${encodeURIComponent(studentId)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.error) throw new Error(data.error);
                    alert(`Student "${studentName}" has been successfully unarchived.`);
                    location.reload();
                })
                .catch((error) => alert(error.message));
        }
    });
});
</script>

<?php include '../header_footer/footer.php'; ?>
