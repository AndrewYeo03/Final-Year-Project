<?php
$titleName = "Rate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

//Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM students INNER JOIN users ON students.user_id = users.id WHERE users.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();
$stmt->close();
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

    .btn-evaluate:hover {
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
                // Retrieve exercises (scenarios) for the student
                $stmt = $conn->prepare("SELECT * FROM exercise 
                                        WHERE exercise_id IN (
                                            SELECT exercise_id 
                                            FROM instructor_classes 
                                            WHERE class_name = ?
                                        )");
                $stmt->bind_param("s", $studentData['class_name']);
                $stmt->execute();
                $exercises = $stmt->get_result();
                
                if ($exercises->num_rows > 0) {
                    while ($exercise = $exercises->fetch_assoc()) {
                        echo '<li class="scenario-item">';
                        echo '<span>' . htmlspecialchars($exercise['title']) . ' (' . htmlspecialchars($exercise['difficulty_level']) . ')</span>';
                        echo '<a href="evaluateScenario.php?id=' . htmlspecialchars($exercise['exercise_id']) . '" class="btn-evaluate">Evaluate</a>';
                        echo '</li>';
                    }
                } else {
                    echo "<p>No scenarios available for your class.</p>";
                }

                $stmt->close();
                ?>
            </ul>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php' ?>