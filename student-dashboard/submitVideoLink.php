<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// List of exercises
$exercises = [
    "sshAttackAi.php",
    "sshAttackAii.php",
    "sshAttackBi.php",
    "sshAttackBii.php",
    "sshDefendA.php",
    "sshDefendB.php",
    "sshDefendC.php"
];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $student_id = $row['id'];

        // Get current exercise ID
        if (!isset($_SESSION['current_exercise_id'])) {
            echo "<script>alert('No active exercise found. Please start an exercise.');</script>";
            exit();
        }

        $exercise_id = $_SESSION['current_exercise_id'];

        // Check if already submitted
        $stmt = $conn->prepare("SELECT * FROM submitted_videos WHERE student_id = ? AND exercise_id = ?");
        $stmt->bind_param("is", $student_id, $exercise_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User has already submitted, move to the next exercise
            $_SESSION['current_exercise']++;
            if ($_SESSION['current_exercise'] < count($exercises)) {
                $nextExercise = $exercises[$_SESSION['current_exercise']];
                echo "<script>alert('You have already submitted your work for this exercise.'); window.location.href = '$nextExercise';</script>";
            } else {
                echo "<script>alert('All exercises completed!'); window.location.href = 'completion_page.php';</script>";
            }
            exit;
        }

        // Submission logic
        $videoLink = trim($_POST['videoLink'] ?? "");
        $file = $_FILES['uploadedFile'] ?? null;

        if (empty($videoLink)) {
            echo "<script>alert('Video link is required.');</script>";
        } elseif (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('File upload is required.');</script>";
        } else {
            $allowedTypes = ['text/plain'];
            $maxFileSize = 10 * 1024 * 1024; // 10MB
            $fileType = mime_content_type($file['tmp_name']);
            $fileSize = $file['size'];

            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>alert('Only .txt files are allowed.');</script>";
            } elseif ($fileSize > $maxFileSize) {
                echo "<script>alert('File size must not exceed 10MB.');</script>";
            } else {
                // Insert video link
                $stmt = $conn->prepare("INSERT INTO submitted_videos (student_id, exercise_id, video_link) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $student_id, $exercise_id, $videoLink);

                if ($stmt->execute()) {
                    $fileContent = file_get_contents($file['tmp_name']);
                    $stmt = $conn->prepare("INSERT INTO submitted_files (student_id, exercise_id, file_name, file_content) VALUES (?, ?, ?, ?)");
                    $fileName = basename($file['name']);
                    $stmt->bind_param("isss", $student_id, $exercise_id, $fileName, $fileContent);

                    if ($stmt->execute()) {
                        echo "<script>alert('Submission successful!');</script>";
                        $_SESSION['current_exercise']++;
                        if ($_SESSION['current_exercise'] < count($exercises)) {
                            $nextExercise = $exercises[$_SESSION['current_exercise']];
                            echo "<script>window.location.href = '$nextExercise';</script>";
                        } else {
                            echo "<script>alert('All exercises completed!'); window.location.href = 'completion_page.php';</script>";
                        }
                    } else {
                        echo "<script>alert('Error saving file content: " . $conn->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('Error submitting video link: " . $conn->error . "');</script>";
                }
            }
        }
    } else {
        echo "<script>alert('Student not found!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Homework - TAR UMT Cyber Range</title>
    <link rel="icon" href="../pictures/school_logo.ico" type="image/x-icon" />
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        .center-box {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }

        .box-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .box-content h2 {
            color: #4CAF50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .box-content p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .box-content input[type="text"],
        .box-content input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .box-content button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .box-content button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="center-box">
        <div class="box-content">
            <h2>Submit Your Work</h2>
            <p>Please submit your homework with a <code>video link</code> and a <code>text file</code>.</p>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="videoLink" placeholder="Enter video link here..." required>
                <input type="file" name="uploadedFile" accept=".txt" required>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>