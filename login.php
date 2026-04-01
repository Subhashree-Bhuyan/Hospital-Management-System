<?php
session_start();
include("config/db.php");

$error_msg = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    if (empty($email) || empty($password) || empty($role)) {
        $error_msg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Enter a valid email address.";
    } else {
        if ($role == 'admin' || $role == 'doctor') {
            $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ? AND role = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "ss", $email, $role);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);

                if (password_verify($password, $row['password']) || $row['password'] === $password) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['name'] = $row['name'];

                    if ($role == 'admin') {
                        header("Location: admin/dashboard.php");
                    } else {
                        header("Location: doctor/dashboard.php");
                    }
                    exit();
                } else {
                    $error_msg = "Wrong password.";
                }
            } else {
                $error_msg = "Invalid credentials.";
            }
        } elseif ($role == 'patient') {
            $stmt = mysqli_prepare($con, "SELECT * FROM patients WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);

                if (password_verify($password, $row['password']) || $row['password'] === $password) {
                    $_SESSION['patient_id'] = $row['patient_id'];
                    $_SESSION['role'] = 'patient';
                    $_SESSION['name'] = trim($row['title'] . ' ' . $row['first_name'] . ' ' . $row['last_name']);
                    header("Location: patients/dashboard.php");
                    exit();
                } else {
                    $error_msg = "Wrong password.";
                }
            } else {
                $error_msg = "Email not found.";
            }
        } else {
            $error_msg = "Invalid role selected.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body{
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    margin:0;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/hospital1.png');
    background-size: cover;
    background-position: center;
}
.login-card{
    width: 360px;
    padding: 35px;
    border-radius: 20px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
}
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
    /* color:#ddd; */
    color: grey;
    font-size:14px;
    pointer-events:none;
    transition:0.3s;
}
.form-group input:focus + label,
.form-group input:valid + label{
    top:-8px;
    left:30px;
    font-size:12px;
    color:#fff;
}
.form-group i{
    position:absolute;
    top:50%;
    left:12px;
    transform: translateY(-50%);
    color:white;
}
.eye-icon{
    right:12px;
    left:auto;
    cursor:pointer;
}
.btn-login{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:none;
    background: #28a745;
    color:white;
    font-weight:bold;
}
.logo{
    width:70px;
    display:block;
    margin:0 auto 10px;
}
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
<?php if ($error_msg) echo "<div class='alert alert-danger text-center'>$error_msg</div>"; ?>

<div class="form-group">
<i class="fa fa-user"></i>
<select name="role" class="form-control" required style="background: rgba(255,255,255,0.2); color: grey; border: none; border-radius: 10px; padding: 12px 40px 12px 40px;">
    <option value="" disabled selected>Select Role</option>
    <option value="admin">Admin</option>
    <option value="doctor">Doctor</option>
    <option value="patient">Patient</option>
</select>
</div>

<div class="form-group">
<i class="fa fa-envelope"></i>
<input type="email" name="email" required>
<label>Email Address</label>
</div>

<div class="form-group">
<i class="fa fa-lock"></i>
<input type="password" name="password" id="password" required>
<label>Password</label>
<i style="text-align: right" class="fa fa-eye eye-icon" onclick="togglePassword()"></i>
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
    x.type = (x.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
