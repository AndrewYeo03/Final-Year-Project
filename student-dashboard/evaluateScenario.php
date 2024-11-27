<?php
$titleName = "Evaluate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

$scenarioId = $_GET['id'];

//Retrieve scenario information
$stmt = $conn->prepare("SELECT * FROM scenario WHERE scenario_id = ?");
$stmt->bind_param("i", $scenarioId);
$stmt->execute();
$scenarioResult = $stmt->get_result();
$scenario = $scenarioResult->fetch_assoc();
$stmt->close();

//Retrieve all exercises related with scenario
$stmt = $conn->prepare("
    SELECT e.exercise_id, e.title 
    FROM exercise e
    WHERE e.scenario_id = ?
");
$stmt->bind_param("i", $scenarioId);
$stmt->execute();
$exercises = $stmt->get_result();
$stmt->close();

//Retrieve student information
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

//Check if the student has already rated the scenario
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

    .btn-evaluate:disabled {
        background-color: #d3d3d3;
        color: #a9a9a9;
        cursor: not-allowed;
        border: 1px solid #a9a9a9;
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

    .radio-container {
        display: flex;
        justify-content: space-evenly;
        gap: 10px;
        flex-wrap: wrap;
    }

    .radio-container label {
        display: inline-block;
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
    <!-- 场景评分模态框 -->
    <div id="rateScenarioModal" class="modal">
        <div class="modal-content">
            <span style="display: flex; flex-flow: row nowrap; justify-content:right;" class="close">&times;</span>
            <h3>Rate Scenario</h3>
            <form method="POST">
                <h4>Part A: Evaluate Scenario Content and Objective (1-10):</h4>

                <!-- 使用JavaScript动态生成评分问题 -->
                <div id="partA_questions"></div>

                </br></br>
                <h4>Part B: Provide Feedback on Instructor Guidance (1-10):</h4>
                <div id="partB_questions"></div>

                </br></br>
                <h4>Part C: Rate the overall experience and facilities (1-10):</h4>
                <div id="partC_questions"></div>

                </br>
                <label style="font-weight:bold;" for="feedbackText">Feedback (Optional): </label><br />
                <textarea name="feedbackText" rows="5" class="feedback" id="feedbackText" style="width:100%; resize:none; padding:10px;"></textarea>

                <input name="submitScenarioRating" type="submit" value="Submit Rating" style="float:right;">
            </form>
        </div>
    </div>

    <h3 class="mt-4">Exercises for this Scenario</h3>
    <?php if ($exercises->num_rows > 0) { ?>
        <ul class="exercise-list">
            <?php while ($exercise = $exercises->fetch_assoc()) { ?>
                <?php
                // 检查用户是否已经评分该练习
                $stmt = $conn->prepare("
                SELECT * FROM exercise_ratings WHERE student_id = ? AND exercise_id = ?
            ");
                $stmt->bind_param("is", $studentId, $exercise['exercise_id']);
                $stmt->execute();
                $exerciseRated = $stmt->get_result()->num_rows > 0;
                $stmt->close();
                ?>
                <li class="exercise-item">
                    <span><?php echo htmlspecialchars($exercise['title']); ?></span>
                    <?php if ($exerciseRated) { ?>
                        <button class="btn-evaluate" disabled>Rated</button>
                    <?php } else { ?>
                        <a href="evaluateExercises.php?id=<?php echo $exercise['exercise_id']; ?>" class="btn-evaluate">Rate Exercise</a>
                    <?php } ?>
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
    echo "<script>alert('You have successfully rated!'); window.location.href='evaluateScenario.php?id=$scenarioId';</script>";
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

    // 设置评分问题的数据
    const partA_questions = [{
            question: 'How clear are the scene objectives?',
            name: 'partA_q1'
        },
        {
            question: 'How engaging is the scene content?',
            name: 'partA_q2'
        },
        {
            question: 'How relevant is the scene objective to your learning?',
            name: 'partA_q3'
        }
    ];

    const partB_questions = [{
            question: 'How clear was the instructor\'s explanation of the scene?',
            name: 'partB_q1'
        },
        {
            question: 'How well did the instructor facilitate the scene?',
            name: 'partB_q2'
        },
        {
            question: 'Was the instructor\'s feedback helpful?',
            name: 'partB_q3'
        }
    ];

    const partC_questions = [{
            question: 'How comfortable were you with the overall facilities during the scenario?',
            name: 'partC_q1'
        },
        {
            question: 'How would you rate the scenario\'s atmosphere?',
            name: 'partC_q2'
        },
        {
            question: 'How effective were the tools provided in the scenario?',
            name: 'partC_q3'
        },
        {
            question: 'Was the timeline for the scenario adequate?',
            name: 'partC_q4'
        },
        {
            question: 'How satisfied were you with the scenario\'s difficulty level?',
            name: 'partC_q5'
        }
    ];

    // Function to generate questions dynamically for each part
    function generateQuestions(part, questions) {
        const container = document.getElementById(part + '_questions');
        questions.forEach(q => {
            const div = document.createElement('div');
            div.classList.add('form-group');
            div.innerHTML = `
            <label for="${q.name}">${q.question}</label><br>
            <div class="radio-container">
                <label><input type="radio" name="${q.name}" value="1" required> 1</label>
                <label><input type="radio" name="${q.name}" value="2" required> 2</label>
                <label><input type="radio" name="${q.name}" value="3" required> 3</label>
                <label><input type="radio" name="${q.name}" value="4" required> 4</label>
                <label><input type="radio" name="${q.name}" value="5" required> 5</label>
                <label><input type="radio" name="${q.name}" value="6" required> 6</label>
                <label><input type="radio" name="${q.name}" value="7" required> 7</label>
                <label><input type="radio" name="${q.name}" value="8" required> 8</label>
                <label><input type="radio" name="${q.name}" value="9" required> 9</label>
                <label><input type="radio" name="${q.name}" value="10" required> 10</label>
            </div>
        `;
            container.appendChild(div);
        });
    }

    // Call the function to generate the questions for all parts
    generateQuestions('partA', partA_questions);
    generateQuestions('partB', partB_questions);
    generateQuestions('partC', partC_questions);

    // 表单提交时验证内容是否完整
    document.querySelector('form').addEventListener('submit', function(event) {
        let isValid = true;

        // 验证每个问题的radio按钮是否有选择
        document.querySelectorAll('.radio-container').forEach(container => {
            const radios = container.querySelectorAll('input[type="radio"]');
            const name = radios[0].name; // 获取当前问题的name属性
            const isSelected = Array.from(radios).some(radio => radio.checked);

            if (!isSelected) {
                isValid = false;
                alert(`Please answer the question: "${name}"`);
            }
        });

        // 验证 textarea 是否填写
        const feedbackText = document.getElementById('feedbackText');
        if (!feedbackText.value.trim()) {
            isValid = false;
            alert('Please provide feedback.');
        }

        // 如果验证失败，阻止表单提交
        if (!isValid) {
            event.preventDefault();
        }
    });
</script>

<?php include '../header_footer/footer.php'; ?>