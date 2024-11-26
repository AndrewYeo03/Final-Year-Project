<?php
$titleName = "Instructor List - TAR UMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

//Retrieve unarchvie intructor infos
$sql = "SELECT instructors.id, users.username, users.email, instructors.instructor_id, instructors.faculty 
        FROM instructors
        JOIN users ON instructors.user_id = users.id
        WHERE users.role_id = 2 AND instructors.is_archived = 0"; //Only display unarchive instructor
$result = $conn->query($sql);
?>

<div class="container-fluid px-4">
    <h2 class="mt-4">Instructor</h2>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Existing Instructor Listing</li>
    </ol>

    <!-- Display all unarchive instructor data -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Instructor
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Instructor ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Faculty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Instructor ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Faculty</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['instructor_id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['faculty']; ?></td>
                            <td>
                                <!-- Edit Button -->
                                <a href="editInstructor.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <!-- Archive Button -->
                                <a href="archive_instructor.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Archive</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../header_footer/footer.php'; ?>
