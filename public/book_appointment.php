<?php
session_start();
include("../config/db.php");

/* check login */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
    exit();
}

/* get doctor id from URL */
$doctor_id = isset($_POST['doctor_id']) ? $_POST['doctor_id'] : $_GET['doctor_id'];
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

/* booking logic */
if(isset($_POST['book'])){
    $patient_id = $_SESSION['patient_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    
    /* check doctor daily limit */
    $limit_check = "SELECT COUNT(*) as total FROM appointments
                    WHERE doctor_id='$doctor_id'
                    AND appointment_date='$date'";

    $limit_result = mysqli_query($con,$limit_check);
    $row = mysqli_fetch_assoc($limit_result);

    if($row['total'] >= 10){
        echo "Doctor is fully booked for this day.";
        exit();
    }

    /* prevent past booking */
    if($date < date('Y-m-d')){
        echo "You cannot book an appointment in the past.";
        exit();
    }

    $check = "SELECT * FROM appointments 
          WHERE doctor_id='$doctor_id'
          AND appointment_date='$date'
          AND appointment_time='$time'";

$result = mysqli_query($con,$check);

if(mysqli_num_rows($result) > 0){

    echo "This time slot is already booked.";

}else{

    $query = "INSERT INTO appointments (patient_id,doctor_id,appointment_date,appointment_time)
              VALUES ('$patient_id','$doctor_id','$date','$time')";

    mysqli_query($con,$query);

    echo "Appointment Booked Successfully";

}
}
?>

<h2>Book Appointment</h2>

<form method="POST">
<input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
<input type="hidden" name="date" value="<?php echo $date; ?>">

<label>Appointment Date</label><br>
<input 
type="date" 
id="date_picker"
value="<?php echo $date; ?>"
min="<?php echo date('Y-m-d'); ?>"
onchange="window.location='book_appointment.php?doctor_id=<?php echo $doctor_id; ?>&date='+this.value;"
required>
<br><br>


    <?php

    $limit_check = "SELECT COUNT(*) as total FROM appointments
                    WHERE doctor_id='$doctor_id'
                    AND appointment_date='$date'";

    $limit_result = mysqli_query($con,$limit_check);
    $row = mysqli_fetch_assoc($limit_result);

    $isFull = ($row['total'] >= 10);

    if($isFull){
        echo "<p style='color:red;'>Doctor is fully booked for this day.</p>";
    }

    ?>

<label>Appointment Time</label><br>
<select name="time" <?php if($isFull) echo "disabled"; ?> required>

<option value="" disabled selected>Select Time</option>

<?php

$slots = ["08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30"];

foreach($slots as $slot){

    $check_slot = "SELECT * FROM appointments 
                WHERE doctor_id='$doctor_id'
                AND appointment_date='$date'
                AND appointment_time='$slot'";

    $result_slot = mysqli_query($con,$check_slot);

    if(mysqli_num_rows($result_slot) > 0){

        echo "<option value='$slot' disabled>$slot (Booked)</option>";

    }else{

        echo "<option value='$slot'>$slot</option>";

    }

}

?>

</select>
<br><br>

<button type="submit" name="book" <?php if($isFull) echo "disabled"; ?>>
Book Appointment
</button>

</form>
