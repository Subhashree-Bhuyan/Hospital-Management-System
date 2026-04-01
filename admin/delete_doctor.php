<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_doctors.php?msg=invalid");
    exit();
}

$doctor_id = (int) $_GET['id'];

/* check if doctor exists */
$doctor_check = mysqli_query($con, "SELECT doctor_id, user_id FROM doctors WHERE doctor_id='$doctor_id' LIMIT 1");
$doctor = mysqli_fetch_assoc($doctor_check);

if (!$doctor) {
    header("Location: manage_doctors.php?msg=notfound");
    exit();
}

/* protect delete if doctor has appointments */
$appointment_check = mysqli_query($con, "SELECT appointment_id FROM appointments WHERE doctor_id='$doctor_id' LIMIT 1");

if (mysqli_num_rows($appointment_check) > 0) {
    header("Location: manage_doctors.php?msg=hasappointments");
    exit();
}

/* if no appointments, delete doctor */
mysqli_query($con, "DELETE FROM doctors WHERE doctor_id='$doctor_id' LIMIT 1");

/* optional: delete login user also */
if (!empty($doctor['user_id'])) {
    mysqli_query($con, "DELETE FROM users WHERE user_id='" . (int)$doctor['user_id'] . "' LIMIT 1");
}

header("Location: manage_doctors.php?msg=deleted");
exit();
?>
