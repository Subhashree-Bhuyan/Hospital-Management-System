<?php
session_start();
include("../config/db.php");

/* check patient login */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
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
LEFT JOIN doctors ON appointments.doctor_id = doctors.doctor_id
WHERE appointments.patient_id='$patient_id'
ORDER BY appointment_date DESC";

$app_result = mysqli_query($con, $app_query);

/* fetch appointments WITH doctor notes */
$app_query = "SELECT appointments.*, doctors.first_name, doctors.last_name, 
              doctor_notes.diagnosis, doctor_notes.prescription
              FROM appointments
              LEFT JOIN doctors ON appointments.doctor_id = doctors.doctor_id
              LEFT JOIN doctor_notes ON appointments.appointment_id = doctor_notes.appointment_id
              WHERE appointments.patient_id='$patient_id'
              ORDER BY appointment_date DESC";

$app_result = mysqli_query($con, $app_query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Patient Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2 class="mb-4">Welcome, <?php echo $patient['first_name']; ?> 👋</h2>

<div class="mb-3">
<a href="../public/doctors.php" class="btn btn-primary">View Doctors</a>
<a href="../public/book_appointment.php" class="btn btn-success">Book Appointment</a>
<a href="../logout.php" class="btn btn-danger">Logout</a>
</div>

<h4>Your Appointments</h4>

<?php if(mysqli_num_rows($app_result) > 0) { ?>
<table class="table table-bordered">
<thead class="table-success">
<tr>
<th>Doctor</th>
<th>Date</th>
<th>Time</th>
<th>Status</th>
</tr>
</thead>


<!-- Doctor notes -->
<tbody>

<?php while($row = mysqli_fetch_assoc($app_result)) { ?>
<tr>
<td colspan="4">
  <div class="card mb-2">
    <div class="card-header bg-light">
      <strong>Dr. <?php echo $row['first_name'] . " " . $row['last_name']; ?></strong> | 
      <?php echo $row['appointment_date']; ?> at <?php echo $row['appointment_time']; ?>
      <?php 
      if($row['status'] == 'Completed'){
        echo " | <span class='badge bg-success'>✓ Completed</span>";
      } elseif($row['status'] == 'Pending'){
        echo " | <span class='badge bg-warning text-dark'>⏳ Pending</span>";
      } else {
        echo " | <span class='badge bg-danger'>✗ Cancelled</span>";
      }
      ?>
    </div>
    <div class="card-body">
      <?php if($row['diagnosis'] && $row['prescription']) { ?>
        <div class="row">
          <div class="col-md-6">
            <strong>🔍 Diagnosis:</strong><br>
            <p><?php echo $row['diagnosis']; ?></p>
          </div>
          <div class="col-md-6">
            <strong>💊 Prescription:</strong><br>
            <p><?php echo $row['prescription']; ?></p>
            <a href="download_prescription.php?id=<?php echo $row['appointment_id']; ?>" class="btn btn-sm btn-primary">📥 Download PDF</a>
          </div>
        </div>
      <?php } else { ?>
        <p class="text-muted">No notes added yet.</p>
      <?php } ?>
    </div>
  </div>
</td>
</tr>

<?php } ?>

</tbody>






</table>
<?php } else { ?>
<p class="alert alert-info">No appointments yet. <a href="../public/book_appointment.php">Book one now!</a></p>
<?php } ?>

</div>

</body>
</html>