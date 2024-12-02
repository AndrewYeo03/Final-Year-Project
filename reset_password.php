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
        // Update User Password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $newPassword, $email);
        $stmt->execute();

        // Deleting OTP data
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Clear Session
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
            <form method="POST" action="" onsubmit="return validateForm(event)">
                <label for="new_password">New Password:</label><br>
                <input type="password" id="new_password" name="new_password" required><br><br>
                <label for="confirm_password">Confirm New Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
                <button type="submit">Reset Password</button>
            </form>
            <p class="register-link">
                <a href="login.php">Back to Login</a>
            </p>
        </div>
    </div>

    <script>
        // Check password complexity
        function validatePassword(password) {
            const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{6,}$/;

            // Checks compliance with basic complexity requirements
            if (!complexityRegex.test(password)) {
                return false;
            }

            // Checks if it contains characters in order or reverse order
            for (let i = 0; i < password.length - 2; i++) {
                const char1 = password.charCodeAt(i);
                const char2 = password.charCodeAt(i + 1);
                const char3 = password.charCodeAt(i + 2);

                // Check sequence (such as 123 or abc)
                if (char2 === char1 + 1 && char3 === char2 + 1) {
                    return false;
                }

                // Check for reverse order (such as 321 or cba)
                if (char2 === char1 - 1 && char3 === char2 - 1) {
                    return false;
                }
            }

            return true;
        }

        // Form Validation
        function validateForm(event) {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert('Password and Confirm Password do not match!');
                event.preventDefault();
                return false;
            }

            if (!validatePassword(password)) {
                alert('The password must be at least 6 characters long, contain uppercase, lowercase, numbers, and symbols, and cannot contain three consecutive characters in order or reverse order (e.g., "123" or "cba")!');
                event.preventDefault();
                return false;
            }
        }
    </script>
</body>

</html>
