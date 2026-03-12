<?php
include("config/db.php");

if(isset($_POST['register'])){

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $query = "INSERT INTO patients
              (first_name,last_name,email,phone,password)
              VALUES
              ('$first_name','$last_name','$email','$phone','$password')";

    mysqli_query($con,$query);

    echo "Registration Successful. <a href='login.php'>Login Now</a>";
}
?>

<h2>Patient Registration</h2>

<form method="POST">

<label>First Name</label><br>
<input type="text" name="first_name" required><br><br>

<label>Last Name</label><br>
<input type="text" name="last_name" required><br><br>

<label>Email</label><br>
<input type="email" name="email" required><br><br>

<label>Phone</label><br>
<input type="text" name="phone" required><br><br>

<label>Password</label><br>
<input type="password" name="password" required><br><br>

<button type="submit" name="register">
Register
</button>

</form>