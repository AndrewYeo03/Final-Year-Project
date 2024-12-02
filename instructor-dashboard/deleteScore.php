<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Check if the scenario_id is provided via GET
if (!isset($_GET['scenario_id']) || empty($_GET['scenario_id'])) {
    die("Error: Scenario ID is required.");
}

include '../connection.php';

$scenario_id = $_GET['scenario_id'];

// Ensure the user has permission to delete the score (optional, depending on your setup)
//Check if the user role is Instructor
if ($_SESSION['role_id'] != 2) {
    header("Location: ../unauthorized.php");
    exit();
}

// Delete all scoring criteria for the given scenario_id
$query = "DELETE FROM scoring_criteria WHERE scenario_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $scenario_id);

if ($stmt->execute()) {
    // Redirect with success message
    header("Location: scoringResult.php?msg=Scores deleted successfully.");
} else {
    // Error handling
    die("Error deleting scores: " . $conn->error);
}

// Close the prepared statement and connection
$stmt->close();
$conn->close();
?>
