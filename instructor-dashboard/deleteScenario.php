<?php
include '../connection.php';

// Check if scenario_id is passed
if (isset($_GET['scenario_id'])) {
    $scenario_id = $_GET['scenario_id'];

    // Mark the scenario as deleted (soft delete) in the database
    $stmt = $conn->prepare("UPDATE scenario SET deleted_at = NOW() WHERE scenario_id = ?");
    $stmt->bind_param("i", $scenario_id);
    $stmt->execute();
    $stmt->close();
} else {
    // If no scenario_id, redirect to scenario management page
    header("Location: scenarioManagement.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario Delete</title>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
            margin: 20px 0;
        }

        .btn {
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 45%;
            margin: 5px;
            display: inline-block;
            text-align: center;
        }

        .btn-undo {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn:hover {
            opacity: 0.8;
        }

        #buttons-container {
            display: none;
            margin-top: 20px;
        }

        .message {
            font-size: 16px;
            margin-bottom: 15px;
        }

    </style>
</head>

<body>
    <div class="container">
        <h2>Scenario Deleted</h2>
        <p class="message">You have successfully deleted this scenario. You still can undo or permanently delete it.</p>

        <!-- Undo and Permanent Delete buttons container -->
        <div id="buttons-container">
            <button id="undoBtn" class="btn btn-undo" onclick="undoDelete()">Undo Delete</button>
            <button id="permDeleteBtn" class="btn btn-delete" onclick="permDelete()">Delete Permanently</button>
        </div>
    </div>

    <script>
        // Simulating the delete delay
        setTimeout(function () {
            document.getElementById("buttons-container").style.display = "block"; // Show the buttons after 1 second
        }, 1000); // Buttons appear after 1 second

        // Function to undo the delete
        function undoDelete() {
            alert("Undoing delete action... The scenario is restored.");
            window.location.href = 'scenarioManagement.php'; // Redirect to scenario list page
        }

        // Function to permanently delete the scenario
        function permDelete() {
            if (confirm("Are you sure you want to permanently delete this scenario? This action cannot be undone anymore.")) {
                // Perform permanent delete action (actual deletion from database)
                window.location.href = 'permDelete.php?scenario_id=<?php echo $scenario_id; ?>';
            }
        }
    </script>
</body>

</html>
