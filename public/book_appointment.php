<?php
session_start();
include("../config/db.php");

/* Check if user is logged in */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

/* Fetch doctor info if doctor_id provided */
$doctor = null;
$isFull = false;

if(!empty($doctor_id)){
    $doc_query = "SELECT doctors.*, departments.department_name 
                  FROM doctors
                  JOIN departments ON doctors.department_id = departments.department_id
                  WHERE doctor_id='$doctor_id'";
    $doc_result = mysqli_query($con, $doc_query);
    $doctor = mysqli_fetch_assoc($doc_result);
    
    if($doctor){
        /* check doctor daily limit */
        $limit_check = "SELECT COUNT(*) as total FROM appointments
                        WHERE doctor_id='$doctor_id'
                        AND appointment_date='$date'";
        $limit_result = mysqli_query($con,$limit_check);
        $row = mysqli_fetch_assoc($limit_result);
        $isFull = ($row['total'] >= 10);
    }
}

/* booking logic */
if(isset($_POST['book'])){
    $doctor_id = mysqli_real_escape_string($con, $_POST['doctor_id']);
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $time = mysqli_real_escape_string($con, $_POST['time']);

    /* prevent past booking */
    if($date < date('Y-m-d')){
        $error_msg = "❌ Cannot book past date";
    }
    else{
        /* check daily limit again */
        $limit_check = "SELECT COUNT(*) as total FROM appointments
                        WHERE doctor_id='$doctor_id'
                        AND appointment_date='$date'";
        $limit_result = mysqli_query($con,$limit_check);
        $row = mysqli_fetch_assoc($limit_result);

        if($row['total'] >= 10){
            $error_msg = "❌ Doctor is fully booked";
        }
        else{
            /* check slot */
            $check = "SELECT * FROM appointments 
                      WHERE doctor_id='$doctor_id'
                      AND appointment_date='$date'
                      AND appointment_time='$time'";
            $result = mysqli_query($con,$check);

            if(mysqli_num_rows($result) > 0){
                $error_msg = "❌ Slot already booked";
            }
            else{
                /* insert appointment */
                $query = "INSERT INTO appointments 
                (patient_id,doctor_id,appointment_date,appointment_time,status)
                VALUES ('$patient_id','$doctor_id','$date','$time','Pending')";

                if(mysqli_query($con,$query)){
                    /* get appointment id */
                    $appointment_id = mysqli_insert_id($con);

                    /* get doctor info for bill */
                    $doc_fee_query = "SELECT consultation_fee FROM doctors WHERE doctor_id='$doctor_id'";
                    $doc_fee_result = mysqli_query($con, $doc_fee_query);
                    $doc_fee = mysqli_fetch_assoc($doc_fee_result);
                    $fee = $doc_fee['consultation_fee'];

                    /* insert bill */
                    $bill_query = "INSERT INTO bills 
                    (appointment_id, patient_id, doctor_id, consultation_fee, total_amount, paid_amount, pending_amount, status)
                    VALUES
                    ('$appointment_id', '$patient_id', '$doctor_id', '$fee', '$fee', 0, '$fee', 'Pending')";

                    mysqli_query($con,$bill_query);

                    /* trigger modal */
                    $success = true;
                } else {
                    $error_msg = "❌ Error booking appointment";
                }
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
  .navbar { 
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    position: sticky;
    top: 0;
    z-index: 1000;
  }
</style>
</head>

<body>

<!-- STICKY NAVBAR -->
<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">🏥 Hospital Management</span>
    <div>
      <a href="../index.php" class="btn btn-light btn-sm me-2">🏠 Home</a>
      <a href="../patients/dashboard.php" class="btn btn-light btn-sm me-2">📋 Dashboard</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">

    <!-- IF NO DOCTOR SELECTED: Show "Select Doctor" Button -->
    <?php if(empty($doctor_id)) { ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow p-5 text-center">
                    <h2 class="mb-4">📅 Book Appointment</h2>
                    
                    <p class="text-muted mb-4">First, choose a doctor from our list</p>

                    <a href="doctors.php" class="btn btn-success btn-lg mb-3">
                        👨‍⚕️ Select Doctor
                    </a>

                    <p class="text-muted">
                        <a href="../index.php">← Back to Home</a>
                    </p>
                </div>
            </div>
        </div>

    <!-- IF DOCTOR SELECTED: Show Booking Form -->
    <?php } else if($doctor) { ?>

        <div class="row justify-content-center">
            <div class="col-md-7">

                <div class="card shadow">

                    <!-- Doctor Info Section -->
                    <div class="card-header bg-light p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <?php
                                $image = "../assets/images/default.png";
                                if(!empty($doctor['image'])){
                                    $image = "../assets/images/" . $doctor['image'];
                                }
                                ?>
                                <img src="<?php echo $image; ?>" 
                                style="width:80px; height:80px; border-radius:50%; object-fit:cover;">
                            </div>
                            <div class="col">
                                <h4>✅ Dr. <?php echo $doctor['first_name'] . " " . $doctor['last_name']; ?></h4>
                                <p class="mb-1"><strong>Department:</strong> <?php echo $doctor['department_name']; ?></p>
                                <p class="mb-0"><strong>Experience:</strong> <?php echo $doctor['experience']; ?> years | <strong>Fee:</strong> ₹<?php echo $doctor['consultation_fee']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <div class="card-body p-4">
                        <h5 class="mb-4">📅 Select Date & Time</h5>

                        <?php if(isset($error_msg)) { ?>
                          <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                        <?php } ?>

                        <form method="POST">
                            <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">

                            <!-- DATE -->
                            <label class="form-label"><strong>📅 Select Date</strong></label>
                            <input 
                            type="date" 
                            class="form-control mb-3"
                            name="date"
                            value="<?php echo $date; ?>"
                            min="<?php echo date('Y-m-d'); ?>"
                            onchange="window.location='book_appointment.php?doctor_id=<?php echo $doctor['doctor_id']; ?>&date='+this.value;"
                            required>

                            <?php if($isFull){ ?>
                                <p class="alert alert-warning mb-3">⚠️ Doctor is fully booked for this day</p>
                            <?php } ?>

                            <!-- TIME -->
                            <label class="form-label"><strong>🕐 Select Time Slot</strong></label>
                            <select name="time" class="form-control mb-4" <?php if($isFull) echo "disabled"; ?> required>
                                <option value="" disabled selected>-- Choose Time --</option>

                                <?php
                                $slots = ["08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30"];

                                foreach($slots as $slot){
                                    $check_slot = "SELECT * FROM appointments 
                                    WHERE doctor_id='{$doctor['doctor_id']}'
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

                            <div class="d-grid gap-2">
                                <button type="submit" name="book" class="btn btn-success btn-lg" <?php if($isFull) echo "disabled"; ?>>
                                    ✓ Confirm Booking
                                </button>
                                <a href="doctors.php" class="btn btn-secondary">⬅️ Choose Different Doctor</a>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    <!-- IF DOCTOR NOT FOUND -->
    <?php } else { ?>

        <div class="alert alert-danger text-center">
            ❌ Doctor not found
        </div>

    <?php } ?>

</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">✅ Success</h5>
      </div>
      <div class="modal-body text-center">
        <h5>Appointment Booked Successfully!</h5>
        <p>📝 Your appointment is confirmed.</p>
        <p>💰 Your bill has been generated.</p>
      </div>
      <div class="modal-footer">
        <a href="../patients/dashboard.php" class="btn btn-success">Go to Dashboard</a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
<?php if(isset($success)){ ?>
var myModal = new bootstrap.Modal(document.getElementById('successModal'));
myModal.show();
<?php } ?>
</script>

</body>
</html>