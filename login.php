<?php
session_start();
include("config/db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM patients WHERE email='$email'";

$result = mysqli_query($con,$query);

if(mysqli_num_rows($result) == 1){

    $row = mysqli_fetch_assoc($result);

    if($row['password'] == $password){

        $_SESSION['patient_id'] = $row['patient_id'];
        header("Location: dashboard.php");

    }else{
        echo "<div class='alert alert-danger text-center'>Wrong Password ❌</div>";
    }

}else{
   echo "<div class='alert alert-danger text-center'>Email not found ❌</div>";
}

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Login</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body{
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    margin:0;
    display:flex;
    justify-content:center;
    align-items:center;

    /* Background */
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
    url('assets/images/hospital1.png');
    background-size: cover;
    background-position: center;
}

/* GLASS CARD */
.login-card{
    width: 360px;
    padding: 35px;
    border-radius: 20px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    animation: fadeIn 1s ease;
}

/* ANIMATION */
@keyframes fadeIn{
    from{opacity:0; transform: translateY(40px);}
    to{opacity:1; transform: translateY(0);}
}

/* FLOATING INPUT */
.form-group{
    position: relative;
    margin-bottom: 25px;
}

.form-group input{
    width: 100%;
    padding: 12px 40px 12px 40px;
    border-radius: 10px;
    border: none;
    outline: none;
    background: rgba(255,255,255,0.2);
    color: white;
}

.form-group label{
    position:absolute;
    top:50%;
    left:40px;
    transform: translateY(-50%);
    color:#ddd;
    font-size:14px;
    pointer-events:none;
    transition:0.3s;
}

/* FLOAT EFFECT */
.form-group input:focus + label,
.form-group input:valid + label{
    top:-8px;
    left:30px;
    font-size:12px;
    color:#fff;
}

/* ICONS */
.form-group i{
    position:absolute;
    top:50%;
    left:12px;
    transform: translateY(-50%);
    color:white;
}

/* PASSWORD EYE */
.eye-icon{
    right:12px;
    left:auto;
    cursor:pointer;
}

/* BUTTON */
.btn-login{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:none;
    background: #28a745;
    color:white;
    font-weight:bold;
    transition:0.3s;
}

.btn-login:hover{
    background:#20c997;
    transform: scale(1.05);
}

/* LOGO */
.logo{
    width:70px;
    display:block;
    margin:0 auto 10px;
}

/* TEXT */
.title{
    text-align:center;
    color:white;
    font-weight:600;
}

.subtitle{
    text-align:center;
    font-size:14px;
    color:#ddd;
    margin-bottom:20px;
}
</style>

</head>

<body>

<div class="login-card">

<img src="assets/images/logo.png" class="logo">

<h3 class="title">City Care Hospital</h3>
<p class="subtitle">Bhubaneswar, Odisha</p>

<form method="POST">

<!-- EMAIL -->
<div class="form-group">
<i class="fa fa-envelope"></i>
<input type="email" name="email" required>
<label>Email Address</label>
</div>

<!-- PASSWORD -->
<div class="form-group">
<i class="fa fa-lock"></i>
<input type="password" name="password" id="password" required>
<label>Password</label>

<i class="fa fa-eye eye-icon" onclick="togglePassword()"></i>
</div>

<button type="submit" name="login" class="btn-login">
<i class="fa fa-sign-in-alt me-1"></i> Login
</button>

</form>

<p class="text-center mt-3 text-white">
Don't have account? 
<a href="register.php" class="text-warning fw-bold">Register</a>
</p>

</div>

<script>
function togglePassword(){
    var x = document.getElementById("password");
    if(x.type === "password"){
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script>

</body>
</html>