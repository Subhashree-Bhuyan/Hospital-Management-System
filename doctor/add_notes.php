<?php
session_start();
include("../config/db.php");

/* check doctor login */
if(!isset($_SESSION['doctor_id'])){
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
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
</head>
<body>

<div class="container mt-5">
<h2>Add Notes for Appointment</h2>
<p><strong>Patient:</strong> <?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></p>
<p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?> <strong>Time:</strong> <?php echo $appointment['appointment_time']; ?></p>

<form method="POST">
<div class="mb-3">
<label>Diagnosis</label>
<textarea name="diagnosis" class="form-control" rows="4"><?php echo $notes['diagnosis'] ?? ''; ?></textarea>
</div>

<div class="mb-3">
<label>Prescription</label>
<textarea name="prescription" class="form-control" rows="4"><?php echo $notes['prescription'] ?? ''; ?></textarea>
</div>

<button type="submit" name="save_notes" class="btn btn-primary">Save Notes</button>
<a href="dashboard.php" class="btn btn-secondary">Back</a>
</form>
</div>

</body>
</html>