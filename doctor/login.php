<?php
session_start();
include("../config/db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT users.*, doctors.doctor_id 
              FROM users 
              JOIN doctors ON users.user_id = doctors.user_id
              WHERE users.email='$email' 
              AND users.password='$password'
              AND users.role='doctor'";

    $result = mysqli_query($con,$query);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['doctor_id'] = $row['doctor_id'];

        header("Location: dashboard.php");

    }else{
        echo "Invalid Login";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Login</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
    height: 100vh;

    background: 
        linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)),
        url('../assets/images/doctor.png');

    background-size: 70%;
    background-position: center;
    background-repeat: no-repeat;

    display: flex;
    justify-content: center;
    align-items: center;

    font-family: 'Segoe UI', sans-serif;
}

.card {
    animation: fadeIn 0.8s ease;
}


@keyframes fadeIn {
    from {opacity:0; transform: translateY(20px);}
    to {opacity:1; transform: translateY(0);}
}
</style>
</head>
<body class="doctor-bg">

<div class="container d-flex justify-content-center align-items-center vh-100 login-wrapper">

<div class="login-card">

<div class="text-center mb-3">

    <img src="../assets/images/logo.png" 
    style="width:70px;height:70px;border-radius:50%;margin-bottom:10px;">

    <h2 class="fw-bold">Doctor's Portal</h2>

    <p class="text-muted small">
        Secure access for authorized medical professionals
    </p>

</div>




<p class="text-center mb-4">
Secure access for authorized medical professionals
</p>

<form method="POST">

<div class="form-group mb-3">
<i class="fa fa-envelope"></i>
<input type="email" name="email" class="form-control rounded-pill ps-5" required>
<label>Email Address</label>
</div>

<div class="form-group mb-3">
<i class="fa fa-lock"></i>
<input type="password" name="password" id="password" class="form-control rounded-pill ps-5 pe-5" required>
<label>Password</label>
</div>

<div class="d-flex justify-content-between mb-3">
<label><input type="checkbox"> Remember me</label>
<a href="#">Forgot Password?</a>
</div>

<button type="submit" name="login" class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow-sm">
    Login to Dashboard
</button>

</form>

</div>
</div>
</html>