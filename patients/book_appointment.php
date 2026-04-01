<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];
$doctor_id = 0;

if (isset($_GET['doctor_id'])) {
    $doctor_id = (int) $_GET['doctor_id'];
}
if (isset($_POST['doctor_id'])) {
    $doctor_id = (int) $_POST['doctor_id'];
}

$date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
$error_msg = "";
$success_msg = "";

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

$stmt = mysqli_prepare($con, "SELECT doctors.*, departments.department_name 
                              FROM doctors
                              JOIN departments ON doctors.department_id = departments.department_id
                              WHERE doctor_id = ?
                              LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$doc_result = mysqli_stmt_get_result($stmt);
$doctor = mysqli_fetch_assoc($doc_result);

if (!$doctor) {
    die("Invalid doctor");
}

$stmt = mysqli_prepare($con, "SELECT COUNT(*) as total FROM appointments
                              WHERE doctor_id = ?
                              AND appointment_date = ?
                              AND status != 'Cancelled'");
mysqli_stmt_bind_param($stmt, "is", $doctor_id, $date);
mysqli_stmt_execute($stmt);
$limit_result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($limit_result);
$isFull = ($row['total'] >= 10);

if (isset($_POST['book'])) {
    $doctor_id = (int) $_POST['doctor_id'];
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);

    $stmt = mysqli_prepare($con, "SELECT doctors.*, departments.department_name 
                                  FROM doctors
                                  JOIN departments ON doctors.department_id = departments.department_id
                                  WHERE doctor_id = ?
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
                    $error_msg = "This slot is already booked.";
                } else {
                    $stmt = mysqli_prepare($con, "INSERT INTO appointments
                                                  (patient_id, doctor_id, appointment_date, appointment_time, status)
                                                  VALUES (?, ?, ?, ?, 'Pending')");
                    mysqli_stmt_bind_param($stmt, "iiss", $patient_id, $doctor_id, $date, $normalized_time);

                    if (mysqli_stmt_execute($stmt)) {
                        $success_msg = "Appointment booked successfully.";
                        header("refresh:2; url=dashboard.php");
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
  .navbar { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
</style>
</head>

<body>

<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Hospital Management</span>
    <div>
      <a href="dashboard.php" class="btn btn-light btn-sm me-2">My Appointments</a>
      <a href="../public/doctors.php" class="btn btn-light btn-sm me-2">View Doctors</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">

<div class="card shadow" style="max-width: 650px; margin: auto;">
  <div class="card-header bg-light">
    <h3>Book Appointment</h3>
  </div>
  <div class="card-body">

    <?php if (!empty($success_msg)) { ?>
      <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php } ?>

    <?php if (!empty($error_msg)) { ?>
      <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php } ?>

    <?php
    $selected_day_name = valid_date_format($date) ? get_day_name($date) : '';
    $doctor_available_today = valid_date_format($date) ? doctor_is_available_on_day($doctor['available_days'], $date) : false;
    ?>

    <div class="mb-4 p-3 bg-light rounded">
      <h5>Dr. <?php echo $doctor['first_name'] . " " . $doctor['last_name']; ?></h5>
      <p class="mb-1"><strong>Department:</strong> <?php echo $doctor['department_name']; ?></p>
      <p class="mb-1"><strong>Experience:</strong> <?php echo $doctor['experience']; ?> years</p>
      <p class="mb-1"><strong>Consultation Fee:</strong> Rs. <?php echo number_format($doctor['consultation_fee'], 2); ?></p>
      <p class="mb-1"><strong>Available Days:</strong> <?php echo $doctor['available_days']; ?></p>
      <p class="mb-0"><strong>Timing:</strong> <?php echo date('h:i A', strtotime($doctor['start_time'])) . " - " . date('h:i A', strtotime($doctor['end_time'])); ?></p>
    </div>

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
      <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

      <div class="mb-3">
        <label class="form-label"><strong>Select Date</strong></label>
        <input type="date"
               name="date"
               class="form-control"
               value="<?php echo htmlspecialchars($date); ?>"
               min="<?php echo date('Y-m-d'); ?>"
               onchange="window.location='book_appointment.php?doctor_id=<?php echo $doctor_id; ?>&date='+this.value;"
               required>
      </div>

      <div class="mb-3">
        <label class="form-label"><strong>Select Time Slot</strong></label>
        <select name="time" class="form-control" <?php if ($isFull || !$doctor_available_today) echo "disabled"; ?> required>
          <option value="">-- Choose Time --</option>
          <?php
          $start = strtotime($doctor['start_time']);
          $end = strtotime($doctor['end_time']);
          $interval = 30 * 60;

          while ($start < $end) {
              $time_slot = date('H:i:s', $start);
              $time_label = date('h:i A', $start);

              $stmt = mysqli_prepare($con, "SELECT appointment_id FROM appointments
                                            WHERE doctor_id = ?
                                            AND appointment_date = ?
                                            AND appointment_time = ?
                                            AND status != 'Cancelled'
                                            LIMIT 1");
              mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $date, $time_slot);
              mysqli_stmt_execute($stmt);
              $slot_result = mysqli_stmt_get_result($stmt);
              $is_booked = mysqli_num_rows($slot_result) > 0;

              if ($is_booked) {
                  echo "<option disabled>{$time_label} (Booked)</option>";
              } else {
                  echo "<option value='{$time_slot}'>{$time_label}</option>";
              }

              $start += $interval;
          }
          ?>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="book" class="btn btn-success" <?php if ($isFull || !$doctor_available_today) echo "disabled"; ?>>Book Appointment</button>
        <a href="../public/doctors.php" class="btn btn-secondary">Back</a>
      </div>
    </form>

  </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
