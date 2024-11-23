<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    //Check that the OTP matches and is not expired
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND otp = ? AND expiry > NOW()");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        //OTP Verification Passed
        session_start();
        $_SESSION['verified_email'] = $email;
        echo "<script>alert('OTP verified successfully.'); window.location.href='reset_password.php';</script>";
    } else {
        echo "<script>alert('Invalid or expired OTP.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - TAR UMT Cyber Range</title>
    <link rel="icon" href="../pictures/school_logo.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/login.css">
</head>

<style>
    .login-box p {
        font-size: 14px;
        color: #666;
        margin: 15px 0;
    }

    /* Styling for OTP input field */
    .login-box input[type="text"][name="otp"] {
        width: 100%;
        padding: 12px;
        border: 2px solid #6c757d;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.15);
        color: #333;
        font-size: 14px;
        transition: all 0.3s ease-in-out;
    }

    .login-box input[type="text"][name="otp"]:focus {
        border-color: #495057;
        background-color: rgba(255, 255, 255, 0.2);
        outline: none;
    }
</style>

<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Verify OTP</h2>
            <p>Please enter the OTP sent to your email to verify your identity.</p>
            <form method="POST" action="">
                <label for="email">Email Address:</label><br>
                <input type="email" name="email" required><br><br>
                <label for="otp">OTP:</label><br>
                <input type="text" name="otp" maxlength="6" required><br><br>
                <button type="submit">Verify OTP</button>
            </form>
            <p class="register-link">
                <a href="forgot_password.php">Resend OTP</a>
            </p>
        </div>
    </div>
</body>

</html>