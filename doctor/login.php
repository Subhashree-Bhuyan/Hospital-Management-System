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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">
<h2>Doctor Login</h2>

<form method="POST">

<label>Email</label>
<input type="email" name="email" class="form-control" required>

<br>

<label>Password</label>
<input type="password" name="password" class="form-control" required>

<br>

<button type="submit" name="login" class="btn btn-success">Login</button>

</form>

</div>

</body>
</html>