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
            <span style="display: flex; flex-flow: row nowrap; justify-content:right;" class="close">&times;</span>
            <h3>Rate the Scenario</h3>
            <form method="POST">
                <h4>Part A: Evaluate the scenario content and objectives (1-10):</h4>

                <!-- 小题目1 -->
                <label for="partA_q1">How clear were the objectives of the scenario?</label><br>
                <input type="radio" id="partA_q1_1" name="partA_q1" value="1"> 1
                <input type="radio" id="partA_q1_2" name="partA_q1" value="2"> 2
                <input type="radio" id="partA_q1_3" name="partA_q1" value="3"> 3
                <input type="radio" id="partA_q1_4" name="partA_q1" value="4"> 4
                <input type="radio" id="partA_q1_5" name="partA_q1" value="5"> 5
                <input type="radio" id="partA_q1_6" name="partA_q1" value="6"> 6
                <input type="radio" id="partA_q1_7" name="partA_q1" value="7"> 7
                <input type="radio" id="partA_q1_8" name="partA_q1" value="8"> 8
                <input type="radio" id="partA_q1_9" name="partA_q1" value="9"> 9
                <input type="radio" id="partA_q1_10" name="partA_q1" value="10"> 10
                <br><br>

                <!-- 小题目2 -->
                <label for="partA_q2">Was the scenario content engaging?</label><br>
                <input type="radio" id="partA_q2_1" name="partA_q2" value="1"> 1
                <input type="radio" id="partA_q2_2" name="partA_q2" value="2"> 2
                <input type="radio" id="partA_q2_3" name="partA_q2" value="3"> 3
                <input type="radio" id="partA_q2_4" name="partA_q2" value="4"> 4
                <input type="radio" id="partA_q2_5" name="partA_q2" value="5"> 5
                <input type="radio" id="partA_q2_6" name="partA_q2" value="6"> 6
                <input type="radio" id="partA_q2_7" name="partA_q2" value="7"> 7
                <input type="radio" id="partA_q2_8" name="partA_q2" value="8"> 8
                <input type="radio" id="partA_q2_9" name="partA_q2" value="9"> 9
                <input type="radio" id="partA_q2_10" name="partA_q2" value="10"> 10
                <br><br>

                <!-- 小题目3 -->
                <label for="partA_q3">How relevant were the scenario objectives to your learning?</label><br>
                <input type="radio" id="partA_q3_1" name="partA_q3" value="1"> 1
                <input type="radio" id="partA_q3_2" name="partA_q3" value="2"> 2
                <input type="radio" id="partA_q3_3" name="partA_q3" value="3"> 3
                <input type="radio" id="partA_q3_4" name="partA_q3" value="4"> 4
                <input type="radio" id="partA_q3_5" name="partA_q3" value="5"> 5
                <input type="radio" id="partA_q3_6" name="partA_q3" value="6"> 6
                <input type="radio" id="partA_q3_7" name="partA_q3" value="7"> 7
                <input type="radio" id="partA_q3_8" name="partA_q3" value="8"> 8
                <input type="radio" id="partA_q3_9" name="partA_q3" value="9"> 9
                <input type="radio" id="partA_q3_10" name="partA_q3" value="10"> 10
                <br><br>

                <h4>Part B: Provide feedback on the instructor's guidance (1-10):</h4>

                <!-- 小题目1 -->
                <label for="partB_q1">How clear was the instructor's explanation of the scenario?</label><br>
                <input type="radio" id="partB_q1_1" name="partB_q1" value="1"> 1
                <input type="radio" id="partB_q1_2" name="partB_q1" value="2"> 2
                <input type="radio" id="partB_q1_3" name="partB_q1" value="3"> 3
                <input type="radio" id="partB_q1_4" name="partB_q1" value="4"> 4
                <input type="radio" id="partB_q1_5" name="partB_q1" value="5"> 5
                <input type="radio" id="partB_q1_6" name="partB_q1" value="6"> 6
                <input type="radio" id="partB_q1_7" name="partB_q1" value="7"> 7
                <input type="radio" id="partB_q1_8" name="partB_q1" value="8"> 8
                <input type="radio" id="partB_q1_9" name="partB_q1" value="9"> 9
                <input type="radio" id="partB_q1_10" name="partB_q1" value="10"> 10
                <br><br>

                <!-- 小题目2 -->
                <label for="partB_q2">How well did the instructor facilitate the scenario?</label><br>
                <input type="radio" id="partB_q2_1" name="partB_q2" value="1"> 1
                <input type="radio" id="partB_q2_2" name="partB_q2" value="2"> 2
                <input type="radio" id="partB_q2_3" name="partB_q2" value="3"> 3
                <input type="radio" id="partB_q2_4" name="partB_q2" value="4"> 4
                <input type="radio" id="partB_q2_5" name="partB_q2" value="5"> 5
                <input type="radio" id="partB_q2_6" name="partB_q2" value="6"> 6
                <input type="radio" id="partB_q2_7" name="partB_q2" value="7"> 7
                <input type="radio" id="partB_q2_8" name="partB_q2" value="8"> 8
                <input type="radio" id="partB_q2_9" name="partB_q2" value="9"> 9
                <input type="radio" id="partB_q2_10" name="partB_q2" value="10"> 10
                <br><br>

                <!-- 小题目3 -->
                <label for="partB_q3">Was the feedback from the instructor helpful?</label><br>
                <input type="radio" id="partB_q3_1" name="partB_q3" value="1"> 1
                <input type="radio" id="partB_q3_2" name="partB_q3" value="2"> 2
                <input type="radio" id="partB_q3_3" name="partB_q3" value="3"> 3
                <input type="radio" id="partB_q3_4" name="partB_q3" value="4"> 4
                <input type="radio" id="partB_q3_5" name="partB_q3" value="5"> 5
                <input type="radio" id="partB_q3_6" name="partB_q3" value="6"> 6
                <input type="radio" id="partB_q3_7" name="partB_q3" value="7"> 7
                <input type="radio" id="partB_q3_8" name="partB_q3" value="8"> 8
                <input type="radio" id="partB_q3_9" name="partB_q3" value="9"> 9
                <input type="radio" id="partB_q3_10" name="partB_q3" value="10"> 10
                <br><br>

                <h4>Part C: Evaluate the overall learning experience (1-10):</h4>

                <!-- 小题目1 -->
                <label for="partC_q1">How well did the scenario contribute to your overall learning?</label><br>
                <input type="radio" id="partC_q1_1" name="partC_q1" value="1"> 1
                <input type="radio" id="partC_q1_2" name="partC_q1" value="2"> 2
                <input type="radio" id="partC_q1_3" name="partC_q1" value="3"> 3
                <input type="radio" id="partC_q1_4" name="partC_q1" value="4"> 4
                <input type="radio" id="partC_q1_5" name="partC_q1" value="5"> 5
                <input type="radio" id="partC_q1_6" name="partC_q1" value="6"> 6
                <input type="radio" id="partC_q1_7" name="partC_q1" value="7"> 7
                <input type="radio" id="partC_q1_8" name="partC_q1" value="8"> 8
                <input type="radio" id="partC_q1_9" name="partC_q1" value="9"> 9
                <input type="radio" id="partC_q1_10" name="partC_q1" value="10"> 10
                <br><br>

                <!-- 小题目2 -->
                <label for="partC_q2">How effective was the combination of theory and practical application?</label><br>
                <input type="radio" id="partC_q2_1" name="partC_q2" value="1"> 1
                <input type="radio" id="partC_q2_2" name="partC_q2" value="2"> 2
                <input type="radio" id="partC_q2_3" name="partC_q2" value="3"> 3
                <input type="radio" id="partC_q2_4" name="partC_q2" value="4"> 4
                <input type="radio" id="partC_q2_5" name="partC_q2" value="5"> 5
                <input type="radio" id="partC_q2_6" name="partC_q2" value="6"> 6
                <input type="radio" id="partC_q2_7" name="partC_q2" value="7"> 7
                <input type="radio" id="partC_q2_8" name="partC_q2" value="8"> 8
                <input type="radio" id="partC_q2_9" name="partC_q2" value="9"> 9
                <input type="radio" id="partC_q2_10" name="partC_q2" value="10"> 10
                <br><br>

                <!-- 小题目3 -->
                <label for="partC_q3">How well did the learning materials support your understanding of the scenario?</label><br>
                <input type="radio" id="partC_q3_1" name="partC_q3" value="1"> 1
                <input type="radio" id="partC_q3_2" name="partC_q3" value="2"> 2
                <input type="radio" id="partC_q3_3" name="partC_q3" value="3"> 3
                <input type="radio" id="partC_q3_4" name="partC_q3" value="4"> 4
                <input type="radio" id="partC_q3_5" name="partC_q3" value="5"> 5
                <input type="radio" id="partC_q3_6" name="partC_q3" value="6"> 6
                <input type="radio" id="partC_q3_7" name="partC_q3" value="7"> 7
                <input type="radio" id="partC_q3_8" name="partC_q3" value="8"> 8
                <input type="radio" id="partC_q3_9" name="partC_q3" value="9"> 9
                <input type="radio" id="partC_q3_10" name="partC_q3" value="10"> 10
                <br><br>

                <!-- 小题目4 -->
                <label for="partC_q4">How satisfied were you with the overall structure and pacing of the scenario?</label><br>
                <input type="radio" id="partC_q4_1" name="partC_q4" value="1"> 1
                <input type="radio" id="partC_q4_2" name="partC_q4" value="2"> 2
                <input type="radio" id="partC_q4_3" name="partC_q4" value="3"> 3
                <input type="radio" id="partC_q4_4" name="partC_q4" value="4"> 4
                <input type="radio" id="partC_q4_5" name="partC_q4" value="5"> 5
                <input type="radio" id="partC_q4_6" name="partC_q4" value="6"> 6
                <input type="radio" id="partC_q4_7" name="partC_q4" value="7"> 7
                <input type="radio" id="partC_q4_8" name="partC_q4" value="8"> 8
                <input type="radio" id="partC_q4_9" name="partC_q4" value="9"> 9
                <input type="radio" id="partC_q4_10" name="partC_q4" value="10"> 10
                <br><br>

                <!-- 小题目5 -->
                <label for="partC_q5">How would you rate your overall learning experience from this scenario?</label><br>
                <input type="radio" id="partC_q5_1" name="partC_q5" value="1"> 1
                <input type="radio" id="partC_q5_2" name="partC_q5" value="2"> 2
                <input type="radio" id="partC_q5_3" name="partC_q5" value="3"> 3
                <input type="radio" id="partC_q5_4" name="partC_q5" value="4"> 4
                <input type="radio" id="partC_q5_5" name="partC_q5" value="5"> 5
                <input type="radio" id="partC_q5_6" name="partC_q5" value="6"> 6
                <input type="radio" id="partC_q5_7" name="partC_q5" value="7"> 7
                <input type="radio" id="partC_q5_8" name="partC_q5" value="8"> 8
                <input type="radio" id="partC_q5_9" name="partC_q5" value="9"> 9
                <input type="radio" id="partC_q5_10" name="partC_q5" value="10"> 10
                <br><br>

                <label style="font-weight:bold;" for="feedbackText">Feedback: </label><br/>
                <textarea name="feedbackText" rows="5" class="feedback" id="feedbackText" style="width:100%; resize:none; padding:10px;"></textarea>

                <input name="submitScenarioRating" type="submit" value="Submit Rating" style="float:right;">
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
if (isset($_POST['submitScenarioRating'])) {

    // Part A
    $partA1 = isset($_POST['partA_q1']) ? intval($_POST['partA_q1']) : 0;
    $partA2 = isset($_POST['partA_q2']) ? intval($_POST['partA_q2']) : 0;
    $partA3 = isset($_POST['partA_q3']) ? intval($_POST['partA_q3']) : 0;
    $partARating = $partA1 + $partA2 + $partA3;

    // Part B
    $partB1 = isset($_POST['partB_q1']) ? intval($_POST['partB_q1']) : 0;
    $partB2 = isset($_POST['partB_q2']) ? intval($_POST['partB_q2']) : 0;
    $partB3 = isset($_POST['partB_q3']) ? intval($_POST['partB_q3']) : 0;
    $partBRating = $partB1 + $partB2 + $partB3;

    // Part C
    $partC1 = isset($_POST['partC_q1']) ? intval($_POST['partC_q1']) : 0;
    $partC2 = isset($_POST['partC_q2']) ? intval($_POST['partC_q2']) : 0;
    $partC3 = isset($_POST['partC_q3']) ? intval($_POST['partC_q3']) : 0;
    $partC4 = isset($_POST['partC_q4']) ? intval($_POST['partC_q4']) : 0;
    $partC5 = isset($_POST['partC_q5']) ? intval($_POST['partC_q5']) : 0;
    $partCRating = $partC1 + $partC2 + $partC3 + $partC4 + $partC5;

    // 获取反馈
    $feedback = isset($_POST['feedbackText']) ? $_POST['feedbackText'] : "";

    // 插入数据库
    $stmt = $conn->prepare("
        INSERT INTO scenario_ratings (student_id, scenario_id, part_a_rating, part_b_rating, part_c_rating, feedback) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiiis", $studentId, $scenarioId, $partARating, $partBRating, $partCRating, $feedback);
    $stmt->execute();
    $stmt->close();

    // 提示信息
    echo "<script>alert('您已成功评分！'); window.location.href='evaluateScenario.php?id=$scenarioId';</script>";
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