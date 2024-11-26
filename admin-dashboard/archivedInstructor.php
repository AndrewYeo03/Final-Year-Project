<?php
$titleName = "Archived Instructor List - TAR UMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

// 检查用户登录状态
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// 获取存档讲师的列表
$stmt = $conn->prepare("
    SELECT i.id, u.username 
    FROM instructors i
    JOIN users u ON i.user_id = u.id
    WHERE i.is_archived = 1
");
$stmt->execute();
$result = $stmt->get_result();
$archivedInstructors = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Archived Instructors</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Archived Instructors</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-times me-1"></i>
            Archived Instructors
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Instructor Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($archivedInstructors)): ?>
                        <?php foreach ($archivedInstructors as $instructor): ?>
                            <tr>
                                <td><?= htmlspecialchars($instructor['username']); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-success unarchive-instructor-btn" 
                                        data-instructor-id="<?= htmlspecialchars($instructor['id']); ?>"
                                        data-instructor="<?= htmlspecialchars($instructor['username']); ?>">
                                        Unarchive
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No archived instructors found.</td>
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
        if (e.target.classList.contains("unarchive-instructor-btn")) {
            const instructorId = e.target.dataset.instructorId;
            const instructorName = e.target.dataset.instructor;

            const confirmUnarchive = confirm(`Are you sure you want to unarchive the instructor "${instructorName}"?`);
            if (!confirmUnarchive) return;

            fetch("unarchive_instructor.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `instructorId=${encodeURIComponent(instructorId)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.error) throw new Error(data.error);
                    alert(`Instructor "${instructorName}" has been successfully unarchived.`);
                    location.reload();
                })
                .catch((error) => alert(error.message));
        }
    });
});
</script>

<?php include '../header_footer/footer.php'; ?>
