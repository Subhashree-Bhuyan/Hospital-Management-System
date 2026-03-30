<?php
session_start();
include("../config/db.php");

/* 🔐 admin protection */
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* check id */
if(!isset($_GET['id'])){
    echo "Invalid Request";
    exit();
}

$id = $_GET['id'];

/* 🔍 check if doctor has appointments */
$check = "SELECT * FROM appointments WHERE doctor_id='$id'";
$result = mysqli_query($con, $check);

if(mysqli_num_rows($result) > 0){
    echo "<script>alert('Cannot delete doctor. Appointments exist.'); window.location='manage_doctors.php';</script>";
    exit();
}

/* 🔍 get user_id BEFORE deleting doctor */
$getUser = mysqli_query($con, "SELECT user_id FROM doctors WHERE doctor_id='$id'");
$data = mysqli_fetch_assoc($getUser);
$user_id = $data['user_id'];

/* delete doctor */
mysqli_query($con, "DELETE FROM doctors WHERE doctor_id='$id'");

/* delete login */
mysqli_query($con, "DELETE FROM users WHERE user_id='$user_id'");

echo "<script>alert('Doctor Deleted Successfully'); window.location='manage_doctors.php';</script>";
?>