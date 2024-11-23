<?php
$titleName = "Archived Classes - TARUMT Cyber Range";
include '../header_footer/header_instructor.php';
include '../connection.php';

// 检查用户登录状态
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// 获取讲师的所有存档课程
$username = $_SESSION['username'];
$stmt = $conn->prepare("
    SELECT ic.class_name 
    FROM instructor_classes ic
    JOIN class c ON ic.class_name = c.class_name
    WHERE c.is_archived = 1
");
$stmt->execute();
$result = $stmt->get_result();
$archivedClasses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Archived Classes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Archived Classes</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-archive me-1"></i>
            Archived Classes
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($archivedClasses)): ?>
                        <?php foreach ($archivedClasses as $class): ?>
                            <tr>
                                <td><?= htmlspecialchars($class['class_name']); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-success unarchive-class-btn" 
                                        data-class="<?= htmlspecialchars($class['class_name']); ?>">
                                        Unarchive
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No archived classes found.</td>
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
        if (e.target.classList.contains("unarchive-class-btn")) {
            const className = e.target.dataset.class;

            const confirmUnarchive = confirm(`Are you sure you want to unarchive the class "${className}"?`);
            if (!confirmUnarchive) return;

            fetch("unarchive_class.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `className=${encodeURIComponent(className)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.error) throw new Error(data.error);
                    alert(`Class "${className}" has been successfully unarchived.`);
                    location.reload();
                })
                .catch((error) => alert(error.message));
        }
    });
});
</script>

<?php include '../header_footer/footer.php'; ?>
