<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $class = strtoupper($_POST['class']);
    $student_id = strtoupper($_POST['student_id']);
    $role_id = 1;

    // Password validation
    if (strlen($password) < 6) {
        echo "<div class='error'>Password must be at least 6 characters long</div>";
    } elseif ($password !== $confirm_password) {
        echo "<div class='error'>Passwords do not match</div>";
    } else {
        $password_hashed = md5($password);

        // Check if the email already exists
        $checkEmail = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            echo "This email has been registered";
        } else {
            // Insert user information
            $sql = "INSERT INTO users (username, email, password, role_id) VALUES ('$username', '$email', '$password_hashed', '$role_id')";

            if ($conn->query($sql) === TRUE) {
                $user_id = $conn->insert_id;

                // Insert student information
                $sqlStudent = "INSERT INTO students (user_id, student_id, class) VALUES ('$user_id', '$student_id', '$class')";

                if ($conn->query($sqlStudent) === TRUE) {
                    echo "<script>
                            alert('Student registration successful');
                            window.location.href = 'login.php'; 
                          </script>";
                } else {
                    echo "<div class='error'>Failed to register student information: " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='error'>Registration failed: " . $conn->error . "</div>";
            }
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
    <link rel="icon" href="../pictures/school_logo.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="css/register.css"> 
    <script>
        function validateForm() {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            if (password.length < 6) {
                alert("Password must be at least 6 characters long");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<div class="register-container">
    <div class="register-box">
        <h2>Student Registration</h2>
        <form method="POST" action="register.php">
            <label for="username">Full Name:</label><br>
            <input type="text" name="username" placeholder="Tan Ah Beng" required><br><br>

            <label for="email">Email Address:</label><br>
            <input type="email" name="email" placeholder="tanab-wm21@student.tarc.edu.my" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" required><br><br>

            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" name="confirm_password" required><br><br>

            <label for="student_id">Student ID:</label><br>
            <input type="text" name="student_id" placeholder="12WMR12345" required><br><br>

            <label for="class">Programme & Tutorial Group:</label><br>
            <input type="text" name="class" placeholder="RIS3G7" required><br><br>

            <button type="submit">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

</body>
</html>