<?php
include '../connection.php';

// Check if instructor_id is provided
if (isset($_POST['instructor_id'])) {
    $instructor_id = $_POST['instructor_id'];

    // Query to get classes assigned to the instructor
    $query = "SELECT class_name FROM classes WHERE instructor_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output the classes as options for the select dropdown
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['class_name'] . "'>" . $row['class_name'] . "</option>";
        }
    } else {
        echo "<option value=''>No classes available</option>";
    }
}
?>
