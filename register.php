<?php
include("config/db.php");

if(isset($_POST['register'])){

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $check = "SELECT * FROM patients WHERE email='$email'";
    $result = mysqli_query($con,$check);

    if(mysqli_num_rows($result) > 0){

        echo "Email already exists";

    }else{

        $query = "INSERT INTO patients
                (first_name,last_name,email,phone,password)
                VALUES
                ('$first_name','$last_name','$email','$phone','$password')";

        mysqli_query($con,$query);

        echo "Registration Successful. <a href='login.php'>Login Now</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- CSS -->
<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="container d-flex justify-content-center align-items-center vh-100 fade-in">

<div class="card shadow p-4 register-card">

<h3 class="text-center mb-4 register-title">
<i class="fa fa-user-plus"></i> Patient Registration
</h3>

<form method="POST">

<!-- FIRST NAME -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
<input type="text" name="first_name" class="form-control" placeholder="First Name" required>
</div>

<!-- LAST NAME -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
<input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
</div>

<!-- EMAIL -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-envelope"></i></span>
<input type="email" name="email" class="form-control" placeholder="Email Address" required>
</div>

<!-- PHONE -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-phone"></i></span>
<input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
</div>

<!-- PASSWORD -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-lock"></i></span>
<input type="password" name="password" id="password" class="form-control" placeholder="Password" required>

<span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;">
<i class="fa fa-eye"></i>
</span>
</div>

<!-- REGISTER BUTTON -->
<button type="submit" name="register" class="btn btn-success w-100">
<i class="fa fa-user-check"></i> Register Now
</button>

</form>

<p class="text-center mt-3">
Already have an account? 
<a href="login.php">Login</a>
</p>

</div>
</div>

<script>
function togglePassword() {
    var x = document.getElementById("password");
    x.type = (x.type === "password") ? "text" : "password";
}
</script>

</body>

</html>