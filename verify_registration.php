<?php
include('connection.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $sql = "SELECT * FROM users WHERE verification_token = '$token'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Activate the user account
        $sqlUpdate = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = '$token'";
        if ($conn->query($sqlUpdate) === TRUE) {
            echo "<script>alert('Your email has been verified successfully!'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Failed to verify your email. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location.href = 'register.php';</script>";
    }
} else {
    echo "<script>alert('No token provided.'); window.location.href = 'register.php';</script>";
}
?>