<?php
include '../connection.php';

if (isset($_POST['exercise_id'])) {
    $exercise_id = $_POST['exercise_id'];

    // Prepare and execute the delete query
    $deleteQuery = "DELETE FROM exercise WHERE exercise_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("s", $exercise_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
