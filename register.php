<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $class = $_POST['class'];  //Programme & Tutorial Group
    $student_id = $_POST['student_id'];  //Student ID
    $role_id = 1; //Fixed as student role

    //Check if the mailbox already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "This email has been registered";
    } else {
        //Insert user information
        $sql = "INSERT INTO users (username, email, password, role_id) VALUES ('$username', '$email', '$password', '$role_id')";

        if ($conn->query($sql) === TRUE) {
            $user_id = $conn->insert_id; //Get the ID of the newly inserted user

            //Insert student information
            $sqlStudent = "INSERT INTO students (user_id, student_id, class) VALUES ('$user_id', '$student_id', '$class')";

            if ($conn->query($sqlStudent) === TRUE) {
                echo "Student registration successful";
            } else {
                echo "Failed to register student information: " . $conn->error;
            }
        } else {
            echo "Registration failed: " . $conn->error;
        }
    }
}

?>

<form method="POST" action="register.php">
    <label for="username">IC Full Name:</label><br>
    <input type="text" name="username" required><br><br>

    <label for="email">Email Address:</label><br>
    <input type="email" name="email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label for="student_id">Student ID:</label><br>
    <input type="text" name="student_id" required><br><br>

    <label for="class">Programme & Tutorial Group:</label><br>
    <input type="text" name="class" required><br><br>

    <button type="submit">Register</button>
</form>