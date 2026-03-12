<?php
session_start();
include("../config/db.php");

/* check login */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
    exit();
}

/* get doctor id from URL */
$doctor_id = $_GET['doctor_id'];

/* booking logic */
if(isset($_POST['book'])){

    $patient_id = $_SESSION['patient_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];

    $query = "INSERT INTO appointments 
              (patient_id, doctor_id, appointment_date, appointment_time)
              VALUES
              ('$patient_id','$doctor_id','$date','$time')";

    mysqli_query($con,$query);

    echo "Appointment Booked Successfully";
}
?>

<h2>Book Appointment</h2>

<form method="POST">

<label>Appointment Date</label><br>
<input type="date" name="appointment_date" required>
<br><br>

<label>Appointment Time</label><br>
<input type="time" name="appointment_time" required>
<br><br>

<button type="submit" name="book">
Book Appointment
</button>

</form>