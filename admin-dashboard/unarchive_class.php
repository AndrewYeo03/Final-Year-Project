<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['className'])) {
    $className = $_POST['className'];

    //Update the class's archive status
    $stmt = $conn->prepare("UPDATE class SET is_archived = 0 WHERE class_name = ?");
    $stmt->bind_param("s", $className);
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
