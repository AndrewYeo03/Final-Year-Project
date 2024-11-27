<?php
$titleName = "Class List - TAR UMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

// Retrieve all active (not archived) students
$stmt = $conn->prepare("
    SELECT *
    FROM class c
    WHERE c.is_archived = 0
");
$stmt->execute();
$classes = $stmt->get_result();
$stmt->close();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">All Active Classes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Exisiting classes</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            List of Active Classes
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Class Name</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if ($classes->num_rows > 0): ?>
                        <?php while ($class = $classes->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($class['class_name']); ?></td>
                                <td>
                                    <a href="editClass.php?class_name=<?= htmlspecialchars($class['class_name']); ?>" class="btn btn-primary btn-md">Edit</a>
                                    <a href="archive_class.php?class_name=<?= htmlspecialchars($class['class_name']); ?>" class="btn btn-warning btn-md">Archive</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No active students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include  '../header_footer/footer.php'; ?>