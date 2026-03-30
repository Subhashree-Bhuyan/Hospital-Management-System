<?php
session_start();
include("../config/db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users 
              WHERE email='$email' 
              AND password='$password'
              AND role='admin'";

    $result = mysqli_query($con,$query);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role'] = $row['role'];

        header("Location: dashboard.php");

    }else{
        echo "Invalid Admin Login";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h3>Admin Login</h3>

<form method="POST">

<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

<button type="submit" name="login" class="btn btn-success">
Login
</button>

</form>

</div>

</body>
</html>