<?php
$titleName = "Student Response - TARUMT Cyber Range";
include  '../header_footer/header_instructor.php';
include '../connection.php';

//Get the username of the currently logged in lecturer
$username = $_SESSION['username'];

//Get the lecturer's information from the database
$stmt = $conn->prepare("
    SELECT i.id AS id
    FROM instructors i
    INNER JOIN users u ON i.user_id = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$instructorData = $result->fetch_assoc();
$stmt->close();

//Get Instructor ID
$instructorId = $instructorData['id'];

// Generate a date range of the last 10 days
$dates = [];
for ($i = 9; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[$date] = 0;  // Initial make it as 0
}

// Get the data for the last 10 days
$stmt = $conn->prepare("
    SELECT DATE(sv.submission_date) AS submission_day, COUNT(sv.video_id) AS daily_submissions
    FROM submitted_videos sv
    JOIN students s ON sv.student_id = s.id
    JOIN student_classes sc ON s.id = sc.student_id
    JOIN class c ON sc.class_name = c.class_name
    JOIN instructor_classes ic ON c.class_name = ic.class_name
    WHERE ic.instructor_id = ? AND sv.submission_date >= DATE_SUB(CURDATE(), INTERVAL 10 DAY)
    GROUP BY DATE(sv.submission_date)
    ORDER BY submission_day
");
$stmt->bind_param("i", $instructorId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $dates[$row['submission_day']] = $row['daily_submissions'];
}
$stmt->close();

// Fill in data for dailySubmissions and labelsDaily
$labelsDaily = array_keys($dates);
$dailySubmissions = array_values($dates);

// Generate a date range of the last 6 months
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i month"));
    $months[$month] = 0;  // Initial make it as 0
}

// Get the data for the last 6 months
$stmt = $conn->prepare("
    SELECT DATE_FORMAT(sv.submission_date, '%Y-%m') AS submission_month, COUNT(sv.video_id) AS monthly_submissions
    FROM submitted_videos sv
    JOIN students s ON sv.student_id = s.id
    JOIN student_classes sc ON s.id = sc.student_id
    JOIN class c ON sc.class_name = c.class_name
    JOIN instructor_classes ic ON c.class_name = ic.class_name
    WHERE ic.instructor_id = ? AND sv.submission_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY submission_month
    ORDER BY submission_month
");
$stmt->bind_param("i", $instructorId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $months[$row['submission_month']] = $row['monthly_submissions'];
}
$stmt->close();

// Populate data for monthlySubmissions and labelsMonthly
$labelsMonthly = array_keys($months);
$monthlySubmissions = array_values($months);

// Get the classes taught by the instructor
$stmt = $conn->prepare("
    SELECT c.class_name
    FROM class c
    JOIN instructor_classes ic ON c.class_name = ic.class_name
    WHERE ic.instructor_id = ?
");
$stmt->bind_param("i", $instructorId);
$stmt->execute();
$result = $stmt->get_result();
$classes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Set the current class logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_class'])) {
    $_SESSION['current_class'] = $_POST['selected_class'];
} elseif (!isset($_SESSION['current_class']) && count($classes) > 0) {
    $_SESSION['current_class'] = $classes[0]['class_name'];
}
$currentClass = $_SESSION['current_class'] ?? null;

// Get student submission records for the current class
$submissions = [];
if ($currentClass) {
    $stmt = $conn->prepare("
        SELECT 
            u.username AS student_name, 
            c.class_name, 
            e.title AS exercise_title, 
            sv.submission_date AS submission_date, 
            sv.video_link AS video_submission_link, 
            s1.assigned_date AS scenario_assigned_date,
            sf.file_content 
        FROM students s 
        JOIN users u ON u.id = s.user_id 
        JOIN student_classes sc ON s.id = sc.student_id 
        JOIN class c ON sc.class_name = c.class_name 
        JOIN submitted_videos sv ON s.id = sv.student_id 
        JOIN exercise e ON e.exercise_id = sv.exercise_id
        JOIN scenario s1 ON e.scenario_id = s1.scenario_id
        LEFT JOIN submitted_files sf ON s.id = sf.student_id 
        WHERE c.class_name = ? 
        ORDER BY sv.submission_date ASC;
    ");
    $stmt->bind_param("s", $currentClass);
    $stmt->execute();
    $result = $stmt->get_result();
    $submissions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<style>
    .button-container button {
        display: inline-block;
        width: 110px;
        margin: 5px 5px;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    button.blue {
        background-color: blue;
        color: white;
    }

    button.blue:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    }

    button.green {
        background-color: green;
        color: white;
    }

    button.green:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    }
</style>


<div class="container-fluid px-4">
    <!--Place your main content here -->
    <h1 class="mt-4">Student Response</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Student Response</li>
    </ol>
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Daily Submission of Scenario
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Monthly Submission of Scenario
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" class="mb-4">
        <div class="custom-dropdown">
            <select id="class-select" name="selected_class" class="form-select" onchange="this.form.submit()">
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class['class_name']); ?>"
                        <?= $class['class_name'] === $currentClass ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($class['class_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Submission of Scenario
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Scenario Name</th>
                        <th>Assignment Date</th>
                        <th>Submission Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Exercises Name</th>
                        <th>Assigned Date</th>
                        <th>Submission Date</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($submission['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($submission['exercise_title']); ?></td>
                            <td><?php echo htmlspecialchars($submission['scenario_assigned_date']); ?></td>
                            <td><?php echo htmlspecialchars($submission['submission_date']); ?></td>
                            <td>
                                <div class="button-container">
                                    <button class="blue" onclick="window.open('<?php echo htmlspecialchars($submission['video_submission_link']); ?>', '_blank')">
                                        Recording Video
                                    </button>
                                    <button class="green" onclick="openCommandLog('<?php echo htmlspecialchars($submission['file_content']); ?>')">
                                        Command Log
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for displaying command log -->
<div class="modal" id="commandLogModal" tabindex="-1" aria-labelledby="commandLogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commandLogModalLabel">Command Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="commandLogContent">
                <!-- Command log content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadLogButton">Download as Text</button>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle opening of the command log modal
    function openCommandLog(fileContent) {
        console.log(fileContent);
        // Load the file contents into the modal
        document.getElementById('commandLogContent').textContent = fileContent;

        // Open modal
        var commandLogModal = new bootstrap.Modal(document.getElementById('commandLogModal'));
        commandLogModal.show();
    }

    // Downloading log files
    document.getElementById('downloadLogButton').addEventListener('click', function() {
        var content = document.getElementById('commandLogContent').textContent;
        var blob = new Blob([content], { type: 'text/plain' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'command_log.txt';
        link.click();
    });
</script>

<!-- chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function filterClass() {
        var selectedClass = document.getElementById('classSelect').value;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'path_to_your_php_file.php?class=' + encodeURIComponent(selectedClass), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.querySelector('table tbody').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
</script>
<script>
    // Daily submissions chart (Line Chart)
    var ctxDaily = document.getElementById('dailyChart').getContext('2d');
    var dailyChart = new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labelsDaily); ?>,
            datasets: [{
                label: 'Daily Submissions',
                data: <?php echo json_encode($dailySubmissions); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Submission Count'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // Monthly submissions chart (Bar Chart)
    var ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    var monthlyChart = new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labelsMonthly); ?>,
            datasets: [{
                label: 'Monthly Submissions',
                data: <?php echo json_encode($monthlySubmissions); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Submission Count'
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include '../header_footer/footer.php' ?>