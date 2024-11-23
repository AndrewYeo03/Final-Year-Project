<?php
include '../connection.php';

// Check if the scenario_id is passed
if (isset($_GET['scenario_id'])) {
    $scenario_id = $_GET['scenario_id'];

    // Perform the permanent delete (remove the record from the table)
    $query = "DELETE FROM scenario WHERE scenario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $scenario_id);

    if ($stmt->execute()) {
        // If deletion is successful, display a success message and redirect
        echo "Scenario permanently deleted.";
        // Redirect after 2 seconds
        header("refresh:2;url=scenarioManagement.php");
        exit();
    } else {
        // If there's an error during deletion
        echo "Error permanently deleting scenario.";
        // Redirect after 2 seconds
        header("refresh:2;url=scenarioManagement.php");
        exit();
    }
} else {
    // If no scenario_id is passed, show error and redirect
    echo "No scenario ID provided.";
    // Redirect after 2 seconds
    header("refresh:1;url=scenarioManagement.php");
    exit();
}
?>
