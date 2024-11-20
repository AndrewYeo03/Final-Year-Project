<?php
include '../connection.php';

$studentId = $_POST['studentId'];
$className = $_POST['className'];


//Ensure student exist
$stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["error" => "Student does not exist."]);
    exit;
}

$studentId = $result->fetch_assoc()['id'];

//Insert into student_classes table
$sql = "INSERT INTO student_classes (student_id, class_name) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $studentId, $className);
$stmt->execute();
echo json_encode(["message" => "Student added successfully."]);
?>
