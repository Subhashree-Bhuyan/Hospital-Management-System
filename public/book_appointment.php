<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];
$doctor_id = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
$date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');

$doctor = null;
$isFull = false;
$error_msg = "";
$success = false;

function get_day_name($date)
{
    return date('l', strtotime($date));
}

function doctor_is_available_on_day($available_days, $selected_date)
{
    $selected_day_full = strtolower(trim(date('l', strtotime($selected_date))));
    $selected_day_short = strtolower(trim(date('D', strtotime($selected_date))));

    $days = array_map('trim', explode(',', $available_days));

    foreach ($days as $day) {
        $day_lower = strtolower($day);

        if ($day_lower === $selected_day_full || $day_lower === $selected_day_short) {
            return true;
        }
    }

    return false;
}


function valid_date_format($date)
{
    return preg_match("/^\d{4}-\d{2}-\d{2}$/", $date);
}

if ($doctor_id > 0) {
    $stmt = mysqli_prepare($con, "SELECT doctors.*, departments.department_name 
                                  FROM doctors
                                  JOIN departments ON doctors.department_id = departments.department_id
                                  WHERE doctors.doctor_id = ?
                                  LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
    mysqli_stmt_execute($stmt);
    $doc_result = mysqli_stmt_get_result($stmt);
    $doctor = mysqli_fetch_assoc($doc_result);

    if ($doctor && valid_date_format($date)) {
        $stmt = mysqli_prepare($con, "SELECT COUNT(*) as total FROM appointments
                                      WHERE doctor_id = ?
                                      AND appointment_date = ?
                                      AND status != 'Cancelled'");
        mysqli_stmt_bind_param($stmt, "is", $doctor_id, $date);
        mysqli_stmt_execute($stmt);
        $limit_result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($limit_result);
        $isFull = ($row['total'] >= 10);
    }
}

if (isset($_POST['book'])) {
    $doctor_id = (int) $_POST['doctor_id'];
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);

    $stmt = mysqli_prepare($con, "SELECT doctors.*, departments.department_name 
                                  FROM doctors
                                  JOIN departments ON doctors.department_id = departments.department_id
                                  WHERE doctors.doctor_id = ?
                                  LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
    mysqli_stmt_execute($stmt);
    $doc_result = mysqli_stmt_get_result($stmt);
    $doctor = mysqli_fetch_assoc($doc_result);

    if (!$doctor) {
        $error_msg = "Invalid doctor selected.";
    } elseif (!valid_date_format($date)) {
        $error_msg = "Invalid appointment date.";
    } elseif ($date < date('Y-m-d')) {
        $error_msg = "Past date booking is not allowed.";
    } elseif (empty($time) || !preg_match("/^\d{2}:\d{2}(:\d{2})?$/", $time)) {
        $error_msg = "Invalid time slot selected.";
    } elseif (!doctor_is_available_on_day($doctor['available_days'], $date)) {
        $error_msg = "Doctor is not available on " . get_day_name($date) . ".";
    } else {
        $normalized_time = strlen($time) == 5 ? $time . ":00" : $time;

        if ($normalized_time < $doctor['start_time'] || $normalized_time >= $doctor['end_time']) {
            $error_msg = "Selected time is outside doctor availability hours.";
        } else {
            $stmt = mysqli_prepare($con, "SELECT COUNT(*) as total FROM appointments
                                          WHERE doctor_id = ?
                                          AND appointment_date = ?
                                          AND status != 'Cancelled'");
            mysqli_stmt_bind_param($stmt, "is", $doctor_id, $date);
            mysqli_stmt_execute($stmt);
            $limit_result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($limit_result);

            if ($row['total'] >= 10) {
                $error_msg = "Doctor is fully booked for this date.";
            } else {
                $stmt = mysqli_prepare($con, "SELECT appointment_id FROM appointments
                                              WHERE doctor_id = ?
                                              AND appointment_date = ?
                                              AND appointment_time = ?
                                              AND status != 'Cancelled'
                                              LIMIT 1");
                mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $date, $normalized_time);
                mysqli_stmt_execute($stmt);
                $slot_result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($slot_result) > 0) {
                    $error_msg = "This time slot is already booked.";
                } else {
                    $stmt = mysqli_prepare($con, "INSERT INTO appointments
                                                  (patient_id, doctor_id, appointment_date, appointment_time, status)
                                                  VALUES (?, ?, ?, ?, 'Pending')");
                    mysqli_stmt_bind_param($stmt, "iiss", $patient_id, $doctor_id, $date, $normalized_time);

                    if (mysqli_stmt_execute($stmt)) {
                        $appointment_id = mysqli_insert_id($con);
                        $success = true;
                    } else {
                        if (mysqli_errno($con) == 1062) {
                            $error_msg = "This slot was just booked by another patient. Please choose another time.";
                        } else {
                            $error_msg = "Error booking appointment.";
                        }
                    }
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
  .navbar {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    position: sticky;
    top: 0;
    z-index: 1000;
  }
</style>
</head>

<body>

<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Hospital Management</span>
    <div>
      <a href="../index.php" class="btn btn-light btn-sm me-2">Home</a>
      <a href="../patients/dashboard.php" class="btn btn-light btn-sm me-2">Dashboard</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">

<?php if (empty($doctor_id)) { ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow p-5 text-center">
      <h2 class="mb-4">Book Appointment</h2>
      <p class="text-muted mb-4">First, choose a doctor from our list</p>
      <a href="doctors.php" class="btn btn-success btn-lg mb-3">Select Doctor</a>
      <p class="text-muted"><a href="../index.php">Back to Home</a></p>
    </div>
  </div>
</div>

<?php } elseif ($doctor) { ?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card shadow">
      <div class="card-header bg-light p-4">
        <div class="row align-items-center">
          <div class="col">
            <h4>Dr. <?php echo $doctor['first_name'] . " " . $doctor['last_name']; ?></h4>
            <p class="mb-1"><strong>Department:</strong> <?php echo $doctor['department_name']; ?></p>
            <p class="mb-1"><strong>Experience:</strong> <?php echo $doctor['experience']; ?> years</p>
            <p class="mb-1"><strong>Fee:</strong> Rs. <?php echo number_format($doctor['consultation_fee'], 2); ?></p>
            <p class="mb-1"><strong>Available Days:</strong> <?php echo $doctor['available_days']; ?></p>
            <p class="mb-0"><strong>Available Time:</strong> <?php echo date('h:i A', strtotime($doctor['start_time'])); ?> - <?php echo date('h:i A', strtotime($doctor['end_time'])); ?></p>
          </div>
        </div>
      </div>

      <div class="card-body p-4">
        <h5 class="mb-4">Select Date & Time</h5>

        <?php if (!empty($error_msg)) { ?>
          <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php } ?>

        <?php
        $selected_day_name = valid_date_format($date) ? get_day_name($date) : '';
        $doctor_available_today = valid_date_format($date) ? doctor_is_available_on_day($doctor['available_days'], $date) : false;
        ?>

        <?php if (valid_date_format($date) && !$doctor_available_today) { ?>
          <div class="alert alert-warning">
            Doctor is not available on <strong><?php echo $selected_day_name; ?></strong>.
          </div>
        <?php } ?>

        <?php if ($isFull) { ?>
          <div class="alert alert-warning">
            Doctor is fully booked for this date.
          </div>
        <?php } ?>

        <form method="POST">
          <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">

          <label class="form-label"><strong>Select Date</strong></label>
          <input type="date"
                 class="form-control mb-3"
                 name="date"
                 value="<?php echo htmlspecialchars($date); ?>"
                 min="<?php echo date('Y-m-d'); ?>"
                 onchange="window.location='book_appointment.php?doctor_id=<?php echo $doctor['doctor_id']; ?>&date='+this.value;"
                 required>

          <label class="form-label"><strong>Select Time Slot</strong></label>
          <select name="time" class="form-control mb-4" <?php if ($isFull || !$doctor_available_today) echo "disabled"; ?> required>
            <option value="" selected disabled>-- Choose Time --</option>

            <?php
            $start = strtotime($doctor['start_time']);
            $end = strtotime($doctor['end_time']);
            $interval = 30 * 60;

            while ($start < $end) {
                $slot_value = date('H:i:s', $start);
                $slot_label = date('h:i A', $start);

                $stmt = mysqli_prepare($con, "SELECT appointment_id FROM appointments
                                              WHERE doctor_id = ?
                                              AND appointment_date = ?
                                              AND appointment_time = ?
                                              AND status != 'Cancelled'
                                              LIMIT 1");
                mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $date, $slot_value);
                mysqli_stmt_execute($stmt);
                $result_slot = mysqli_stmt_get_result($stmt);
                $is_booked = mysqli_num_rows($result_slot) > 0;

                if ($is_booked) {
                    echo "<option disabled>{$slot_label} (Booked)</option>";
                } else {
                    echo "<option value='{$slot_value}'>{$slot_label}</option>";
                }

                $start += $interval;
            }
            ?>
          </select>

          <div class="d-grid gap-2">
            <button type="submit" name="book" class="btn btn-success btn-lg" <?php if ($isFull || !$doctor_available_today) echo "disabled"; ?>>
              Confirm Booking
            </button>
            <a href="doctors.php" class="btn btn-secondary">Choose Different Doctor</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php } else { ?>

<div class="alert alert-danger text-center">Doctor not found</div>

<?php } ?>

</div>

<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Success</h5>
      </div>
      <div class="modal-body text-center">
        <h5>Appointment Booked Successfully</h5>
        <p>Your appointment is confirmed.</p>
        <p>You can download the appointment slip from your dashboard.</p>
      </div>
      <div class="modal-footer">
        <a href="../patients/dashboard.php" class="btn btn-success">Go to Dashboard</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if ($success) { ?>
var myModal = new bootstrap.Modal(document.getElementById('successModal'));
myModal.show();
<?php } ?>
</script>

</body>
</html>
