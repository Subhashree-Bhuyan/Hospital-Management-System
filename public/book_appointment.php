<?php
session_start();
include("../config/db.php");

/* check login */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
    exit();
}

/* get doctor id */
$doctor_id = isset($_POST['doctor_id']) ? $_POST['doctor_id'] : $_GET['doctor_id'];
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

/* check doctor full */
$limit_check = "SELECT COUNT(*) as total FROM appointments
                WHERE doctor_id='$doctor_id'
                AND appointment_date='$date'";
$limit_result = mysqli_query($con,$limit_check);
$row = mysqli_fetch_assoc($limit_result);
$isFull = ($row['total'] >= 10);

/* booking logic */
if(isset($_POST['book'])){
    $patient_id = $_SESSION['patient_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    if($isFull){
        echo "<script>alert('Doctor is fully booked');</script>";
    }
    else if($date < date('Y-m-d')){
        echo "<script>alert('Cannot book past date');</script>";
    }
    else{
        $check = "SELECT * FROM appointments 
                  WHERE doctor_id='$doctor_id'
                  AND appointment_date='$date'
                  AND appointment_time='$time'";

        $result = mysqli_query($con,$check);

        if(mysqli_num_rows($result) > 0){
            echo "<script>alert('Slot already booked');</script>";
        }else{
            $query = "INSERT INTO appointments 
            (patient_id,doctor_id,appointment_date,appointment_time)
            VALUES ('$patient_id','$doctor_id','$date','$time')";

            mysqli_query($con,$query);

            echo "<script>alert('Appointment Booked Successfully');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
<title>Book Appointment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container">
    <a class="navbar-brand" href="#">Hospital</a>

    <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="doctors.php">Doctors</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Appointment</a></li>
        <li class="nav-item"><a class="nav-link" href="../dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
    </ul>

  </div>
</nav>

<div class="container mt-5">

<div class="row justify-content-center">
<div class="col-md-5">

<div class="card shadow p-4">

<h3 class="text-center mb-4">Book Appointment</h3>

<form method="POST">

<input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

<!-- DATE -->
<label class="form-label">Select Date</label>
<input 
type="date" 
name="date"
class="form-control mb-3"
value="<?php echo $date; ?>"
min="<?php echo date('Y-m-d'); ?>"
onchange="window.location='book_appointment.php?doctor_id=<?php echo $doctor_id; ?>&date='+this.value;"
required>

<?php if($isFull){ ?>
<p class="text-danger">Doctor is fully booked for this day</p>
<?php } ?>

<!-- TIME -->
<label class="form-label">Select Time</label>
<select name="time" class="form-control mb-3" <?php if($isFull) echo "disabled"; ?> required>

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
        echo "<option disabled>$slot (Booked)</option>";
    }else{
        echo "<option value='$slot'>$slot</option>";
    }
}
?>

</select>

<button type="submit" name="book" class="btn btn-success w-100">
Book Appointment
</button>

</form>

</div>

</div>
</div>

</div>

</body>
</html>