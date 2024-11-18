<?php
$titleName = "Evaluate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

// Get the scenario ID from the query string
$scenario_id = $_GET['id'];

// Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("
    SELECT s.id AS student_id
    FROM students s
    INNER JOIN users u ON s.user_id = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();
$stmt->close();

// Retrieve the scenario details
$stmt = $conn->prepare("SELECT * FROM scenario WHERE scenario_id = ?");
$stmt->bind_param("i", $scenario_id);
$stmt->execute();
$scenario = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];

    // Insert the rating into the database
    $stmt = $conn->prepare("INSERT INTO ratings (student_id, scenario_id, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $studentData['id'], $scenario_id, $rating);
    if ($stmt->execute()) {
        echo "<p>Thank you for your rating! Your feedback has been submitted successfully.</p>";
    } else {
        echo "<p>There was an error submitting your rating. Please try again.</p>";
    }
    $stmt->close();
}
?>

<div class="evaluation-container">
    <div class="evaluation-header">
        <h2>Evaluate Scenario - <?php echo htmlspecialchars($scenario['title']); ?></h2>
    </div>
    <div class="evaluation-content">
        <form action="" method="POST">
            <div class="form-group">
                <label for="rating">Rating (1 to 5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-evaluate">Submit Rating</button>
            </div>
        </form>
    </div>
</div>

<?php include '../header_footer/footer.php' ?>
