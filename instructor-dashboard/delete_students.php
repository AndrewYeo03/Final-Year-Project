<?php
include '../connection.php';

$studentId = $_POST['studentId'];
$className = $_POST['className'];

$stmt = $conn->prepare("
    SELECT id 
    FROM students 
    WHERE student_id = ?
");
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$id = $result->fetch_assoc();
$stmt->close();

if (empty($id) || empty($className)) {
    echo json_encode(["error" => "Invalid input data."]);
    exit;
} else {
    $stmt = $conn->prepare("DELETE FROM student_classes WHERE student_id = ? AND class_name = ?");
    $stmt->bind_param("is", $id['id'], $className);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        echo json_encode(["error" => "No matching record found to delete."]);
        exit;
    }

    echo json_encode(["message" => "Student deleted successfully."]);
}
