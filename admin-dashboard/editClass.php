<?php
include '../header_footer/header_admin.php';
include '../connection.php';

// Get the class name from the previous page
$className = $_GET['class_name'];

// Retrieve class infos
$stmt = $conn->prepare("
    SELECT c.class_name, c.description
    FROM class c
    WHERE c.class_name = ?
");
$stmt->bind_param("s", $className);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and update teacher information
    $description = strtoupper($_POST['description']);


    // Update class table
    $update_stmt = $conn->prepare("
        UPDATE class 
        SET description = ? 
        WHERE class_name = ?
    ");
    $update_stmt->bind_param("ss", $description, $class['class_name']);
    $update_stmt->execute();

    $update_success = true; // Mark the update as successful
}
?>

<div class="container-fluid px-4">
    <h2 class="mt-4">Edit Instructor</h2>
    <form method="POST">
        <div class="form-group">
            <label for="className">Class Name:</label>
            <input type="text" class="form-control" id="className" name="className" value="<?php echo $class['class_name']; ?>" readonly disabled>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" class="form-control" id="description" name="description" value="<?php echo $class['description']; ?>" required>
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
        alert('Class information updated successfully!');
        window.location.href = 'classList.php';
    </script>";
}
?>