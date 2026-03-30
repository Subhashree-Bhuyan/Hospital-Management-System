<?php
session_start();
include("../config/db.php");

/* 🔐 Admin Protection */
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* 📊 Fetch counts */
$doctor_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM doctors"));
$patient_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM patients"));
$appointment_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM appointments"));
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<!-- Title -->
<h2 class="mb-4">Admin Dashboard</h2>

<!-- Navigation -->
<div class="mb-4">
    <a href="dashboard.php" class="btn btn-dark btn-sm">Dashboard</a>
    <a href="manage_doctors.php" class="btn btn-primary btn-sm">Doctors</a>
    <a href="add_doctor.php" class="btn btn-success btn-sm">Add Doctor</a>
    <a href="add_department.php" class="btn btn-info btn-sm">Departments</a>
    <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
</div>

<!-- Cards -->
<div class="row">

    <!-- Doctors -->
    <div class="col-md-4">
        <div class="card text-center shadow">
            <div class="card-body">
                <h5>Total Doctors</h5>
                <h2><?php echo $doctor_count; ?></h2>
            </div>
        </div>
    </div>


    <!-- Patients -->
    <div class="col-md-4">
        <div class="card text-center shadow">
            <div class="card-body">
                <h5>Total Patients</h5>
                <h2><?php echo $patient_count; ?></h2>
            </div>
        </div>
    </div>

    <!-- Appointments -->
    <div class="col-md-4">
        <div class="card text-center shadow">
            <div class="card-body">
                <h5>Total Appointments</h5>
                <h2><?php echo $appointment_count; ?></h2>
            </div>
        </div>
    </div>

</div>

</div>

</body>
</html>