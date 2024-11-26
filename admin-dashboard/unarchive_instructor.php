<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['instructorId'])) {
    $instructorId = $_POST['instructorId'];

    //Update the instructor's archive status
    $stmt = $conn->prepare("UPDATE instructors SET is_archived = 0 WHERE id = ?");
    $stmt->bind_param("i", $instructorId);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to unarchive instructor"]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
