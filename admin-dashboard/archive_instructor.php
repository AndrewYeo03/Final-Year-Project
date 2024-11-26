<?php
include '../connection.php';

//Get lecturer id
if (isset($_GET['id'])) {
    $instructor_id = $_GET['id'];

    //Update the is_archived field for instructors
    $stmt = $conn->prepare("UPDATE instructors SET is_archived = 1 WHERE id = ?");
    $stmt->bind_param("i", $instructor_id);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Instructor has been archived successfully.');
                window.location.href = 'instructorsList.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
