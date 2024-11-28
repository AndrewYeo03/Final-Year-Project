<?php  
$titleName = "Admin Dashboard - TARUMT Cyber Range";
include '../header_footer/header_admin.php';
include '../connection.php';

// Fetch data from the database
// Fetch total counts for various entities
$totalUsersQuery = "SELECT COUNT(*) AS total FROM users";
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, $totalUsersQuery))['total'];

$totalStudentsQuery = "SELECT COUNT(*) AS total FROM students";
$totalStudents = mysqli_fetch_assoc(mysqli_query($conn, $totalStudentsQuery))['total'];

$totalInstructorsQuery = "SELECT COUNT(*) AS total FROM instructors";
$totalInstructors = mysqli_fetch_assoc(mysqli_query($conn, $totalInstructorsQuery))['total'];

$totalClassesQuery = "SELECT COUNT(*) AS total FROM class";
$totalClasses = mysqli_fetch_assoc(mysqli_query($conn, $totalClassesQuery))['total'];

// Scenario distribution data
$scenarioDistributionQuery = "SELECT COUNT(scenario_id) AS count, due_date FROM scenario GROUP BY due_date";
$scenarioDistribution = mysqli_query($conn, $scenarioDistributionQuery);
$scenarioLabels = [];
$scenarioCounts = [];
while ($row = mysqli_fetch_assoc($scenarioDistribution)) {
    $scenarioLabels[] = $row['due_date'];
    $scenarioCounts[] = $row['count'];
}

// Fetch all scenario details for the table
$scenarioDetailsQuery = "SELECT title, description, assigned_date, due_date FROM scenario";
$scenarioDetails = mysqli_query($conn, $scenarioDetailsQuery);

// User role distribution data
$roleDistributionQuery = "SELECT roles.role_name, COUNT(users.role_id) AS count 
                          FROM roles 
                          LEFT JOIN users ON roles.id = users.role_id 
                          GROUP BY roles.role_name";
$roleLabels = [];
$roleCounts = [];
$roleResults = mysqli_query($conn, $roleDistributionQuery);
while ($row = mysqli_fetch_assoc($roleResults)) {
    $roleLabels[] = $row['role_name'];
    $roleCounts[] = $row['count'];
}

// Fetch all users for the User List table
$userListQuery = "SELECT users.id, users.username, users.email, roles.role_name 
                  FROM users 
                  LEFT JOIN roles ON users.role_id = roles.id";
$userList = mysqli_query($conn, $userListQuery);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}
.table-hover tbody tr:hover {
    background-color: #f1f1f1;
}
.table-bordered th, .table-bordered td {
    border: 1px solid #ddd;
}
.table th, .table td {
    text-align: center;
    vertical-align: middle;
}
.table-dark {
    background-color: #343a40;
    color: white;
}
#scenariosBarChart, #userRolesPieChart {
        height: 300px;  /* Adjust the height as needed */
        width: 100%;
    }

</style>
<div class="container-fluid px-4">
    <h1 class="mt-4">Admin Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Welcome back, <?php echo htmlspecialchars($username); ?></li>
    </ol>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">Total Users: <?php echo $totalUsers; ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">Total Students: <?php echo $totalStudents; ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">Total Instructors: <?php echo $totalInstructors; ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">Total Classes: <?php echo $totalClasses; ?></div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Bar Chart for Scenarios -->
        <div class="col-xl-6 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i>
                    Scenarios Distribution by Due Date
                </div>
                <div class="card-body">
                    <canvas id="scenariosBarChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Pie Chart for User Roles -->
        <div class="col-xl-6 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i>
                    User Role Distribution
                </div>
                <div class="card-body">
                    <canvas id="userRolesPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- User List Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table"></i>
            User List
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($userList)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scenarios Table -->
    <div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table"></i>
        Scenario Details
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Assigned Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($scenario = mysqli_fetch_assoc($scenarioDetails)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($scenario['title']); ?></td>
                        <td><?php echo htmlspecialchars($scenario['description']); ?></td>
                        <td><?php echo date('d M Y', strtotime($scenario['assigned_date'])); ?></td>
                        <td><?php echo date('d M Y', strtotime($scenario['due_date'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<script>
    // Bar Chart for Scenarios
    var scenariosCtx = document.getElementById('scenariosBarChart').getContext('2d');
    var scenariosBarChart = new Chart(scenariosCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($scenarioLabels); ?>,
            datasets: [{
                label: 'Number of Scenarios',
                data: <?php echo json_encode($scenarioCounts); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Pie Chart for User Role Distribution
    var rolesCtx = document.getElementById('userRolesPieChart').getContext('2d');
    var rolesPieChart = new Chart(rolesCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($roleLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($roleCounts); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                hoverOffset: 4
            }]
        }
    });
</script>
</html>

<?php include '../header_footer/footer.php'; ?>