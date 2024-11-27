<?php
include '../connection.php';

// Get class name from the URL
if (isset($_GET['class_name'])) {
    $className = $_GET['class_name'];

    // Toggle the class's archived status
    $stmt = $conn->prepare("UPDATE class SET is_archived = 1 WHERE class_name = ?");
    $stmt->bind_param("s", $className);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('$className archive status has been updated.');
                window.location.href = 'classList.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>