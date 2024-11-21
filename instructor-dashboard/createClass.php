<?php
$titleName = "Create Class - TARUMT Cyber Range";
include  '../header_footer/header_instructor.php';
include '../connection.php';

// Retrieve instructor information
$username = $_SESSION['username'];
$stmt = $conn->prepare("
    SELECT i.id AS id
    FROM instructors i
    INNER JOIN users u ON i.user_id = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$instructorData = $result->fetch_assoc();
$stmt->close();

// Get instructor ID
$instructorId = $instructorData['id'];

//Auto generate class_code
function generateClassCode($length = 7)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return strtoupper($randomString);
}

//Handling form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $className = strtoupper(trim($_POST['class_name']));
    $description = strtoupper(trim($_POST['description']));
    $classCode = generateClassCode();

    $stmt = $conn->prepare("
        INSERT INTO class (class_name, description, class_code, created_by) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("sssi", $className, $description, $classCode, $instructorId);

    if ($stmt->execute()) {
        $success_message = "Class created successfully! Class Code: <strong>$classCode</strong>";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<style>
    body {
        background: #f7f9fc;
        font-family: 'Arial', sans-serif;
    }

    h2 {
        color: #2c3e50;
    }

    form {
        max-width: 600px;
        margin: auto;
    }

    .btn-primary {
        background-color: #2980b9;
        border-color: #2980b9;
        color: white;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #1f618d;
        border-color: #1f618d;
    }

    .was-validated .btn-primary:invalid {
        background-color: #2980b9;
        border-color: #2980b9;
    }

    .card {
        max-width: 600px;
        margin: auto;
    }

    .invalid-feedback {
        color: red;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4" style="text-align: center;">Create a New Class</h1>
    <p class="text-muted text-center">Organize your students with unique class structures</p>

    <!-- Display success or error message -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
    <?php elseif (!empty($error_message)): ?>
        <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Form Section -->
    <form action="" method="POST" class="shadow p-4 bg-white rounded" id="createClassForm">
        <div class="mb-3">
            <label for="class_name" class="form-label">Class Name</label>
            <input type="text" class="form-control" id="class_name" name="class_name" required placeholder="E.g. RIS3S2G7" oninput="updatePreview()">
            <div class="invalid-feedback">Please provide a class name.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Class Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="E.g. Bachelor of Information Technology(Honurs) in Information Security - Year 3 Semester 2 Group 7" oninput="updatePreview()"></textarea>
            <div class="invalid-feedback">Please provide a class description.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Create Class</button>
    </form>

    <!-- Class Preview Section -->
    <div class="mt-5">
        <h4 class="text-center">Class Preview</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title" id="previewClassName">Class Name Preview</h5>
                <p class="card-text" id="previewDescription">Class Description Preview</p>
                <p class="card-text"><small class="text-muted">Class Code will be auto-generated upon creation.</small></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('createClassForm').addEventListener('submit', function(event) {
        let form = this;
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        }
    });

    // Update Class Preview
    function updatePreview() {
        const className = document.getElementById('class_name').value;
        const description = document.getElementById('description').value;

        document.getElementById('previewClassName').innerText = className || 'Class Name Preview';
        document.getElementById('previewDescription').innerText = description || 'Class Description Preview';
    }
</script>

<?php include '../header_footer/footer.php' ?>