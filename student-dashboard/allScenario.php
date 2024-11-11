<?php
$titleName = "Scenario - TARUMT Cyber Range";
include '../header_footer/header_student.php';
include '../connection.php';

//Retrieve student information
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM students INNER JOIN users ON students.user_id = users.id WHERE users.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->fetch_assoc();
$stmt->close();
?>

<style>
    .blue {
        background-color: blue;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .blue:hover {
        background-color: green;
        color: white;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Scenario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">All Scenario</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Assigned Scenario
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Scenario Name</th>
                        <th>Type of attacks</th>
                        <th>Instructor Name</th>
                        <th>Assigned Date</th>
                        <th>Due date</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Scenario Name</th>
                        <th>Type of attacks</th>
                        <th>Instructor Name</th>
                        <th>Assigned Date</th>
                        <th>Due date</th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td>SSH Attack</td>
                        <td>Command-based</td>
                        <td>Tan Yi Yang</td>
                        <td>14/8/2024</td>
                        <td>30/9/2024</td>
                        <td>
                            <a href="sshAttackAi.php" class="blue">Start Scenario</a>
                        </td>
                    </tr>
                    <tr>
                        <td>LDAP Attack</td>
                        <td>Capture The Flag (CTF)</td>
                        <td>Ian Lai Wen Kye</td>
                        <td>14/8/2024</td>
                        <td>30/9/2024</td>
                        <td>
                            <a href="ldapattack.php" class="blue">Start Scenario</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php' ?>