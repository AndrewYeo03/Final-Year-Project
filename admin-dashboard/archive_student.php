<?php
include '../connection.php';

// Get student ID from the URL
if (isset($_GET['stud_id'])) {
    $student_id = $_GET['stud_id'];

    // Toggle the student's archived status
    $stmt = $conn->prepare("UPDATE students SET is_archived = 1 WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Student archive status has been updated.');
                window.location.href = 'studentsList.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>