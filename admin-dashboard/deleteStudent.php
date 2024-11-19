<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../connection.php';

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $studentId = $_GET['id'];

    // Start a transaction to ensure both deletions succeed or fail together
    $conn->begin_transaction();

    try {
        // First, get the associated user_id from the students table
        $query = "SELECT user_id FROM students WHERE id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $stmt->bind_result($user_id);
            $stmt->fetch();
            $stmt->close();
        } else {
            throw new Exception("Error: Could not fetch student details.");
        }

        if (!$user_id) {
            throw new Exception("Student not found.");
        }

        // Delete the student from the students table
        $deleteStudent = "DELETE FROM students WHERE id = ?";
        $stmt = $conn->prepare($deleteStudent);

        if ($stmt) {
            $stmt->bind_param("i", $studentId);
            $stmt->execute();

            if ($stmt->affected_rows <= 0) {
                throw new Exception("Error: Student not found or could not be deleted.");
            }

            $stmt->close();
        } else {
            throw new Exception("Error: Could not prepare the DELETE query for students.");
        }

        // Now delete the user from the users table
        if ($user_id) {
            $deleteUser = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($deleteUser);

            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                if ($stmt->affected_rows <= 0) {
                    throw new Exception("Error: User associated with the student could not be deleted.");
                }

                $stmt->close();
            } else {
                throw new Exception("Error: Could not prepare the DELETE query for users.");
            }
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to the students list page after successful deletion
        header("Location: studentsList.php?success=1");
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();

        // Display the error message
        echo $e->getMessage();
    }
} else {
    echo "Invalid student ID.";
}

$conn->close();

?>
