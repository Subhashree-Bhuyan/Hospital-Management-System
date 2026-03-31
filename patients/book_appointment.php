<?php
session_start();
include("../config/db.php");

/* check patient login */
if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

/* get doctor id from URL or POST */
$doctor_id = '';
if(isset($_GET['doctor_id'])){
    $doctor_id = $_GET['doctor_id'];
}
if(isset($_POST['doctor_id'])){
    $doctor_id = $_POST['doctor_id'];
}

/* get date */
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

/* fetch doctor info */
$doc_query = "SELECT doctors.*, departments.department_name 
              FROM doctors
              JOIN departments ON doctors.department_id = departments.department_id
              WHERE doctor_id='$doctor_id'";
$doc_result = mysqli_query($con, $doc_query);
$doctor = mysqli_fetch_assoc($doc_result);

if(!$doctor){
    die("Invalid doctor");
}

/* check doctor daily limit */
$limit_check = "SELECT COUNT(*) as total FROM appointments
                WHERE doctor_id='$doctor_id'
                AND appointment_date='$date'";
$limit_result = mysqli_query($con, $limit_check);
$row = mysqli_fetch_assoc($limit_result);
$isFull = ($row['total'] >= 10);

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
        /* check daily limit */
        $limit_check = "SELECT COUNT(*) as total FROM appointments
                        WHERE doctor_id='$doctor_id'
                        AND appointment_date='$date'";
        $limit_result = mysqli_query($con, $limit_check);
        $row = mysqli_fetch_assoc($limit_result);

        if($row['total'] >= 10){
            $error_msg = "❌ Doctor is fully booked for this date";
        }
        else{
            /* check slot already booked */
            $check = "SELECT * FROM appointments 
                      WHERE doctor_id='$doctor_id'
                      AND appointment_date='$date'
                      AND appointment_time='$time'";
            $result = mysqli_query($con, $check);

            if(mysqli_num_rows($result) > 0){
                $error_msg = "❌ This slot is already booked";
            }
            else{
                /* insert appointment */
                $query = "INSERT INTO appointments 
                         (patient_id, doctor_id, appointment_date, appointment_time, status)
                         VALUES ('$patient_id', '$doctor_id', '$date', '$time', 'Pending')";
                
                if(mysqli_query($con, $query)){
                    $success_msg = "✅ Appointment booked successfully!";
                    header("refresh:2; url=dashboard.php");
                }
                else{
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
      <a href="dashboard.php" class="btn btn-light btn-sm me-2">📋 My Appointments</a>
      <a href="doctors.php" class="btn btn-light btn-sm me-2">👨‍⚕️ View Doctors</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">

<div class="card shadow" style="max-width: 600px; margin: auto;">
  <div class="card-header bg-light">
    <h3>📅 Book Appointment</h3>
  </div>
  <div class="card-body">

    <?php if(isset($success_msg)) { ?>
      <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php } ?>

    <?php if(isset($error_msg)) { ?>
      <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php } ?>

    <!-- Doctor Info -->
    <div class="mb-4 p-3 bg-light rounded">
      <h5>👨‍⚕️ Dr. <?php echo $doctor['first_name'] . " " . $doctor['last_name']; ?></h5>
      <p class="mb-1"><strong>Department:</strong> <?php echo $doctor['department_name']; ?></p>
      <p class="mb-1"><strong>Experience:</strong> <?php echo $doctor['experience']; ?> years</p>
      <p class="mb-1"><strong>Consultation Fee:</strong> ₹<?php echo $doctor['consultation_fee']; ?></p>
      <p class="mb-0"><strong>Timing:</strong> <?php echo $doctor['start_time'] . " - " . $doctor['end_time']; ?></p>
    </div>

    <!-- Booking Form -->
    <form method="POST">
      <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

      <div class="mb-3">
        <label class="form-label"><strong>Select Date</strong></label>
        <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" 
               min="<?php echo date('Y-m-d'); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label"><strong>Select Time Slot</strong></label>
        <select name="time" class="form-control" required>
          <option value="">-- Choose Time --</option>
          <?php
          // Generate time slots (9 AM to 5 PM, every 30 mins)
          $start = strtotime($doctor['start_time']);
          $end = strtotime($doctor['end_time']);
          $interval = 30 * 60; // 30 minutes
          
          while($start < $end) {
            $time_slot = date('H:i', $start);
            
            // Check if slot is booked
            $slot_check = "SELECT * FROM appointments 
                          WHERE doctor_id='$doctor_id'
                          AND appointment_date='$date'
                          AND appointment_time='$time_slot'";
            $slot_result = mysqli_query($con, $slot_check);
            $is_booked = mysqli_num_rows($slot_result) > 0;
            
            if(!$is_booked){
              echo "<option value='$time_slot'>" . date('h:i A', $start) . "</option>";
            }
            $start += $interval;
          }
          ?>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="book" class="btn btn-success">✓ Book Appointment</button>
        <a href="doctors.php" class="btn btn-secondary">⬅️ Back</a>
      </div>
    </form>

  </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>