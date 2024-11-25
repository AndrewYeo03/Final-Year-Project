<?php
$titleName = "Completion Page - TARUMT Cyber Range";
include '../header_footer/header_student.php';

//Clear the current exercise index to prevent duplicate access
unset($_SESSION['current_exercise']);
unset($_SESSION['current_exercise_id']);
?>

<style>
    .completion-box {
        text-align: center;
        background: #fff;
        margin-top: 180px;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .completion-box h1 {
        color: #4CAF50;
        font-size: 36px;
    }

    .completion-box p {
        color: #555;
        font-size: 18px;
        margin: 20px 0;
    }

    .completion-box a {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: #fff;
        text-decoration: none;
        font-size: 16px;
        border-radius: 5px;
    }

    .completion-box a:hover {
        background-color: #45a049;
    }
</style>

<div class="container-fluid px-4">
    <div class="completion-box">
        <h1>Congratulations!</h1>
        <p>You have successfully completed all exercises.</p>
        <a href="student_dashboard.php">Go Back to Dashboard</a>
    </div>

</div>

<?php include '../header_footer/footer.php'; ?>