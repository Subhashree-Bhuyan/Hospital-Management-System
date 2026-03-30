<?php
session_start();
include("../config/db.php");

/* check doctor login */
if(!isset($_SESSION['doctor_id'])){
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

/* fetch appointments */
$query = "SELECT appointments.*, patients.first_name, patients.last_name
          FROM appointments
          JOIN patients ON appointments.patient_id = patients.patient_id
          WHERE doctor_id='$doctor_id'
          ORDER BY appointment_date ASC";

$result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h2 class="mb-4">Doctor Dashboard</h2>

<table class="table table-bordered">

<thead class="table-success">
<tr>
<th>Patient Name</th>
<th>Date</th>
<th>Time</th>
<th>Status</th>
<th>Action</th>

</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
<td><?php echo $row['first_name']." ".$row['last_name']; ?></td>
<td><?php echo $row['appointment_date']; ?></td>
<td><?php echo $row['appointment_time']; ?></td>
<td><?php echo $row['status']; ?></td>
<td>
    <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=Completed" class="btn btn-success btn-sm">
        Complete
    </a>

    <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=Cancelled" class="btn btn-danger btn-sm">
        Cancel
    </a>
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>
</html>