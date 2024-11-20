<?php include  '../header_footer/header_instructor.php' ?>

<style>
    button.blue {
        background-color: blue;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }

    button.green {
        background-color: green;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }
</style>

<div class="container-fluid px-4">
    <!--Place your main content here -->
    <h1 class="mt-4">Student Response</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Student Response</li>
    </ol>
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Daily Submission of Scenario
                </div>
                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Monthly Submission of Scenario
                </div>
                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Submission of Scenario
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Owned Group</th>
                        <th>Scenario</th>
                        <th>Assigned Date</th>
                        <th>Submission date</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Student Name</th>
                        <th>Owned Group</th>
                        <th>Scenario</th>
                        <th>Assigned Date</th>
                        <th>Submission date</th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td>Yeo Jun Ken</td>
                        <td>RIS3_Group7</td>
                        <td>SSH Attack</td>
                        <td>14/8/2024</td>
                        <td>14/8/2024</td>
                        <td>
                            <button class="blue">Recording Video</button>
                            <button class="green">Command Logs</button>
                            Progress : 100%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>



</div>

<!-- chart -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<!-- Area chart demo-->
<script src="assets/demo/chart-area-demo.js"></script>
<!-- Bar chart demo-->
<script src="assets/demo/chart-bar-demo.js"></script>
<!-- Table chart -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>

<?php include '../header_footer/footer.php' ?>