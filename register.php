<?php
include('connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $student_id = strtoupper($_POST['student_id']);
    $role_id = 1; // Fixed as student role

    $password_hashed = md5($password); // 对密码进行哈希处理

    // Check if the email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('This email has already been registered');</script>";
    } else {
        // Insert into users table
        $sql = "INSERT INTO users (username, email, password, role_id) VALUES ('$username', '$email', '$password_hashed', '$role_id')";

        if ($conn->query($sql) === TRUE) {
            $user_id = $conn->insert_id; // Get the ID of the newly inserted user

            // Insert into students table
            $sqlStudent = "INSERT INTO students (user_id, student_id) VALUES ('$user_id', '$student_id')";

            if ($conn->query($sqlStudent) === TRUE) {
                // After inserting the user into the `users` table
                $verificationToken = bin2hex(random_bytes(16)); // Generate a unique token
                $sqlUpdateToken = "UPDATE users SET verification_token = '$verificationToken' WHERE email = '$email'";
                $conn->query($sqlUpdateToken);

                // Email Verification Link
                $verificationLink = "http://localhost:3000//verify_registration.php?token=$verificationToken";

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
                    $mail->setFrom('no-reply@tarumt-cyber-range.com', 'TAR UMT Cyber Range');
                    $mail->addAddress($email); // Add recipient

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Email Address';
                    $mail->Body = "
                    Dear $username,<br><br>
                    Please verify your email by clicking the link below:<br>$verificationLink<br><br>
                    Thank you!<br><br>
                    Best regards,<br>
                    <strong>TARUMT Cyber Range Team</strong>
                    ";

                    $mail->send();
                    echo "<script>alert('Registration successful. Please check your email to verify your account.');window.location.href = 'login.php';</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Failed to send verification email: {$mail->ErrorInfo}');</script>";
                }
            } else {
                echo "<script>alert('Failed to register student information: $conn->error');</script>";
            }
        } else {
            echo "<script>alert('Registration failed: $conn->error');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - TAR UMT Cyber Range</title>
    <link rel="icon" href="../pictures/school_logo.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/register.css">

    <script>
        //Check password complexity
        function validatePassword(password) {
            const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{6,}$/;

            //Checks compliance with basic complexity requirements
            if (!complexityRegex.test(password)) {
                return false;
            }

            //Checks if it contains characters in order or reverse order
            for (let i = 0; i < password.length - 2; i++) {
                const char1 = password.charCodeAt(i);
                const char2 = password.charCodeAt(i + 1);
                const char3 = password.charCodeAt(i + 2);

                //Check sequence (such as 123 or abc)
                if (char2 === char1 + 1 && char3 === char2 + 1) {
                    return false;
                }

                //Check for reverse order (such as 321 or cba)
                if (char2 === char1 - 1 && char3 === char2 - 1) {
                    return false;
                }
            }

            return true;
        }

        //Form Validation
        function validateForm(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

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
</head>

<body>

    <div class="register-container">
        <div class="register-box">
            <h2>Start Your Journey!</h2>
            <form method="POST" action="register.php" onsubmit="validateForm(event)">
                <label for="username">Full Name:</label><br>
                <input type="text" name="username" placeholder="Tan Ah Beng" required><br><br>

                <label for="email">Email Address:</label><br>
                <input type="email" name="email" placeholder="tanab-wm21@student.tarc.edu.my" required><br><br>

                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password" required><br><br>

                <label for="confirmPassword">Confirm Password:</label><br>
                <input type="password" name="confirm_password" id="confirmPassword" required><br><br>

                <label for="student_id">Student ID:</label><br>
                <input type="text" name="student_id" placeholder="12WMR12345" required><br><br>

                <button type="submit">Register</button>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>

</html>