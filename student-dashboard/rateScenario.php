<?php
$titleName = "Rate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

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

if (!$studentData || !isset($studentData['student_id'])) {
    echo "<p>Unable to retrieve student information. Please contact the administrator.</p>";
    include '../header_footer/footer.php';
    exit;
}

$studentId = $studentData['student_id'];
?>

<style>
    .evaluation-container {
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin: 20px auto;
        width: 80%;
    }

    .evaluation-header {
        background-color: #e8f5fe;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px;
    }

    .evaluation-header h2 {
        font-size: 24px;
        color: #0073e6;
    }

    .evaluation-header p {
        font-size: 16px;
        color: #333;
    }

    .evaluation-content {
        display: flex;
        gap: 20px;
    }

    .evaluation-instructions {
        flex: 1;
        padding: 10px;
        border-right: 1px solid #ddd;
    }

    .evaluation-instructions ul {
        list-style-type: none;
        padding: 0;
    }

    .evaluation-instructions li {
        margin-bottom: 10px;
    }

    .evaluation-list {
        flex: 2;
        padding: 10px;
    }

    .evaluation-list ul {
        list-style-type: none;
        padding: 0;
    }

    .evaluation-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
    }

    .scenario-item span {
        font-size: 16px;
        color: #333;
    }

    .btn-evaluate {
        padding: 5px 10px;
        background-color: #28a745;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
    }

    .btn-evaluate:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }

    .btn-evaluate:hover:not(:disabled) {
        background-color: #218838;
    }
</style>


<div class="evaluation-container">
    <div class="evaluation-header">
        <h2>Scenario Evaluation 2024</h2>
        <p>
            Dear Students,<br>
            Please provide your feedback for each scenario. Your responses are crucial for improving the learning experience.
            Select a scenario from the list below to proceed with the evaluation.
        </p>
    </div>
    <div class="evaluation-content">
        <div class="evaluation-instructions">
            <h3>How the Evaluation Works:</h3>
            <ul>
                <li><b>Part A:</b> Evaluate the scenario content and objectives.</li>
                <li><b>Part B:</b> Provide feedback on the instructor's guidance.</li>
                <li><b>Part C:</b> Rate the overall experience and facilities.</li>
            </ul>
        </div>
        <div class="evaluation-list">
            <h3>Please select a scenario to evaluate:</h3>
            <ul>
                <?php
                // Retrieve all scenarios available for the student's class
                $stmt = $conn->prepare("
                    SELECT DISTINCT scenario.scenario_id, scenario.title, 
                        (SELECT COUNT(*) FROM ratings WHERE ratings.student_id = ? AND ratings.scenario_id = scenario.scenario_id) AS rated
                    FROM scenario
                    INNER JOIN class_scenarios ON class_scenarios.scenario_id = scenario.scenario_id
                    INNER JOIN student_classes ON student_classes.class_name = class_scenarios.class_name
                    WHERE student_classes.student_id = ?
                ");
                $stmt->bind_param("ii", $studentId, $studentId);
                $stmt->execute();
                $scenarios = $stmt->get_result();

                if ($scenarios->num_rows > 0) {
                    while ($scenario = $scenarios->fetch_assoc()) {
                        $isRated = $scenario['rated'] > 0;
                        echo '<li class="scenario-item">';
                        echo '<span>' . htmlspecialchars($scenario['title']) . '</span>';
                        echo '<a href="evaluateScenario.php?id=' . htmlspecialchars($scenario['scenario_id']) . '" class="btn-evaluate" ' . ($isRated ? 'disabled' : '') . '>' . ($isRated ? 'Already Rated' : 'Evaluate') . '</a>';
                        echo '</li>';
                    }
                } else {
                    echo "<p>No scenarios available at this time.</p>";
                }

                $stmt->close();
                ?>
            </ul>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php' ?>
