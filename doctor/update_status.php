<?php
session_start();
include("../config/db.php");

/* check doctor login */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$doc_query = "SELECT doctor_id FROM doctors WHERE user_id='$user_id'";
$doc_result = mysqli_query($con, $doc_query);
$doc_row = mysqli_fetch_assoc($doc_result);
$doctor_id = $doc_row['doctor_id'];

/* get data */
$id = $_GET['id'];
$status = $_GET['status'];

/* make sure doctor owns appointment */
$check_q = "SELECT * FROM appointments WHERE appointment_id='$id' AND doctor_id='$doctor_id'";
$check_r = mysqli_query($con, $check_q);
if(mysqli_num_rows($check_r) != 1){
    header("Location: dashboard.php");
    exit();
}

/* update query */
$query = "UPDATE appointments 
          SET status='$status' 
          WHERE appointment_id='$id' AND doctor_id='$doctor_id'";
mysqli_query($con,$query);

/* redirect back */
header("Location: dashboard.php");
exit();
?>