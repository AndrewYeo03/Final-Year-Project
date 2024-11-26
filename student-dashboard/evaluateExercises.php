<?php
$titleName = "Evaluate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

$exerciseId = $_GET['id'];

// 获取 exercise 信息
$stmt = $conn->prepare("SELECT * FROM exercise WHERE exercise_id = ?");
$stmt->bind_param("i", $exerciseId);
$stmt->execute();
$scenarioResult = $stmt->get_result();
$exercise = $scenarioResult->fetch_assoc();
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
</style>

<div class="container-fluid px-4">
    <button class="btn-back" onclick="window.history.back();">
        <span class="icon">&#8592;</span>
        <span class="btn-back-text">Back</span>
    </button>

    <!-- Scenario Header and Rate Scenario Button -->
    <h2 class="mt-4">Exercises - <?= htmlspecialchars($exercise['title']); ?></h2> 

</div>

<?php include '../header_footer/footer.php'; ?>