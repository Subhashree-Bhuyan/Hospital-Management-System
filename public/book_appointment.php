<?php
session_start();
include("../config/db.php");

/* check login */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
    exit();
}

/* get doctor id */
$doctor_id = '';

if(isset($_GET['doctor_id'])){
    $doctor_id = $_GET['doctor_id'];
}
if(isset($_POST['doctor_id'])){
    $doctor_id = $_POST['doctor_id'];
}

/* get date */
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

/* check doctor daily limit */
$limit_check = "SELECT COUNT(*) as total FROM appointments
                WHERE doctor_id='$doctor_id'
                AND appointment_date='$date'";
$limit_result = mysqli_query($con,$limit_check);
$row = mysqli_fetch_assoc($limit_result);
$isFull = ($row['total'] >= 10);

/* booking logic */
if(isset($_POST['book'])){

    $patient_id = $_SESSION['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    /* prevent past booking */
    if($date < date('Y-m-d')){
        echo "<script>alert('Cannot book past date');</script>";
    }
    else{

        /* check daily limit again */
        $limit_check = "SELECT COUNT(*) as total FROM appointments
                        WHERE doctor_id='$doctor_id'
                        AND appointment_date='$date'";
        $limit_result = mysqli_query($con,$limit_check);
        $row = mysqli_fetch_assoc($limit_result);

        if($row['total'] >= 10){
            echo "<script>alert('Doctor is fully booked');</script>";
        }
        else{

            /* check slot */
            $check = "SELECT * FROM appointments 
                      WHERE doctor_id='$doctor_id'
                      AND appointment_date='$date'
                      AND appointment_time='$time'";

            $result = mysqli_query($con,$check);

            if(mysqli_num_rows($result) > 0){
                echo "<script>alert('Slot already booked');</script>";
            }
            else{

                /* insert appointment */
                $query = "INSERT INTO appointments 
                (patient_id,doctor_id,appointment_date,appointment_time)
                VALUES ('$patient_id','$doctor_id','$date','$time')";

                mysqli_query($con,$query);

                /* get appointment id */
                $appointment_id = mysqli_insert_id($con);

                /* get consultation fee */
                $doc_query = "SELECT consultation_fee FROM doctors WHERE doctor_id='$doctor_id'";
                $doc_result = mysqli_query($con,$doc_query);
                $doc = mysqli_fetch_assoc($doc_result);

                $fee = $doc['consultation_fee'];

                /* insert bill */
                $bill_query = "INSERT INTO bills 
                (appointment_id, patient_id, doctor_id, consultation_fee, total_amount, paid_amount, pending_amount, status)
                VALUES
                ('$appointment_id', '$patient_id', '$doctor_id', '$fee', '$fee', 0, '$fee', 'Pending')";

                mysqli_query($con,$bill_query);

                /* trigger modal */
                $success = true;
            }
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

                    <!-- hidden -->
                    <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                    <input type="hidden" name="date" value="<?php echo $date; ?>">

                    <!-- DATE -->
                    <label class="form-label">Select Date</label>
                    <input 
                    type="date" 
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

                    <button type="submit" name="book" class="btn btn-success w-100" <?php if($isFull) echo "disabled"; ?>>
                        Book Appointment
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Success</h5>
      </div>

      <div class="modal-body text-center">
        <h5>Appointment Booked ✅</h5>
        <p>Your bill has been generated.</p>
      </div>

      <div class="modal-footer">
        <a href="../dashboard.php" class="btn btn-success">Go to Dashboard</a>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Show Modal -->
<?php if(isset($success)){ ?>
<script>
var myModal = new bootstrap.Modal(document.getElementById('successModal'));
myModal.show();
</script>
<?php } ?>

</body>
</html>