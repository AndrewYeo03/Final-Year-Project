<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $class = strtoupper($_POST['class']);  // Programme & Tutorial Group
    $student_id = strtoupper($_POST['student_id']);  // Student ID
    $role_id = 1; // Fixed as student role

    // Check if the mailbox already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "This email has been registered";
    } else {
        // Insert user information
        $sql = "INSERT INTO users (username, email, password, role_id) VALUES ('$username', '$email', '$password', '$role_id')";

        if ($conn->query($sql) === TRUE) {
            $user_id = $conn->insert_id; // Get the ID of the newly inserted user

            // Insert student information
            $sqlStudent = "INSERT INTO students (user_id, student_id, class) VALUES ('$user_id', '$student_id', '$class')";

            if ($conn->query($sqlStudent) === TRUE) {
                // Display success message and show JavaScript popup
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="css/register.css"> 
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