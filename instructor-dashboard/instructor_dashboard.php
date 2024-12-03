<?php
$titleName = "Instructor Dashboard - TARUMT Cyber Range";
include  '../header_footer/header_instructor.php';
include '../connection.php';

// Radar chart
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
$stmt->close();

// Bar chart for Scenario Part A Ratings
$stmt = $conn->prepare("
    SELECT s.title, AVG(sr.part_a_rating) AS avg_part_a_rating 
    FROM scenario s 
    INNER JOIN scenario_ratings sr ON s.scenario_id = sr.scenario_id 
    GROUP BY s.title 
    ORDER BY avg_part_a_rating DESC 
    LIMIT 5
");
$stmt->execute();
$barChartData = $stmt->get_result();
$barScenarios = [];
$partARatings = [];
while ($row = $barChartData->fetch_assoc()) {
    $barScenarios[] = $row['title'];
    $partARatings[] = $row['avg_part_a_rating'];
}
$stmt->close();

// Area chart for Scenario Part B Ratings
$stmt = $conn->prepare("
    SELECT s.title, MAX(sr.part_b_rating) AS max_part_b_rating
    FROM scenario s
    INNER JOIN scenario_ratings sr ON s.scenario_id = sr.scenario_id
    GROUP BY s.title
    ORDER BY max_part_b_rating DESC
    LIMIT 5
");
$stmt->execute();
$partBAreaData = $stmt->get_result();
$areaScenarios = [];
$partBRatings = [];
while ($row = $partBAreaData->fetch_assoc()) {
    $areaScenarios[] = $row['title'];
    $partBRatings[] = $row['max_part_b_rating'];
}
$stmt->close();

// Line chart for Scenario Part C Ratings
$stmt = $conn->prepare("
    SELECT s.title, MAX(sr.part_c_rating) AS max_part_c_rating
    FROM scenario s
    INNER JOIN scenario_ratings sr ON s.scenario_id = sr.scenario_id
    GROUP BY s.title
    ORDER BY max_part_c_rating DESC
    LIMIT 5
");
$stmt->execute();
$PartCLineData = $stmt->get_result();
$linePartC = [];
$partCRatings = [];
while ($row = $PartCLineData->fetch_assoc()) {
    $linePartC[] = $row['title'];
    $partCRatings[] = $row['max_part_c_rating'];
}
$stmt->close();

// Scatter chart for Scenario Part C Ratings
$stmt = $conn->prepare("
    SELECT s.student_id, sv.exercise_id, sv.submission_date 
    FROM submitted_videos sv
    INNER JOIN students s ON sv.student_id = s.id
    ORDER BY sv.submission_date ASC
");
$stmt->execute();
$submissionData = $stmt->get_result();
$scatterData = [];
while ($row = $submissionData->fetch_assoc()) {
    $scatterData[] = [
        'student_id' => $row['student_id'],
        'exercise_id' => $row['exercise_id'],
        'submission_date' => date('c', strtotime($row['submission_date']))
    ];
}
$stmt->close();
?>

<style>
    .charts-container {
        margin: 20px auto;
        padding: 20px;
        background-color: #f1f1f1;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
        width: 90%;
    }

    .charts-container h3 {
        font-size: 22px;
        color: #333;
        margin-bottom: 20px;
    }

    .charts-row {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .chart {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        max-width: 45%;
        /* Adjusts width for two charts per row */
        height: 400px;
    }

    canvas {
        width: 100%;
        height: 400px;
    }
</style>

<!-- Top 5 Scenarios Chart Section -->
<div class="charts-container">
    <h3>Top Scenarios Analysis</h3>

    <div class="charts-row">
        <div class="chart">
            <h4>Top 5 Scenarios (Overall)</h4>
            <canvas id="polarChart"></canvas>
        </div>

        <div class="chart">
            <h4>Top 5 Scenarios (Best Content)</h4>
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <div class="charts-row">
        <div class="chart">
            <h4>Top 5 Scenarios (Best Instructor Guidance)</h4>
            <canvas id="areaChart"></canvas>
        </div>

        <div class="chart">
            <h4>Top 5 Scenarios (Best Experience and Facilities)</h4>
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <div class="charts-row">
        <div class="chart">
            <h4>Student Submission Timeline</h4>
            <canvas id="scatterChart"></canvas>
        </div>
    </div>
</div>

<!-- Include Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Function to format long labels
    function formatLabels(labels) {
        return labels.map(label => label.length > 40 ? label.slice(0, 40) + '...' : label);
    }

    // Tooltip to show full labels
    function createTooltipItem(context) {
        return context.raw.label;
    }

    // Data for Radar Chart (Top 5 Highest Rated Scenarios)
    const polarData = {
        labels: formatLabels(<?php echo json_encode($radarScenarios); ?>),
        datasets: [{
            label: 'Average Ratings',
            data: <?php echo json_encode($ratings); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)', // Color for the first category
                'rgba(54, 162, 235, 0.5)', // Color for the second category
                'rgba(255, 206, 86, 0.5)', // Color for the third category
                'rgba(75, 192, 192, 0.5)', // Color for the fourth category
                'rgba(153, 102, 255, 0.5)' // Color for the fifth category
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)', // Border color for the first category
                'rgba(54, 162, 235, 1)', // Border color for the second category
                'rgba(255, 206, 86, 1)', // Border color for the third category
                'rgba(75, 192, 192, 1)', // Border color for the fourth category
                'rgba(153, 102, 255, 1)' // Border color for the fifth category
            ],
            borderWidth: 1
        }]
    };

    // Polar Area Chart Configuration
    const polarConfig = {
        type: 'polarArea',
        data: polarData,
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        title: (items) => items[0].raw.label
                    }
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    };

    // Bar Chart Data (Top 5 Scenarios by Best Content)
    const barData = {
        labels: formatLabels(<?php echo json_encode($barScenarios); ?>),
        datasets: [{
            label: 'Part A Ratings',
            data: <?php echo json_encode($partARatings); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    };

    const barConfig = {
        type: 'bar',
        data: barData,
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    min: 0,
                    max: 30
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        title: (items) => items[0].raw.label
                    }
                }
            }
        }
    };

    // Area Chart Data (Top 5 Scenarios by Part B Ratings)
    const areaData = {
        labels: formatLabels(<?php echo json_encode($areaScenarios); ?>),
        datasets: [{
            label: 'Part B Ratings',
            data: <?php echo json_encode($partBRatings); ?>,
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1,
            fill: true // This makes it an area chart
        }]
    };

    const areaConfig = {
        type: 'line', // Use line chart type with fill to create an area chart
        data: areaData,
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    min: 0,
                    max: 30
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        title: (items) => items[0].raw.label
                    }
                }
            }
        }
    };

    // Line Chart Data (Top 5 Scenarios by Best Experience and Facilities)
    const lineData = {
        labels: formatLabels(<?php echo json_encode($linePartC); ?>),
        datasets: [{
            label: 'Experience Score',
            data: <?php echo json_encode($partCRatings); ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
            tension: 0.4
        }]
    };

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
                    max: 50
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        title: (items) => items[0].raw.label
                    }
                }
            }
        }
    };

    // Scatter Chart Data (Timelines for All student exercise submission)
    const scatterRawData = <?php echo json_encode($scatterData); ?>;
    console.log("Scatter Raw Data:", scatterRawData);


    const scatterData = scatterRawData.map(entry => ({
        x: new Date(entry.submission_date),
        y: parseInt(entry.exercise_id, 10),
        student: entry.student_id
    }));

    // 打印数据进行调试
    console.log("Formatted Scatter Data:", scatterData);

    const scatterConfig = {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Submission Timeline',
                data: scatterData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day'
                    },
                    title: {
                        display: true,
                        text: 'Submission Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Exercise ID'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const studentId = scatterData[context.dataIndex].student;
                            return `Student ID: ${studentId}, Exercise ID: ${context.raw.y}`;
                        }
                    }
                }
            }
        }
    };

    // Render Charts
    new Chart(document.getElementById('polarChart').getContext('2d'), polarConfig);
    new Chart(document.getElementById('barChart'), barConfig);
    new Chart(document.getElementById('areaChart'), areaConfig);
    new Chart(document.getElementById('lineChart'), lineConfig);
    new Chart(document.getElementById('scatterChart').getContext('2d'), scatterConfig);
</script>
<?php include '../header_footer/footer.php'; ?>