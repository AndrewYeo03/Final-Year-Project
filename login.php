<?php
include('connection.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['username'] = $user['username'];

        // Redirect by role
        if ($user['role_id'] == 1) {
            header("Location: student-dashboard/student_dashboard.php");
        } elseif ($user['role_id'] == 2) {
            header("Location: instructor-dashboard/instructor_dashboard.php");
        } elseif ($user['role_id'] == 3) {
            header("Location: admin-dashboard/admin_dashboard.php");
        }
    } else {
        echo "<div class='error'>Login failed, email or password is incorrect</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <label for="email">Email Address:</label><br>
            <input type="email" name="email" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" name="password" required><br><br>
            <button type="submit">Login</button>
        </form>
        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

</body>
</html>