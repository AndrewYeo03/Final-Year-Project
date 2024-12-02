<?php
session_start();
include '../connection.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Get the ID of the answer to delete from the URL parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $actual_answer_id = intval($_GET['id']); // Sanitize input

    // Prepare the SQL query to delete the answer
    $query = "DELETE FROM actual_answers WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $actual_answer_id);

        if ($stmt->execute()) {
            // Redirect to the answer list page after successful deletion
            header("Location: answerList.php?delete=success");
        } else {
            // Handle error during deletion
            echo "Error deleting answer: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    // Redirect back if 'id' is not provided or not valid
    header("Location: answerList.php?delete=error");
}

$conn->close();
?>
