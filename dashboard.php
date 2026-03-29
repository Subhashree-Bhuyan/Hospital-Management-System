<?php
session_start();
include("config/db.php");

/* check login */
if(!isset($_SESSION['patient_id'])){
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

/* fetch patient data */
$patient_query = "SELECT * FROM patients WHERE patient_id='$patient_id'";
$patient_result = mysqli_query($con, $patient_query);
$patient = mysqli_fetch_assoc($patient_result);

/* fetch appointments */
$app_query = "SELECT appointments.*, doctors.first_name, doctors.last_name
FROM appointments
JOIN doctors ON appointments.doctor_id = doctors.doctor_id
WHERE patient_id='$patient_id'
ORDER BY appointment_date DESC";

$app_result = mysqli_query($con, $app_query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CSS -->
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container">
    <a class="navbar-brand" href="#">Hospital</a>

    <div>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="public/doctors.php">Doctors</a></li>
        <li class="nav-item"><a class="nav-link" href="public/book_appointment.php">Appointment</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">

  <h2 class="mb-4">Welcome, <?php echo $patient['first_name']; ?> 👋</h2>

  <div class="card shadow p-4">

    <h4>Your Appointments</h4>

    <table class="table table-bordered mt-3">
    <thead class="table-success">
    <tr>
    <th>Doctor</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    </tr>
    </thead>

    <tbody>

    <?php while($row = mysqli_fetch_assoc($app_result)) { ?>

    <tr>
    <td>Dr. <?php echo $row['first_name']." ".$row['last_name']; ?></td>
    <td><?php echo $row['appointment_date']; ?></td>
    <td><?php echo $row['appointment_time']; ?></td>
    <td>
      <?php 
      if($row['status'] == 'Pending'){
          echo "<span class='badge bg-warning text-dark'>Pending</span>";
      }
      else if($row['status'] == 'Completed'){
          echo "<span class='badge bg-success'>Completed</span>";
      }
      else{
          echo "<span class='badge bg-danger'>Cancelled</span>";
      }
      ?>
    </td>
    </tr>

    <?php } ?>

    </tbody>
    </table>

  </div>


</table>

</div>   <!-- END of appointments card -->

<!-- 👉 ADD BILL CODE HERE -->

<div class="card shadow p-4 mt-4">

<h4>Your Bills</h4>

<table class="table table-bordered mt-3">
<thead class="table-success">
<tr>
<th>Doctor</th>
<th>Total Amount</th>
<th>Paid</th>
<th>Pending</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php

$bill_query = "SELECT bills.*, doctors.first_name, doctors.last_name
FROM bills
JOIN doctors ON bills.doctor_id = doctors.doctor_id
WHERE bills.patient_id='$patient_id'
ORDER BY bills.created_at DESC";

$bill_result = mysqli_query($con, $bill_query);

while($bill = mysqli_fetch_assoc($bill_result)){

?>

<tr>
<td>Dr. <?php echo $bill['first_name']." ".$bill['last_name']; ?></td>
<td>₹<?php echo $bill['total_amount']; ?></td>
<td>₹<?php echo $bill['paid_amount']; ?></td>
<td>₹<?php echo $bill['pending_amount']; ?></td>

<td>
<?php 
if($bill['status'] == 'Pending'){
    echo "<span class='badge bg-warning text-dark'>Pending</span>";
}
else if($bill['status'] == 'Paid'){
    echo "<span class='badge bg-success'>Paid</span>";
}
else{
    echo "<span class='badge bg-info text-dark'>Partial</span>";
}
?>
</td>

</tr>

<?php } ?>

</tbody>
</table>

</div>

</div>

</body>
</html>