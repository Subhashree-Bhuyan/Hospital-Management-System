<?php
session_start();
include("../config/db.php");

/* check doctor login */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* get doctor info */
$doc_query = "SELECT doctor_id, first_name, last_name FROM doctors WHERE user_id='$user_id'";
$doc_result = mysqli_query($con, $doc_query);
$doc_row = mysqli_fetch_assoc($doc_result);
$doctor_id = $doc_row['doctor_id'];
$doctor_name = $doc_row['first_name'] . " " . $doc_row['last_name'];

/* fetch summary stats */
$stats_query = "SELECT 
                COUNT(*) as total_appt,
                SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending_appt,
                SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed_appt,
                SUM(CASE WHEN status='Cancelled' THEN 1 ELSE 0 END) as cancelled_appt
                FROM appointments WHERE doctor_id='$doctor_id'";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

/* fetch appointments */
$query = "SELECT appointments.*, patients.first_name, patients.last_name
          FROM appointments
          JOIN patients ON appointments.patient_id = patients.patient_id
          WHERE doctor_id='$doctor_id'
          ORDER BY appointment_date ASC";

$result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  body { background-color: #f8f9fa; }
  .navbar { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
  .stat-card { border-left: 4px solid #28a745; background: white; }
  .stat-card h4 { color: #28a745; }
  .appointment-card { border-left: 4px solid #0d6efd; }
</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">🏥 Hospital Management</span>
    <div>
      <span class="text-white me-3">👨‍⚕️ Dr. <?php echo $doctor_name; ?></span>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

<!-- Summary Stats -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card stat-card">
      <div class="card-body">
        <h4><?php echo $stats['total_appt'] ?? 0; ?></h4>
        <p class="text-muted">Total Appointments</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card stat-card" style="border-left-color: #ffc107;">
      <div class="card-body">
        <h4 style="color: #ffc107;"><?php echo $stats['pending_appt'] ?? 0; ?></h4>
        <p class="text-muted">Pending</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card stat-card" style="border-left-color: #198754;">
      <div class="card-body">
        <h4 style="color: #198754;"><?php echo $stats['completed_appt'] ?? 0; ?></h4>
        <p class="text-muted">Completed</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card stat-card" style="border-left-color: #dc3545;">
      <div class="card-body">
        <h4 style="color: #dc3545;"><?php echo $stats['cancelled_appt'] ?? 0; ?></h4>
        <p class="text-muted">Cancelled</p>
      </div>
    </div>
  </div>
</div>

<!-- Appointments Section -->
<h3 class="mb-3">📋 Appointments</h3>

<?php if(mysqli_num_rows($result) > 0) { ?>
  <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <div class="card appointment-card mb-3">
      <div class="card-header bg-light">
        <div class="row">
          <div class="col-md-6">
            <strong>👤 <?php echo $row['first_name'] . " " . $row['last_name']; ?></strong>
          </div>
          <div class="col-md-6 text-end">
            <?php 
            if($row['status'] == 'Completed'){
              echo "<span class='badge bg-success'>✓ Completed</span>";
            } elseif($row['status'] == 'Pending'){
              echo "<span class='badge bg-warning text-dark'>⏳ Pending</span>";
            } else {
              echo "<span class='badge bg-danger'>✗ Cancelled</span>";
            }
            ?>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <strong>📅 Date:</strong> <?php echo $row['appointment_date']; ?>
          </div>
          <div class="col-md-4">
            <strong>🕐 Time:</strong> <?php echo $row['appointment_time']; ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <a href="add_notes.php?id=<?php echo $row['appointment_id']; ?>" class="btn btn-info btn-sm">
              📝 Add/Edit Notes
            </a>
            <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=Completed" class="btn btn-success btn-sm">
              ✓ Mark Complete
            </a>
            <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=Cancelled" class="btn btn-danger btn-sm">
              ✗ Cancel
            </a>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
<?php } else { ?>
  <div class="alert alert-info">No appointments scheduled.</div>
<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>