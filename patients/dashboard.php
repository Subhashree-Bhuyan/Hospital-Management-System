<?php
session_start();
include("../config/db.php");

/* check patient login */
if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];

/* fetch patient data */
$patient_query = "SELECT * FROM patients WHERE patient_id='$patient_id' LIMIT 1";
$patient_result = mysqli_query($con, $patient_query);
$patient_row = mysqli_fetch_assoc($patient_result);
$patient_name = $patient_row['first_name'] . " " . $patient_row['last_name'];

/* notification messages */
$success_msg = "";
$error_msg = "";

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'cancelled') {
        $success_msg = "Appointment cancelled successfully.";
    } elseif ($_GET['msg'] == 'notallowed') {
        $error_msg = "Only pending appointments can be cancelled.";
    } elseif ($_GET['msg'] == 'notfound') {
        $error_msg = "Appointment not found.";
    } elseif ($_GET['msg'] == 'invalid') {
        $error_msg = "Invalid appointment request.";
    } elseif ($_GET['msg'] == 'error') {
        $error_msg = "Something went wrong. Please try again.";
    }
}

/* filters */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_date = isset($_GET['appointment_date']) ? trim($_GET['appointment_date']) : '';

/* summary stats */
$stats_query = "SELECT 
                COUNT(*) AS total_appt,
                SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) AS pending_appt,
                SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) AS completed_appt,
                SUM(CASE WHEN status='Cancelled' THEN 1 ELSE 0 END) AS cancelled_appt
                FROM appointments
                WHERE patient_id='$patient_id'";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

/* fetch appointments with filters */
$app_query = "SELECT appointments.*, doctors.first_name, doctors.last_name, 
              doctor_notes.diagnosis, doctor_notes.prescription
              FROM appointments
              LEFT JOIN doctors ON appointments.doctor_id = doctors.doctor_id
              LEFT JOIN doctor_notes ON appointments.appointment_id = doctor_notes.appointment_id
              WHERE appointments.patient_id='$patient_id'";

if (!empty($search)) {
    $safe_search = mysqli_real_escape_string($con, $search);
    $app_query .= " AND (
                    doctors.first_name LIKE '%$safe_search%'
                    OR doctors.last_name LIKE '%$safe_search%'
                    OR CONCAT(doctors.first_name, ' ', doctors.last_name) LIKE '%$safe_search%'
                  )";
}

if (!empty($filter_status)) {
    $safe_status = mysqli_real_escape_string($con, $filter_status);
    $app_query .= " AND appointments.status='$safe_status'";
}

if (!empty($filter_date)) {
    $safe_date = mysqli_real_escape_string($con, $filter_date);
    $app_query .= " AND appointments.appointment_date='$safe_date'";
}

$app_query .= " ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC";

$app_result = mysqli_query($con, $app_query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Patient Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { background-color: #f8f9fa; }
  .navbar {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    position: sticky;
    top: 0;
    z-index: 1000;
  }
  .stat-card { border: none; border-radius: 14px; }
  .appointment-card { border-left: 4px solid #20c997; }
</style>

</head>
<body>

<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand mb-0 h1" href="#">
      <img src="../assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-middle me-2 rounded-circle border border-white" style="object-fit: cover;">
      Patient Panel
    </a>
    <div>
      <span class="text-white me-3"><?php echo $patient_name; ?></span>
      <a href="../index.php" class="btn btn-light btn-sm me-2">Home</a>
      <a href="dashboard.php" class="btn btn-light btn-sm me-2">Dashboard</a>
      <a href="view_bills.php" class="btn btn-light btn-sm me-2">My Bills</a>
      <a href="../public/doctors.php" class="btn btn-light btn-sm me-2">View Doctors</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

<h2 class="mb-4">Welcome, <?php echo $patient_row['first_name']; ?></h2>

<?php if (!empty($success_msg)) { ?>
  <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php } ?>

<?php if (!empty($error_msg)) { ?>
  <div class="alert alert-danger"><?php echo $error_msg; ?></div>
<?php } ?>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card stat-card shadow-sm text-center">
      <div class="card-body">
        <h6 class="text-muted">Total Appointments</h6>
        <h3><?php echo $stats['total_appt'] ?? 0; ?></h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card stat-card shadow-sm text-center">
      <div class="card-body">
        <h6 class="text-muted">Pending</h6>
        <h3 class="text-warning"><?php echo $stats['pending_appt'] ?? 0; ?></h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card stat-card shadow-sm text-center">
      <div class="card-body">
        <h6 class="text-muted">Completed</h6>
        <h3 class="text-success"><?php echo $stats['completed_appt'] ?? 0; ?></h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card stat-card shadow-sm text-center">
      <div class="card-body">
        <h6 class="text-muted">Cancelled</h6>
        <h3 class="text-danger"><?php echo $stats['cancelled_appt'] ?? 0; ?></h3>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search by doctor name" value="<?php echo htmlspecialchars($search); ?>">
      </div>

      <div class="col-md-3">
        <input type="date" name="appointment_date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
      </div>

      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="Pending" <?php if ($filter_status == 'Pending') echo 'selected'; ?>>Pending</option>
          <option value="Completed" <?php if ($filter_status == 'Completed') echo 'selected'; ?>>Completed</option>
          <option value="Cancelled" <?php if ($filter_status == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>
      </div>

      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-success w-100">Filter</button>
        <a href="dashboard.php" class="btn btn-secondary w-100">Reset</a>
      </div>
    </form>
  </div>
</div>

<h4 class="mb-3">Your Appointments</h4>

<?php if (mysqli_num_rows($app_result) > 0) { ?>
  <?php while ($row = mysqli_fetch_assoc($app_result)) { ?>
    <div class="card appointment-card shadow-sm mb-3">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
          <strong>Dr. <?php echo $row['first_name'] . " " . $row['last_name']; ?></strong>
          <div class="text-muted small">
            <?php echo $row['appointment_date']; ?> at <?php echo date("h:i A", strtotime($row['appointment_time'])); ?>
          </div>
        </div>
        <div>
          <?php
          if ($row['status'] == 'Completed') {
            echo "<span class='badge bg-success'>Completed</span>";
          } elseif ($row['status'] == 'Pending') {
            echo "<span class='badge bg-warning text-dark'>Pending</span>";
          } else {
            echo "<span class='badge bg-danger'>Cancelled</span>";
          }
          ?>
        </div>
      </div>

      <div class="card-body">
        <?php if (!empty($row['diagnosis']) && !empty($row['prescription'])) { ?>
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Diagnosis:</strong>
              <p class="mb-0"><?php echo $row['diagnosis']; ?></p>
            </div>
            <div class="col-md-6">
              <strong>Prescription:</strong>
              <p><?php echo $row['prescription']; ?></p>
              <a href="download_prescription.php?id=<?php echo $row['appointment_id']; ?>" class="btn btn-sm btn-primary">
                Download Prescription PDF
              </a>
            </div>
          </div>
        <?php } else { ?>
          <p class="text-muted">No doctor notes added yet.</p>
        <?php } ?>

        <div class="d-flex flex-wrap gap-2">
          <a href="download_appointment.php?id=<?php echo $row['appointment_id']; ?>" class="btn btn-sm btn-outline-dark">
            Download Appointment Slip
          </a>

          <?php if ($row['status'] == 'Pending') { ?>
            <a href="cancel_appointment.php?id=<?php echo $row['appointment_id']; ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Are you sure you want to cancel this appointment?');">
               Cancel Appointment
            </a>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>
<?php } else { ?>
  <div class="alert alert-info">No appointments found for the selected filters. <a href="../public/doctors.php">Book one now!</a></div>
<?php } ?>

</div>
</body>
</html>
