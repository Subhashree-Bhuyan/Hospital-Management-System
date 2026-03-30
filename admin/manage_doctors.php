<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}


/* OPTIONAL: check admin login later */

$query = "SELECT doctors.*, departments.department_name 
          FROM doctors
          JOIN departments 
          ON doctors.department_id = departments.department_id";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Doctors</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h3 class="mb-4">Manage Doctors</h3>
<a href="../logout.php" class="btn btn-danger mb-3">Logout</a>

<div class="mb-4">
    <a href="dashboard.php" class="btn btn-dark btn-sm">Dashboard</a>
    <a href="manage_doctors.php" class="btn btn-primary btn-sm">Doctors</a>
    <a href="add_doctor.php" class="btn btn-success btn-sm">Add Doctor</a>
    <a href="add_department.php" class="btn btn-info btn-sm">Departments</a>
    <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
</div>
<table class="table table-bordered">

<tr>
<th>Name</th>
<th>Department</th>
<th>Phone</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
<td>
<?php echo $row['title']." ".$row['first_name']." ".$row['last_name']; ?>
</td>

<td><?php echo $row['department_name']; ?></td>

<td><?php echo $row['phone']; ?></td>

<td>
<a href="edit_doctor.php?id=<?php echo $row['doctor_id']; ?>" 
class="btn btn-primary btn-sm">Edit</a>

<a href="delete_doctor.php?id=<?php echo $row['doctor_id']; ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('Are you sure you want to delete this doctor?');">
Delete
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>