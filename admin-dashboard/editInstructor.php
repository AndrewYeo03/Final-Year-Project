<?php
include '../header_footer/header_admin.php';
include '../connection.php';

// Get the instructor ID from the previous page
$instructor_id = $_GET['id'];

// Retrieve instructor's infos
$stmt = $conn->prepare("
    SELECT i.id, u.username, u.email, i.instructor_id, i.faculty 
    FROM instructors i
    JOIN users u ON i.user_id = u.id
    WHERE i.id = ?
");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();

$update_success = false; // Initialize the update success flag

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and update teacher information
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $faculty = $_POST['faculty'];

    // Update users table
    $update_stmt = $conn->prepare("
        UPDATE users 
        SET username = ?, email = ? 
        WHERE id = (SELECT user_id FROM instructors WHERE id = ?)
    ");
    $update_stmt->bind_param("ssi", $username, $email, $instructor_id);
    $update_stmt->execute();

    // Update instructors table (In future, once cyber range open to more faculty, can enable back the input textfield and use this back-end code to update)
    $update_faculty_stmt = $conn->prepare("
        UPDATE instructors 
        SET faculty = ? 
        WHERE id = ?
    ");
    $update_faculty_stmt->bind_param("si", $faculty, $instructor_id);
    $update_faculty_stmt->execute();

    $update_success = true; // Mark the update as successful
}
?>

<div class="container-fluid px-4">
    <h2 class="mt-4">Edit Instructor</h2>
    <form method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $instructor['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $instructor['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="faculty">Faculty:</label>
            <!-- Make the faculty field disabled (user can't edit) -->
            <input type="text" class="form-control" id="faculty" name="faculty" value="<?php echo $instructor['faculty']; ?>" disabled>
            <small class="form-text text-muted">Faculty information is currently fixed and cannot be edited.</small>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
    </form>
</div>

<?php include '../header_footer/footer.php'; ?>

<?php
// If the update was successful, show the JavaScript alert
if ($update_success) {
    echo "
    <script type='text/javascript'>
        alert('Instructor information updated successfully!');
        window.location.href = 'instructorsList.php';
    </script>";
}
?>