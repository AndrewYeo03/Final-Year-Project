<?php
$titleName = "Evaluate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

$exerciseId = $_GET['id'];

// 获取 exercise 信息
$stmt = $conn->prepare("SELECT * FROM exercise WHERE exercise_id = ?");
$stmt->bind_param("s", $exerciseId);
$stmt->execute();
$scenarioResult = $stmt->get_result();
$exercise = $scenarioResult->fetch_assoc();
$stmt->close();

//Stored scenario id
$scenarioId = $exercise['scenario_id'];

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

    /* Container styling */
    .container-fluid {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2, h3, h4 {
        text-align: center;
        font-family: Arial, sans-serif;
        color: #333;
    }

    .breadcrumb {
        justify-content: center;
        background: transparent;
        font-size: 14px;
        font-weight: 500;
        color: #666;
    }

    /* Feedback textarea styling */
    textarea {
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 5px;
        padding: 10px;
        font-size: 14px;
        font-family: Arial, sans-serif;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    textarea:focus {
        outline: none;
        border-color: #5a54e0;
        box-shadow: 0 0 6px rgba(90, 84, 224, 0.3);
    }

    /* Radio button container styling */
    .radio-container {
        display: flex;
        justify-content: space-between;
    }

    .radio-container label {
        display: flex;
        align-items: center;
        font-size: 14px;
        margin: 0 5px;
        cursor: pointer;
    }

    .radio-container input[type="radio"] {
        margin-right: 5px;
    }

    /* Submit button styling */
    input[type="submit"] {
        width: 100%;
        background: #5a54e0;
        color: white;
        border: none;
        font-size: 16px;
        font-weight: bold;
        padding: 12px 20px;
        border-radius: 50px;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
    }

    input[type="submit"]:hover {
        background: #4842c8;
        box-shadow: 0 4px 10px rgba(90, 84, 224, 0.4);
        transform: translateY(-2px);
    }

    input[type="submit"]:active {
        background: #3b35a8;
        transform: translateY(1px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Form group styling for spacing */
    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        color: #333;
        display: block;
        margin-bottom: 10px;
    }
</style>

<div class="container-fluid px-4">
    <button class="btn-back" onclick="window.history.back();">
        <span class="icon">&#8592;</span>
        <span class="btn-back-text">Back</span>
    </button>

    <!-- Scenario Header and Rate Scenario Button -->
    <h2 class="mt-4"><?= htmlspecialchars($exercise['title']); ?></h2>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= htmlspecialchars($exercise['exerciseType']); ?> | <?= htmlspecialchars($exercise['difficulty_level']); ?></li>
    </ol>

    <h3>Rate Exercise</h3>
    <form method="POST">
        <h4>Part A: Evaluate Exercise Content and Objective (1-10):</h4>

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

        <input name="submitExerciseRating" type="submit" value="Submit Rating" style="float:right;">
    </form>
</div>

<?php
// 处理场景评分提交
if (isset($_POST['submitExerciseRating'])) {

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
        INSERT INTO exercise_ratings (student_id, exercise_id, part_a_rating, part_b_rating, part_c_rating, feedback) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isiiis", $studentId, $exerciseId, $partARating, $partBRating, $partCRating, $feedback);
    $stmt->execute();
    $stmt->close();

    // 提示信息
    echo "<script>alert('You have successfully rated!'); window.location.href='evaluateScenario.php?id=$scenarioId';</script>";
}
?>

<script>
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