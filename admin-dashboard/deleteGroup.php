<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php';

// Check if the group ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: groupsList.php");
    exit;
}

$id = $_GET['id'];

// Validate that the ID is a numeric value
if (!is_numeric($id)) {
    $_SESSION['error_message'] = "Invalid group ID.";
    header("Location: groupsList.php");
    exit;
}

// Delete the group from the database
$sql = "DELETE FROM class WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Group deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting group. Please try again.";
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = "Database error. Please try again.";
}

// Redirect back to the group list
header("Location: groupsList.php");
exit;
?>
