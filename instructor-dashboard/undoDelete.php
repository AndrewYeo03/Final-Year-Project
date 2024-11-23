<?php
include '../connection.php';

// Check if the scenario_id is passed
if (isset($_GET['scenario_id'])) {
    $scenario_id = $_GET['scenario_id'];

    // Restore the scenario by setting deleted_at to NULL
    $query = "UPDATE scenario SET deleted_at = NULL WHERE scenario_id = ? AND deleted_at IS NOT NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $scenario_id);
    
    if ($stmt->execute()) {
        echo "Scenario has been restored successfully.";
    } else {
        echo "Error restoring scenario.";
    }
} else {
    echo "No scenario ID provided.";
}
?>
