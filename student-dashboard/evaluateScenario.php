<?php
$titleName = "Evaluate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

$scenarioId = $_GET['id'];

// 获取场景信息
$stmt = $conn->prepare("SELECT * FROM scenario WHERE scenario_id = ?");
$stmt->bind_param("i", $scenarioId);
$stmt->execute();
$scenarioResult = $stmt->get_result();
$scenario = $scenarioResult->fetch_assoc();
$stmt->close();

// 获取场景下的所有练习
$stmt = $conn->prepare("
    SELECT e.exercise_id, e.title 
    FROM exercise e
    WHERE e.scenario_id = ?
");
$stmt->bind_param("i", $scenarioId);
$stmt->execute();
$exercises = $stmt->get_result();
$stmt->close();

// 获取学生信息
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT s.id AS student_id FROM students s INNER JOIN users u ON s.user_id = u.id WHERE u.username = ?");
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

// 检查该学生是否已经为场景评分
$stmt = $conn->prepare("
    SELECT * FROM scenario_ratings WHERE student_id = ? AND scenario_id = ?
");
$stmt->bind_param("ii", $studentId, $scenarioId);
$stmt->execute();
$scenarioRated = $stmt->get_result()->num_rows > 0;
$stmt->close();
?>

<style>
    .btn-back {
        background: none;
        color: #000;
        border: none;
        font-size: 18px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        padding: 8px 16px;
        margin-top: 10px;
        border-radius: 50px;
    }

    .btn-back:hover {
        color: #5a54e0;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px);
    }

    .btn-back .icon {
        font-size: 20px;
        margin-right: 8px;
    }

    .btn-back-text {
        font-size: 16px;
        letter-spacing: 1px;
    }

    .btn-back:active {
        transform: translateY(1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* 按钮样式 */
    .btn-evaluate {
        background-color: #6c63ff;
        color: white;
        padding: 12px 30px;
        font-size: 16px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-evaluate:hover {
        background-color: #5a54e0;
        transform: translateY(-5px);
    }

    /* Modal 样式 */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        padding-top: 100px;
        transition: opacity 0.3s ease-in-out;
    }

    .modal-content {
        background-color: #fff;
        margin: 0 auto;
        padding: 30px;
        border-radius: 10px;
        width: 50%;
        max-width: 600px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .close {
        color: #aaa;
        font-size: 30px;
        font-weight: bold;
        float: right;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }

    /* 练习列表样式 */
    .exercise-list {
        list-style-type: none;
        padding: 0;
    }

    .exercise-item {
        display: flex;
        justify-content: space-between;
        background-color: #fff;
        margin: 10px 0;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .exercise-item a {
        color: #fff;
        background-color: #6c63ff;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 50px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .exercise-item a:hover {
        background-color: #5a54e0;
        transform: translateY(-3px);
    }

    .no-exercises-message {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-size: 16px;
        margin-top: 150px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }

    .no-exercises-message .icon {
        font-size: 30px;
        margin-right: 10px;
    }
</style>

<div class="container-fluid px-4">
    <button class="btn-back" onclick="window.history.back();">
        <span class="icon">&#8592;</span>
        <span class="btn-back-text">Back</span>
    </button>

    <!-- Scenario Header and Rate Scenario Button -->
    <h2 class="mt-4">Scenario - <?= htmlspecialchars($scenario['title']); ?></h2>
    <button id="rateScenarioBtn" class="btn-evaluate" <?php echo $scenarioRated ? 'disabled' : ''; ?>>Rate Scenario</button>

    <!-- Scenario Rating Modal -->
    <div id="rateScenarioModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Rate the Scenario</h3>
            <form method="POST">
                <label for="scenarioRating">Rating (1-5):</label>
                <input type="number" id="scenarioRating" name="scenarioRating" min="1" max="5" required>
                <button type="submit" name="submitScenarioRating" class="btn-evaluate">Submit Rating</button>
            </form>
        </div>
    </div>

    <!-- Exercise List -->
    <h3 class="mt-4">Exercises for this Scenario</h3>
    <?php if ($exercises->num_rows > 0) { ?>
        <ul class="exercise-list">
            <?php while ($exercise = $exercises->fetch_assoc()) { ?>
                <li class="exercise-item">
                    <span><?php echo htmlspecialchars($exercise['title']); ?></span>
                    <a href="evaluateExercises.php?id=<?php echo $exercise['exercise_id']; ?>" class="btn-evaluate">Rate Exercise</a>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <div class="no-exercises-message">
            <span class="icon">&#9888;</span> <!-- A warning icon -->
            <strong>Notice:</strong> This scenario currently has no exercises.
        </div>
    <?php } ?>
</div>

<?php
// 处理场景评分提交
if (isset($_POST['submitScenarioRating']) && !$scenarioRated) {
    $scenarioRating = $_POST['scenarioRating'];
    $stmt = $conn->prepare("
        INSERT INTO scenario_ratings (student_id, scenario_id, rating) 
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iii", $studentId, $scenarioId, $scenarioRating);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('You have successfully rated the scenario!'); window.location.href='evaluateScenario.php?id=$scenarioId';</script>";
}
?>

<script>
    // 获取Modal元素
    var modal = document.getElementById("rateScenarioModal");
    var btn = document.getElementById("rateScenarioBtn");
    var span = document.getElementsByClassName("close")[0];

    // 点击按钮打开Modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // 点击关闭按钮关闭Modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // 如果点击在模态框外部，关闭模态框
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php include '../header_footer/footer.php'; ?>