<?php
session_start();

$role = $_SESSION['role_id'];

if ($role == 1) {
    include '../student-dashboard/student_dashboard.php';
} elseif ($role == 2) {
    include '../instructor-dashboard/instructor.php';
} elseif ($role == 3) {
    include '../admin-dashboard/admin.php';
} else {
    echo "Access Denied.";
}
?>