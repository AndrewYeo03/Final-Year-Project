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

// Use this line to confirm the connection if needed:
// echo "Connected successfully";
?>