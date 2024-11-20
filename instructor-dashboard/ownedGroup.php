<?php
$titleName = "Owned Group - TARUMT Cyber Range";
include  '../header_footer/header_instructor.php';
include '../connection.php';

// Retrieve instructor information
$username = $_SESSION['username'];
$stmt = $conn->prepare("
    SELECT i.id AS id
    FROM instructors i
    INNER JOIN users u ON i.user_id = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$instructorData = $result->fetch_assoc();
$stmt->close();

// Get instructor ID
$instructorId = $instructorData['id'];

//Retrieve the Classes belonging to the current Instructor
$allClasses = [];
$stmt = $conn->prepare("
    SELECT ic.class_name 
    FROM instructor_classes ic
    JOIN class c ON ic.class_name = c.class_name
    WHERE ic.instructor_id = ?
    ");
$stmt->bind_param("i", $instructorId);
$stmt->execute();
$result = $stmt->get_result();
$allClasses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Classes & Students</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Classes</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Classes
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allClasses as $class): ?>
                        <tr>
                            <td><?= htmlspecialchars($class['class_name']); ?></td>
                            <td>
                                <button
                                    class="btn btn-primary manage-students-btn"
                                    data-class="<?= htmlspecialchars($class['class_name']); ?>">
                                    Manage Students
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Box / Pop-Up Box -->
<div class="modal fade" id="manageStudentsModal" tabindex="-1" aria-labelledby="manageStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageStudentsModalLabel">Manage Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- All students table -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Dynamically loading content -->
                    </tbody>
                </table>
                <!-- Add Student Form -->
                <form id="addStudentForm" class="mt-3">
                    <div class="mb-3">
                        <label for="studentId" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="studentId" name="studentId" required>
                        <input type="hidden" id="className" name="className">
                    </div>
                    <button type="submit" class="btn btn-success">Add Student</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="ownedGroup.js"></script>
<?php include '../header_footer/footer.php' ?>