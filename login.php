<?php
session_start();
include("config/db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM patients WHERE email='$email'  AND password='$password'";

    $result = mysqli_query($con,$query);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['patient_id'] = $row['patient_id'];

        header("Location: index.php");

    }else{

        echo "Invalid Email or Password";

    }

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
    <h2>Patient Login</h2>

    <form method="POST">

    <label>Email</label><br>
    <input type="email" name="email" required>
    <br><br>

    <label>Password</label><br>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit" name="login">
    Login
    </button>

    </form>
</div>
</body>
</html>