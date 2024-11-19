<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php';

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $instructorId = $_GET['id'];

    // Start a transaction to ensure both deletions succeed or fail together
    $conn->begin_transaction();

    try {
        // First, get the associated username or user_id from the instructors table
        $query = "SELECT username FROM instructors WHERE id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $instructorId);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();
        } else {
            throw new Exception("Error: Could not fetch instructor details.");
        }

        if (!$username) {
            throw new Exception("Instructor not found.");
        }

        // Delete from the instructors table
        $deleteInstructor = "DELETE FROM instructors WHERE id = ?";
        $stmt = $conn->prepare($deleteInstructor);

        if ($stmt) {
            $stmt->bind_param("i", $instructorId);
            $stmt->execute();

            if ($stmt->affected_rows <= 0) {
                throw new Exception("Error: Instructor not found or could not be deleted.");
            }

            $stmt->close();
        } else {
            throw new Exception("Error: Could not prepare the DELETE query for instructors.");
        }

        // Delete associated user information from the users table
        $deleteUser = "DELETE FROM users WHERE username = ?";
        $stmt = $conn->prepare($deleteUser);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();

            if ($stmt->affected_rows <= 0) {
                throw new Exception("Error: User associated with the instructor could not be deleted.");
            }

            $stmt->close();
        } else {
            throw new Exception("Error: Could not prepare the DELETE query for users.");
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to the instructor list page after successful deletion
        header("Location: instructorsList.php?success=1");
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();

        // Display the error message
        echo $e->getMessage();
    }
} else {
    echo "Invalid instructor ID.";
}

$conn->close();
?>
