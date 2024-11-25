<?php
include('connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, "0", STR_PAD_LEFT);
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Insert or update OTP
        $stmt = $conn->prepare("
            INSERT INTO password_resets (email, otp, expiry) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE otp = ?, expiry = ?
        ");
        $stmt->bind_param("sssss", $email, $otp, $expiry, $otp, $expiry);
        $stmt->execute();

        // Send OTP using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'tarumtcyberrange@gmail.com'; // Your email
            $mail->Password = 'vppiisklkqaqozeb'; // Your email password (or app-specific password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('tarumtcyberrange@gmail.com', 'TAR UMT Cyber Range');
            $mail->addAddress($email); // Add recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = "Your OTP is: <strong>$otp</strong>. It is valid for 15 minutes.";

            $mail->send();
            echo "<script>alert('OTP sent to your email. Please check your inbox.'); window.location.href='verify_otp.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Failed to send OTP. Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email address.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - TAR UMT Cyber Range</title>
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
            <h2>Forgot Password</h2>
            <p>Please enter your registered email address. We will send an OTP for password recovery.</p>
            <form method="POST" action="">
                <label for="email">Email Address:</label><br>
                <input type="email" name="email" required><br><br>
                <button type="submit">Send OTP</button>
            </form>
            <p class="register-link">
                <a href="login.php">Back to Login</a>
            </p>
        </div>
    </div>
</body>

</html>