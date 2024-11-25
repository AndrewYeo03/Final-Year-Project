<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "cyberrange"; 

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the timezone for MySQL to Malaysia
$conn->query("SET time_zone = '+08:00'");

// Optionally, set PHP's timezone to Malaysia as well
date_default_timezone_set('Asia/Kuala_Lumpur');

// Use this line to confirm the connection if needed:
// echo "Connected successfully";
?>
