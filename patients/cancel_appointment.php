<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php?msg=invalid");
    exit();
}

$appointment_id = (int) $_GET['id'];

$check_query = "SELECT appointment_id, status 
                FROM appointments 
                WHERE appointment_id='$appointment_id' 
                AND patient_id='$patient_id'
                LIMIT 1";
$check_result = mysqli_query($con, $check_query);
$appointment = mysqli_fetch_assoc($check_result);

if (!$appointment) {
    header("Location: dashboard.php?msg=notfound");
    exit();
}

if ($appointment['status'] !== 'Pending') {
    header("Location: dashboard.php?msg=notallowed");
    exit();
}

$update_query = "UPDATE appointments 
                 SET status='Cancelled' 
                 WHERE appointment_id='$appointment_id' 
                 AND patient_id='$patient_id'
                 LIMIT 1";

if (mysqli_query($con, $update_query)) {
    header("Location: dashboard.php?msg=cancelled");
    exit();
} else {
    header("Location: dashboard.php?msg=error");
    exit();
}
?>
