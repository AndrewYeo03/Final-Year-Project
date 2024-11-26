<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['studentId'])) {
    $studentId = $_POST['studentId'];

    //Update the instructor's archive status
    $stmt = $conn->prepare("UPDATE students SET is_archived = 0 WHERE student_id = ?");
    $stmt->bind_param("s", $studentId);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to unarchive student"]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
