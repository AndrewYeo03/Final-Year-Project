<?php
$titleName = "Archived Class List - TAR UMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

//Check user login status
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

//Get a list of archived students
$stmt = $conn->prepare("
    SELECT c.class_name 
    FROM class c
    WHERE c.is_archived = 1
");
$stmt->execute();
$result = $stmt->get_result();
$archivedClass = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">All Archived Classes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Archived Classes</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-times me-1"></i>
            Archived classes
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
                    <?php if (!empty($archivedClass)): ?>
                        <?php foreach ($archivedClass as $class): ?>
                            <tr>
                                <td><?= htmlspecialchars($class['class_name']); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-success unarchive-student-btn" 
                                        data-class-name="<?= htmlspecialchars($class['class_name']); ?>">
                                        Unarchive
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No archived classes found.</td>
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
            const className = e.target.dataset.className;

            const confirmUnarchive = confirm(`Are you sure you want to unarchive the student "${className}"?`);
            if (!confirmUnarchive) return;

            fetch("unarchive_class.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `className=${encodeURIComponent(className)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.error) throw new Error(data.error);
                    alert(`Class: "${className}" has been successfully unarchived.`);
                    location.reload();
                })
                .catch((error) => alert(error.message));
        }
    });
});
</script>

<?php include '../header_footer/footer.php'; ?>
