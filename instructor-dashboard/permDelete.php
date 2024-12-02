<?php
include '../connection.php';

// Check if the scenario_id is passed
if (isset($_GET['scenario_id'])) {
    $scenario_id = $_GET['scenario_id'];

    // Delete dependent rows in the exercise table
    $deleteExercisesQuery = "DELETE FROM exercise WHERE scenario_id = ?";
    $stmt = $conn->prepare($deleteExercisesQuery);
    $stmt->bind_param("i", $scenario_id);
    $stmt->execute();

    // Delete the scenario after dependent rows are removed
    $deleteScenarioQuery = "DELETE FROM scenario WHERE scenario_id = ?";
    $stmt = $conn->prepare($deleteScenarioQuery);
    $stmt->bind_param("i", $scenario_id);

    if ($stmt->execute()) {
        echo "Scenario permanently deleted.";
        header("refresh:2;url=scenarioManagement.php");
    } else {
        echo "Error permanently deleting scenario.";
        header("refresh:2;url=scenarioManagement.php");
    }
} else {
    echo "No scenario ID provided.";
    header("refresh:1;url=scenarioManagement.php");
    exit();
}
?>
