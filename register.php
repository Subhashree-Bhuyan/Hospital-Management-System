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
<!-- TITLE -->
<div class="mb-3 input-group">
  <span class="input-group-text"><i class="fa fa-user"></i></span>
  <select name="title" id="title" class="form-select" required>
    <option value="" selected disabled>Title</option>
    <option value="Mr">Mr</option>
    <option value="Mrs">Mrs</option>
    <option value="Miss">Miss</option>
    <option value="Ms">Ms</option>
    <option value="Other">Other</option>
  </select>
</div>

<!-- FIRST NAME -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
<input type="text" name="first_name" class="form-control" placeholder="First Name" required>
</div>

<!-- MIDDLE NAME -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
<input type="text" name="last_name" class="form-control" placeholder="Middle Name">
</div>

<!-- LAST NAME -->
<div class="mb-3 input-group">
<span class="input-group-text"><i class="fa fa-user"></i></span>
<input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
</div>

<!-- GENDER -->
<div class="mb-3 input-group">
    <label class="input-group-text" for="gender"><i class="fa fa-users"></i></label>
    <select name="gender" id="gender" class="form-select" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
        <option value="prefer-not-to-say">Prefer not to say</option>
    </select>
</div>

<!-- DATE OF BIRTH -->
<div class="mb-3 input-group">
  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
  <input type="date" name="dob" id="dob" class="form-control" required>
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