<?php
include '../connection.php';

if (!isset($_POST['className'])) {
    echo json_encode(['error' => 'Class name is required.']);
    exit();
}

$className = $_POST['className'];

$stmt = $conn->prepare("UPDATE class SET is_archived = 0 WHERE class_name = ?");
$stmt->bind_param("s", $className);

if ($stmt->execute()) {
    echo json_encode(['message' => "Class '$className' unarchived successfully."]);
} else {
    echo json_encode(['error' => 'Failed to unarchive class.']);
}
$stmt->close();
?>
