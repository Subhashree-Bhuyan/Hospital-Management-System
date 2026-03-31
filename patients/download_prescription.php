<?php
session_start();
include("../config/db.php");

/* check patient login */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$appointment_id = $_GET['id'];

/* fetch appointment + prescription + doctor info */
$query = "SELECT appointments.*, patients.*, doctors.first_name as doc_first, doctors.last_name as doc_last,
          doctor_notes.diagnosis, doctor_notes.prescription
          FROM appointments
          JOIN patients ON appointments.patient_id = patients.patient_id
          JOIN doctors ON appointments.doctor_id = doctors.doctor_id
          LEFT JOIN doctor_notes ON appointments.appointment_id = doctor_notes.appointment_id
          WHERE appointments.appointment_id='$appointment_id' 
          AND appointments.patient_id='$patient_id'";

$result = mysqli_query($con, $query);

if(mysqli_num_rows($result) != 1){
    die("Invalid prescription");
}

$row = mysqli_fetch_assoc($result);

/* Generate HTML for PDF */
$html = "
<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.header { text-align: center; border-bottom: 3px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; }
.header h1 { color: #28a745; margin: 0; }
.header p { margin: 5px 0; color: #666; }
.patient-info { margin: 20px 0; }
.patient-info p { margin: 5px 0; }
.prescription-box { border: 2px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 5px; }
.prescription-box h3 { color: #28a745; margin-top: 0; }
.diagnosis-section { margin-bottom: 15px; }
.prescription-section { margin-bottom: 15px; }
.footer { margin-top: 40px; text-align: center; font-size: 12px; color: #999; }
</style>
</head>
<body>

<div class='header'>
  <img scr='../assets/images/logo.png' alt='logo'>
  <h1> City Care Hospital</h1>
  <p>Bhubaneswar, Odisha</p>
  <p>Phone: +91-XXXX-XXXX-XX</p>
</div>

<h3>Prescription</h3>

<div class='patient-info'>
  <p><strong>Patient Name:</strong> " . $row['title'] . " " . $row['first_name'] . " " . $row['last_name'] . "</p>
  <p><strong>Date of Appointment:</strong> " . $row['appointment_date'] . "</p>
  <p><strong>Doctor:</strong> Dr. " . $row['doc_first'] . " " . $row['doc_last'] . "</p>
</div>

<div class='prescription-box'>
  <div class='diagnosis-section'>
    <h3>🔍 Diagnosis</h3>
    <p>" . $row['diagnosis'] . "</p>
  </div>

  <div class='prescription-section'>
    <h3>💊 Prescription</h3>
    <p>" . $row['prescription'] . "</p>
  </div>
</div>

<div class='footer'>
  <p>This prescription is valid for 7 days from the date of appointment.</p>
  <p>For queries, contact the hospital helpline.</p>
</div>

</body>
</html>
";

/* Send as HTML */
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="prescription_' . $appointment_id . '.html"');

/* Output the HTML */
echo $html;

/* Simple conversion to PDF text */
echo $html;
?>