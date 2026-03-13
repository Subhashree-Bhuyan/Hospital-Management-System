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