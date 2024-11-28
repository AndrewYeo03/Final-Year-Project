<?php
// Include necessary files for session and connection handling
include '../connection.php';
session_start();

// Check if scenario_id is passed
if (!isset($_GET['scenario_id'])) {
    die("No scenario ID provided.");
}

$scenarioId = intval($_GET['scenario_id']); // Ensure the ID is an integer

// Navigation logic based on scenario_id
switch ($scenarioId) {
    case 1:
        header("Location: sshAttackAi.php");
        break;
    case 2:
        header("Location: ldapattacka.php");
        break;
    case 3:
        header("Location: testing1.php");
        break;
    case 4:
        header("Location: testing2.php");
        break;
    case 5:
        header("Location: testing3.php");
        break;
    case 6:
        header("Location: exerciseCustomization.php");
        break;
    default:
        header("Location: exerciseCustomization.php");
        break;
}

// Stop further execution after redirection
exit();
