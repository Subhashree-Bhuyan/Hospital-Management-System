<?php
session_start();
include("../config/db.php");

/* check doctor login */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* get doctor_id from doctors table */
$doc_query = "SELECT doctor_id, first_name, last_name FROM doctors WHERE user_id='$user_id'";
$doc_result = mysqli_query($con, $doc_query);
$doc_row = mysqli_fetch_assoc($doc_result);
$doctor_id = $doc_row['doctor_id'];
$doctor_name = $doc_row['first_name'] . " " . $doc_row['last_name'];

$appointment_id = $_GET['id'];

// Fetch appointment details
$app_query = "SELECT appointments.*, patients.first_name, patients.last_name
              FROM appointments
              JOIN patients ON appointments.patient_id = patients.patient_id
              WHERE appointment_id='$appointment_id' AND doctor_id='$doctor_id'";
$app_result = mysqli_query($con, $app_query);
$appointment = mysqli_fetch_assoc($app_result);

if(!$appointment){
    echo "Invalid appointment";
    exit();
}

// Check if notes already exist
$notes_query = "SELECT * FROM doctor_notes WHERE appointment_id='$appointment_id'";
$notes_result = mysqli_query($con, $notes_query);
$notes = mysqli_fetch_assoc($notes_result);

if(isset($_POST['save_notes'])){
    $diagnosis = mysqli_real_escape_string($con, $_POST['diagnosis']);
    $prescription = mysqli_real_escape_string($con, $_POST['prescription']);

    if($notes){
        // Update
        $update_query = "UPDATE doctor_notes SET diagnosis='$diagnosis', prescription='$prescription' WHERE appointment_id='$appointment_id'";
        mysqli_query($con, $update_query);
    }else{
        // Insert
        $insert_query = "INSERT INTO doctor_notes (appointment_id, diagnosis, prescription) VALUES ('$appointment_id', '$diagnosis', '$prescription')";
        mysqli_query($con, $insert_query);
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Notes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .navbar { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">🏥 Hospital Management</span>
    <div>
      <span class="text-white me-3">👨‍⚕️ Dr. <?php echo $doctor_name; ?></span>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">

<div class="card">
  <div class="card-header bg-light">
    <h3>📝 Add/Edit Appointment Notes</h3>
  </div>
  <div class="card-body">
    <div class="row mb-4">
      <div class="col-md-6">
        <p><strong>👤 Patient:</strong> <?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></p>
      </div>
      <div class="col-md-6">
        <p><strong>📅 Date:</strong> <?php echo $appointment['appointment_date']; ?> <strong>🕐 Time:</strong> <?php echo $appointment['appointment_time']; ?></p>
      </div>
    </div>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label"><strong>🔍 Diagnosis</strong></label>
        <textarea name="diagnosis" class="form-control" rows="5" placeholder="Enter patient diagnosis..." required><?php echo $notes['diagnosis'] ?? ''; ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label"><strong>💊 Prescription</strong></label>
        <textarea name="prescription" class="form-control" rows="5" placeholder="Enter prescription details..." required><?php echo $notes['prescription'] ?? ''; ?></textarea>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="save_notes" class="btn btn-primary">💾 Save Notes</button>
        <a href="dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
      </div>
    </form>
  </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>