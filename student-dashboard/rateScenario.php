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

//Radar chart
$stmt = $conn->prepare("
    SELECT s.title, AVG(((sr.part_a_rating + sr.part_b_rating + sr.part_c_rating) / 110)*100) AS avg_rating
    FROM scenario s
    INNER JOIN scenario_ratings sr ON s.scenario_id = sr.scenario_id
    GROUP BY s.title
    ORDER BY avg_rating DESC
    LIMIT 5
");
$stmt->execute();
$chartData = $stmt->get_result();
$radarScenarios = [];
$ratings = [];
while ($row = $chartData->fetch_assoc()) {
    $radarScenarios[] = $row['title'];
    $ratings[] = $row['avg_rating'];
}
var_dump(json_encode($radarScenarios), json_encode($ratings));
?>

<style>
    .charts-container {
        margin: 20px auto;
        padding: 20px;
        background-color: #f1f1f1;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
        width: 80%;
    }

    .charts-container h3 {
        font-size: 22px;
        color: #333;
        margin-bottom: 20px;
    }

    .charts {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: nowrap;
    }

    .chart {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        max-width: 45%;
    }

    canvas {
        max-width: 100%;
        height: auto;
    }

    .evaluation-container {
        padding: 20px;
        background-color: #f1f1f1;
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

<!-- Top 5 Scenarios Chart Section -->
<div class="charts-container">
    <h3>Top Scenarios Analysis</h3>
    <div class="charts">
        <!-- Spider/Radar Chart for Top 5 Highest Rated Scenarios -->
        <div class="chart">
            <h4>Top 5 Scenarios by Rating</h4>
            <canvas id="radarChart"></canvas>
        </div>

        <!-- Line Chart for Top 5 Scenarios by Experience -->
        <div class="chart">
            <h4>Top 5 Scenarios by Experience</h4>
            <canvas id="lineChart"></canvas>
        </div>
    </div>
</div>

<div class="evaluation-container">
    <div class="evaluation-header">
        <h2>Scenario Evaluation <?php echo date('Y'); ?></h2>
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
                    (SELECT COUNT(*) FROM scenario_ratings WHERE scenario_ratings.student_id = ? AND scenario_ratings.scenario_id = scenario.scenario_id) AS rated
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



<!-- Include Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    //Data for Radar Chart (Top 5 Highest Rated Scenarios)
    const radarData = {
        labels: <?php echo json_encode($radarScenarios); ?>,
        datasets: [{
            label: 'Average Ratings',
            data: <?php echo json_encode($ratings); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    // Radar Chart Configuration
    const radarConfig = {
        type: 'radar',
        data: radarData,
        options: {
            responsive: true,
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 5
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    };

    // Static Data for Line Chart (Top 5 Scenarios by Experience Score)
    const lineData = {
        labels: ['Scenario 1', 'Scenario 2', 'Scenario 3', 'Scenario 4', 'Scenario 5'],
        datasets: [{
            label: 'Experience Score',
            data: [90, 85, 80, 75, 70],
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
            tension: 0.4
        }]
    };

    // Line Chart Configuration
    const lineConfig = {
        type: 'line',
        data: lineData,
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    min: 0,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    };

    // Render Charts
    const radarChart = new Chart(document.getElementById('radarChart'), radarConfig);
    const lineChart = new Chart(document.getElementById('lineChart'), lineConfig);
</script>

<?php include '../header_footer/footer.php' ?>