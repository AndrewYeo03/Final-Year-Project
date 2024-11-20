<?php
include '../connection.php';

$className = $_POST['className'];


$stmt = $conn->prepare("
    SELECT s.student_id, u.username AS student_name 
    FROM student_classes sc
    JOIN students s ON sc.student_id = s.id
    JOIN users u ON s.user_id = u.id
    WHERE sc.class_name = ?
    ORDER BY s.student_id
");
$stmt->bind_param("s", $className);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row; 
}

echo json_encode(['students' => $students]);
?>