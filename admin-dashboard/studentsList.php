<?php
$titleName = "Student List - TAR UMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

// Retrieve all active (not archived) students
$stmt = $conn->prepare("SELECT s.student_id, u.username, s.is_archived
                        FROM students s
                        INNER JOIN users u ON s.user_id = u.id
                        WHERE s.is_archived = 0
                        ORDER BY s.student_id");
$stmt->execute();
$students = $stmt->get_result();
$stmt->close();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Students - Active Classes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Students</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            List of Active Students
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Student ID</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if ($students->num_rows > 0): ?>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['student_id']); ?></td>
                                <td><?= htmlspecialchars($student['username']); ?></td>
                                <td>
                                    <a href="editStudent.php?stud_id=<?= htmlspecialchars($student['student_id']); ?>" class="btn btn-primary btn-md">Edit</a>
                                    <a href="archive_Student.php?stud_id=<?= htmlspecialchars($student['student_id']); ?>" class="btn btn-warning btn-md">
                                        Archive
                                    </a>
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

<?php include '../header_footer/footer.php'; ?>
