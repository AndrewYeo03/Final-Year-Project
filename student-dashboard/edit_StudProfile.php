<?php
$titleName = "Update Profile - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

// 获取学生ID
$username = $_SESSION['username'];

// 获取学生信息
$stmt = $conn->prepare("
    SELECT u.username, u.email, s.student_id 
    FROM users u 
    INNER JOIN students s ON u.id = s.user_id 
    WHERE u.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// 如果表单提交，更新资料
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    // 如果密码更新了，先加密密码
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE username = ?");
        $update_stmt->bind_param("sss", $new_email, $hashed_password, $username);
    } else {
        $update_stmt = $conn->prepare("UPDATE users SET email = ? WHERE username = ?");
        $update_stmt->bind_param("ss", $new_email, $username);
    }

    if ($update_stmt->execute()) {
        echo "<p class='success-message'>Profile updated successfully!</p>";
    } else {
        echo "<p class='error-message'>Error updating profile. Please try again.</p>";
    }
    $update_stmt->close();
}

?>

<div class="container">
    <h2>Update Profile</h2>
    <form method="POST" id="updateForm" action="update_profile.php" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" required readonly disabled>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required readonly disabled>
        </div>

        <div class="form-group">
            <label for="password">New Password (Leave blank if no change):</label>
            <input type="password" id="password" name="password" placeholder="Enter a new password" onkeyup="checkPasswordStrength()">
            <div id="password-strength-status"></div>
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirm New Password:</label>
            <input type="password" id="password-confirm" name="password-confirm" placeholder="Re-enter your password">
            <span id="password-match-status"></span>
        </div>

        <div>
            <button type="submit" id="updateProfile">Update Profile</button>
        </div>
    </form>
</div>

<script>
    // Password validation
    function validatePassword(password) {
        const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{6,}$/;

        // Checks compliance with basic complexity requirements
        if (!complexityRegex.test(password)) {
            return false;
        }

        // Checks if it contains characters in order or reverse order
        for (let i = 0; i < password.length - 2; i++) {
            const char1 = password.charCodeAt(i);
            const char2 = password.charCodeAt(i + 1);
            const char3 = password.charCodeAt(i + 2);

            // Check sequence (such as 123 or abc)
            if (char2 === char1 + 1 && char3 === char2 + 1) {
                return false;
            }

            // Check for reverse order (such as 321 or cba)
            if (char2 === char1 - 1 && char3 === char2 - 1) {
                return false;
            }
        }

        return true;
    }

    // Display password strength status
    function checkPasswordStrength() {
        var password = document.getElementById('password').value;
        var strengthStatus = document.getElementById('password-strength-status');

        if (password.length < 6) {
            strengthStatus.innerHTML = "Weak password (minimum 6 characters).";
            strengthStatus.style.color = "red";
        } else if (validatePassword(password)) {
            strengthStatus.innerHTML = "Strong password!";
            strengthStatus.style.color = "green";
        } else {
            strengthStatus.innerHTML = "Password does not meet complexity requirements.";
            strengthStatus.style.color = "orange";
        }
    }

    // Check if passwords match
    function checkPasswordMatch() {
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('password-confirm').value;
        var matchStatus = document.getElementById('password-match-status');

        if (password !== confirmPassword) {
            matchStatus.innerHTML = "Passwords do not match.";
            matchStatus.style.color = "red";
        } else {
            matchStatus.innerHTML = "Passwords match.";
            matchStatus.style.color = "green";
        }
    }

    // Form validation
    function validateForm() {
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('password-confirm').value;

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }

        return true;
    }

    document.getElementById('password-confirm').addEventListener('keyup', checkPasswordMatch);
</script>

<style>
    /* General styling */
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    h2 {
        text-align: center;
    }

    form[id="updateForm"] {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-size: 14px;
        color: #333;
    }

    input[id="username"],
    input[id="email"],
    input[id="password"], 
    input[id="password-confirm"]{
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    button[id="updateProfile"] {
        width: 100%;
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }

    #password-strength-status {
        margin-top: 5px;
    }

    #password-match-status {
        font-size: 14px;
        margin-top: 5px;
    }

    /* Success/Error messages */
    .success-message {
        color: green;
        font-weight: bold;
        text-align: center;
    }

    .error-message {
        color: red;
        font-weight: bold;
        text-align: center;
    }
</style>

<?php
include '../header_footer/footer.php';
?>