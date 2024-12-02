<?php
$titleName = "Create Instructor - TAR UMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Processing logic after submitting the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $instructor_id = strtoupper($_POST['instructor_id']);
    $faculty = "Faculty of Computing and Information Technology (FOCS)";

    // Check if a field is empty
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($instructor_id)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirmPassword) {
        $error = "The password and confirm password do not match!";
    } else {
        // Check if email already exists
        $emailCheckQuery = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($emailCheckQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email address has been registered!";
        } else {

            // Check if instructor id already exists
            $instructorIDCheckQuery = "SELECT id FROM instructors WHERE instructor_id = ?";
            $stmt = $conn->prepare($instructorIDCheckQuery);
            $stmt->bind_param("s", $instructor_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "This instructor ID has already existed!";
            } else {
                // Start transaction
                $conn->begin_transaction();
                try {
                    // Insert into users table
                    $hashed_password = md5($password);
                    $insertUserQuery = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, (SELECT id FROM roles WHERE role_name = 'instructor'))";
                    $stmt = $conn->prepare($insertUserQuery);
                    $stmt->bind_param("sss", $username, $email, $hashed_password);
                    if (!$stmt->execute()) {
                        throw new Exception("Unable to insert User information: " . $stmt->error);
                    }
                    $user_id = $stmt->insert_id;
                    $stmt->close();

                    // Insert into instructors table
                    $insertInstructorQuery = "INSERT INTO instructors (user_id, instructor_id, faculty) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($insertInstructorQuery);
                    $stmt->bind_param("iss", $user_id, $instructor_id, $faculty);
                    if (!$stmt->execute()) {
                        throw new Exception("Unable to insert Instructor information: " . $stmt->error);
                    }
                    $stmt->close();

                    // Commit transaction
                    $conn->commit();

                    // Send email notification to users using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                        $mail->SMTPAuth = true;
                        $mail->Username = 'tarumtcyberrange@gmail.com'; // Your Gmail address
                        $mail->Password = 'vppiisklkqaqozeb'; // Your Gmail app password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Recipients
                        $mail->setFrom('no-reply@tarumt-cyber-range.com', 'TAR UMT Cyber Range');
                        $mail->addAddress($email); // Add recipient

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = "Welcome to TAR UMT Cyber Range";
                        $mail->Body = "
                            Dear $username,<br><br>
                            Your account has been successfully created.<br><br>
                            <strong>Username:</strong> $email<br>
                            <strong>Password:</strong> $password<br><br>
                            You may change your password after your first login.<br><br>
                            Best regards,<br>
                            <strong>TARUMT Cyber Range Team</strong>
                        ";

                        $mail->send();
                        $success = "Instructor created successfully! Email has been sent to $email.";
                    } catch (Exception $e) {
                        throw new Exception("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
                    }
                } catch (Exception $e) {
                    // Rollback transaction
                    $conn->rollback();
                    $error = $e->getMessage();
                }
            }
        }
    }
}
?>

<script>
    //Check password complexity
    function validatePassword(password) {
        const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{6,}$/;

        //Checks compliance with basic complexity requirements
        if (!complexityRegex.test(password)) {
            return false;
        }

        //Checks if it contains characters in order or reverse order
        for (let i = 0; i < password.length - 2; i++) {
            const char1 = password.charCodeAt(i);
            const char2 = password.charCodeAt(i + 1);
            const char3 = password.charCodeAt(i + 2);

            //Check sequence (such as 123 or abc)
            if (char2 === char1 + 1 && char3 === char2 + 1) {
                return false;
            }

            //Check for reverse order (such as 321 or cba)
            if (char2 === char1 - 1 && char3 === char2 - 1) {
                return false;
            }
        }

        return true;
    }

    //Form Validation
    function validateForm(event) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (password !== confirmPassword) {
            alert('Password and Confirm Password do not match!');
            event.preventDefault();
            return false;
        }

        if (!validatePassword(password)) {
            alert('The password must be at least 6 characters long, contain uppercase, lowercase, numbers, and symbols, and cannot contain three consecutive characters in order or reverse order (e.g., "123" or "cba")!');
            event.preventDefault();
            return false;
        }
    }
</script>

<div class="container-fluid px-4">
    <h2 class="mt-4">Create Instructor</h2>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Instructor</li>
    </ol>

    <!-- Display error or success information -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="" onsubmit="validateForm(event)">
        <div class="mb-3">
            <label for="username" class="form-label">Instructor Name:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password:</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
        </div>

        <div class="mb-3">
            <label for="instructor_id" class="form-label">Instructor ID:</label>
            <input type="text" class="form-control" id="instructor_id" name="instructor_id" required>
        </div>

        <div class="mb-3">
            <label for="faculty" class="form-label">Faculty:</label>
            <input type="text" class="form-control" id="faculty" name="faculty" value="Faculty of Computing and Information Technology (FOCS)" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Create Instructor</button>
    </form>
</div>

<?php include '../header_footer/footer.php'; ?>