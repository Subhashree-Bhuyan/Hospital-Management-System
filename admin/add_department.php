<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add'])){

    $name = $_POST['department_name'];

    $query = "INSERT INTO departments (department_name)
              VALUES ('$name')";

    mysqli_query($con,$query);

    echo "<script>alert('Department Added');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Department</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h3>Add Department</h3>

<form method="POST">

<input type="text" name="department_name" class="form-control mb-3" placeholder="Enter Department Name" required>

<button type="submit" name="add" class="btn btn-success">Add</button>

</form>

</div>

</body>
</html>