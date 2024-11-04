<?php
// Database connection details
$host = 'localhost';
$dbname = 'cyberrange';
$username = 'admin';
$password = 'tarumtCR@2024';

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: Could not connect. " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flagInput1 = $_POST['flagInput1'];
    $flagInput2 = $_POST['flagInput2'];

    // Fetch the correct flags from the database
    $stmt = $pdo->prepare("SELECT flag_value FROM flag WHERE flag_id IN ('fOA1', 'fOA2') ORDER BY flag_id ASC");
    $stmt->execute();
    $flags = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($flags && count($flags) === 2) {
        // Compare user inputs with database values
        if ($flagInput1 === $flags[0] && $flagInput2 === $flags[1]) {
            echo "<p style='color: green;'>Flags submitted successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error: The flags are incorrect. Please try again.</p>";
        }
    } else {
        echo "<p style='color: red;'>Error: Flags not found in the database.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flag Submission</title>
</head>
<body>
    <div class="flag-container">
        <h2 class="flag-title">Submission of Flag</h2>
        <form method="post" action="">
            <label for="flagInput1">Enter Username:</label>
            <input type="text" name="flagInput1" id="flagInput1" placeholder="Enter username" style="width: 100%; padding: 8px;">
            <label for="flagInput2">Enter Password:</label>
            <input type="text" name="flagInput2" id="flagInput2" placeholder="Enter password" style="width: 100%; padding: 8px;">
            <button type="submit" style="margin-top: 10px; padding: 8px 16px;">Submit</button>
        </form>
    </div>
</body>
</html>
