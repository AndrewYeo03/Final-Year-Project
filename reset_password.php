<?php
include('connection.php');
session_start();

if (!isset($_SESSION['verified_email'])) {
    echo "<script>alert('Unauthorized access.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['verified_email'];
    $newPassword = md5($_POST['new_password']);
    $confirmPassword = md5($_POST['confirm_password']);

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        //Update User Password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $newPassword, $email);
        $stmt->execute();

        //Deleting OTP data
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        //Clear Session
        unset($_SESSION['verified_email']);
        echo "<script>alert('Password reset successfully.'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TAR UMT Cyber Range</title>
    <link rel="icon" href="../pictures/school_logo.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/login.css">
</head>

<style>
    .login-box p {
        font-size: 14px;
        color: #666;
        margin: 15px 0;
    }
</style>

<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Reset Password</h2>
            <p>Enter your new password below to reset it.</p>
            <form method="POST" action="">
                <label for="new_password">New Password:</label><br>
                <input type="password" name="new_password" required><br><br>
                <label for="confirm_password">Confirm New Password:</label><br>
                <input type="password" name="confirm_password" required><br><br>
                <button type="submit">Reset Password</button>
            </form>
            <p class="register-link">
                <a href="login.php">Back to Login</a>
            </p>
        </div>
    </div>
</body>

</html>