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
    $ratings = $_POST['ratings']; // Array with ratings for all parts

    // Prepare a single SQL statement to insert multiple ratings
    $stmt = $conn->prepare("INSERT INTO ratings (student_id, scenario_id, part, rating) VALUES (?, ?, ?, ?)");

    foreach ($ratings as $part => $rating) {
        $stmt->bind_param("iiii", $studentData['student_id'], $scenario_id, $part, $rating);
        $stmt->execute();
    }
    $stmt->close();
    echo "<p class='success-msg'>Thank you! Your feedback has been submitted successfully.</p>";
}
?>

<style>
    .evaluation-container {
        max-width: 800px;
        margin: auto;
        margin-bottom: 25px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .evaluation-header {
        text-align: center;
        margin-bottom: 20px;
    }

    h2 {
        color: #333;
        font-size: 24px;
    }

    .scenario-title {
        background-color: #f8f9fa;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .scenario-title h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
        font-weight: bold;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        /* Make the text bold */
    }

    .rating-options {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .rating-options label {
        display: inline-block;
        margin-right: 10px;
        padding: 5px;
        text-align: center;
    }

    .rating-options input[type="radio"] {
        margin: 0 5px;
    }

    .btn-evaluate {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-evaluate:hover {
        background-color: #0056b3;
    }

    .success-msg {
        color: green;
        font-weight: bold;
        text-align: center;
    }
</style>


<div class="evaluation-container">
    <div class="evaluation-header">
        <h2>Evaluate Scenario - <?php echo htmlspecialchars($scenario['title']); ?></h2>
    </div>

    <form action="" method="POST">
        <!-- Part A -->
        <div class="scenario-title">
            <h3>Part A: Evaluate the scenario content and objectives</h3>
        </div>
        <div class="form-group">
            <?php for ($i = 1; $i <= 3; $i++) {
                $questionTitles = [
                    1 => "Clarity of Scenario Objectives",
                    2 => "Relevance of Scenario Content",
                    3 => "Scenario Complexity Appropriateness"
                ];
            ?>
                <label>
                    Question <?php echo $i; ?>: <?php echo $questionTitles[$i]; ?>
                </label>
                <div class="rating-options">
                    <?php for ($j = 1; $j <= 10; $j++) { ?>
                        <label>
                            <input type="radio" name="ratings[1][<?php echo $i; ?>]" value="<?php echo $j; ?>" required>
                            <?php echo $j; ?>
                        </label>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- Part B -->
        <div class="scenario-title">
            <h3>Part B: Provide feedback on the instructor's guidance</h3>
        </div>
        <div class="form-group">
            <?php for ($i = 1; $i <= 3; $i++) {
                $questionTitles = [
                    1 => "Instructor's Clarity in Explaining Concepts",
                    2 => "Effectiveness of Guidance Provided",
                    3 => "Instructor's Responsiveness to Questions"
                ];
            ?>
                <label>
                    Question <?php echo $i; ?>: <?php echo $questionTitles[$i]; ?>
                </label>
                <div class="rating-options">
                    <?php for ($j = 1; $j <= 10; $j++) { ?>
                        <label>
                            <input type="radio" name="ratings[2][<?php echo $i; ?>]" value="<?php echo $j; ?>" required>
                            <?php echo $j; ?>
                        </label>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- Part C -->
        <div class="scenario-title">
            <h3>Part C: Rate the overall experience and facilities</h3>
        </div>
        <div class="form-group">
            <?php for ($i = 1; $i <= 3; $i++) {
                $questionTitles = [
                    1 => "Quality of Provided Facilities",
                    2 => "Overall Experience Enjoyability",
                    3 => "Adequacy of Resources Provided"
                ];
            ?>
                <label>
                    Question <?php echo $i; ?>: <?php echo $questionTitles[$i]; ?>
                </label>
                <div class="rating-options">
                    <?php for ($j = 1; $j <= 10; $j++) { ?>
                        <label>
                            <input type="radio" name="ratings[3][<?php echo $i; ?>]" value="<?php echo $j; ?>" required>
                            <?php echo $j; ?>
                        </label>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <div class="form-group">
            <button type="submit" class="btn-evaluate">Submit Feedback</button>
        </div>
    </form>
</div>

<?php include '../header_footer/footer.php'; ?>