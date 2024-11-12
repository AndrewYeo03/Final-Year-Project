<?php
$titleName = "Rate Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

//Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM students INNER JOIN users ON students.user_id = users.id WHERE users.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();
$stmt->close();
?>



<?php include '../header_footer/footer.php' ?>