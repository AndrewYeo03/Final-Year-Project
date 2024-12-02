<?php
$titleName = "Scenario List";
include '../connection.php';
include '../header_footer/header_instructor.php';

// Fetch all scenarios
$query = "SELECT * FROM scenario";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titleName; ?></title>
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .mainContent {
            padding: 30px 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-add {
            background-color: #28a745;
            color: white;
        }

        .btn-edit {
            background-color: #ffc107;
            color: white;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .page-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 10px 15px;
        }

        .page-title span {
            display: inline-block;
            color: #000000;
            border-bottom: 2px solid #000000;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="mainContent">
        <div class="page-title">
            <span><?php echo $titleName; ?></span>
        </div>
        <div class="mainContent">
            <a href="addScenario.php" class="btn btn-add">Add New Scenario</a>
            <table>
                <thead>
                    <tr>
                        <th>Scenario ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['scenario_id']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['assigned_date']; ?></td>
                                <td><?php echo $row['due_date']; ?></td>
                                <td>
                                    <a href="viewScenario.php?scenario_id=<?php echo $row['scenario_id']; ?>" class="btn btn-view">View</a>
                                    <a href="editScenario.php?scenario_id=<?php echo $row['scenario_id']; ?>" class="btn btn-edit">Edit</a>
                                    <a href="deleteScenario.php?scenario_id=<?php echo $row['scenario_id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this scenario? \n\nAttention: All the exercises will be deleted as well !');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No scenarios found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<?php include '../header_footer/footer.php' ?>