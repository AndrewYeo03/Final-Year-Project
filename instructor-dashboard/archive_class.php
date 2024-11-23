<?php
include '../connection.php';

//Checking POST data
if (!isset($_POST['className'])) {
    echo json_encode(['error' => 'Class name is required.']);
    exit();
}

$className = $_POST['className'];

//Update the database to mark the course as archived
$stmt = $conn->prepare("UPDATE class SET is_archived = 1 WHERE class_name = ?");
$stmt->bind_param("s", $className);

if ($stmt->execute()) {
    echo json_encode(['message' => "Class '$className' archived successfully."]);
} else {
    echo json_encode(['error' => 'Failed to archive class.']);
}
$stmt->close();
?>
