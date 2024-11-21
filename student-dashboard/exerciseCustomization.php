<?php
$titleName = "Exploitation of SSH (Secure Shell) Protocol - TARUMT Cyber Range";
include '../connection.php';
include '../header_footer/header_student.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="../css/questionLayout.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .center-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
            background-color: #f8f9fa;
        }

        .info-card {
            text-align: center;
            background-color: #ffffff;
            padding: 50px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }

        .info-card h1 {
            font-size: 24px;
            color: #333;
        }

        .info-card p {
            font-size: 16px;
            color: #555;
            margin: 10px 0;
        }

        .info-card button {
            margin-top: 15px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .info-card button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <div class="center-content">
        <div class="info-card">
            <h1>Custom Exercise Required</h1>
            <p>It seems this exercise page requires customization by the administrator.</p>
            <p>Please contact the admin for further assistance and to customize this page according to your needs.</p>
            <button onclick="window.location.href='mailto:admin@tarumt.edu.my';">Contact Admin</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
</body>

</html>
