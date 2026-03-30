<?php
session_start();
include("../config/db.php");

/* check login */
if(!isset($_SESSION['doctor_id'])){
    header("Location: login.php");
    exit();
}

/* get data */
$id = $_GET['id'];
$status = $_GET['status'];

/* update query */
$query = "UPDATE appointments 
          SET status='$status' 
          WHERE appointment_id='$id'";

mysqli_query($con,$query);

/* redirect back */
header("Location: dashboard.php");
?>