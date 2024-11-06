<?php
session_start();
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $videoLink = $_POST['videoLink'];

    $user_id = $_SESSION['user_id'];

    
    $sql = "SELECT student_id FROM students WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $student_id = $row['student_id'];
        
        $sql = "INSERT INTO video (student_id, video_link) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $student_id, $videoLink);
        
        if ($stmt->execute()) {
            echo "<script>alert('Video link submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error submitting video link: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Student not found!');</script>";
    }

    // Increment the current exercise index if not completed
    if (!isset($_SESSION['current_exercise'])) {
        $_SESSION['current_exercise'] = 0;
    } else {
        $_SESSION['current_exercise']++;
    }

    // List of exercises
    $exercises = [
        "sshAttackAi.php",  // Exercise 1
        "sshAttackAii.php", // Exercise 2
        "sshAttackBi.php",  // Exercise 3
        "sshAttackBii.php", // Exercise 4
        "sshDefendA.php",   // Exercise 5
        "sshDefendB.php",   // Exercise 6
        "sshDefendC.php"    // Exercise 7
    ];

    // Redirect to next exercise if it exists
    if ($_SESSION['current_exercise'] < count($exercises)) {
        header("Location: " . $exercises[$_SESSION['current_exercise']]);
        exit();
    } else {
        echo "<h1>All exercises completed!</h1>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations!</title>
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Centered container styling */
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

        .box-content input[type="text"] {
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
            <h2>Congratulations!</h2>
            <p>Your flags are correct. Please submit your homework <code>(Video Link)</code>.</p>
            <form action="" method="POST">
                <input type="text" name="videoLink" placeholder="Enter video link here..." required>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>
