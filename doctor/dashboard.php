<?php
session_start();
include("../config/db.php");

/* check doctor login */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

/* get doctor info */
$doc_query = "SELECT doctor_id, first_name, last_name FROM doctors WHERE user_id='$user_id' LIMIT 1";
$doc_result = mysqli_query($con, $doc_query);
$doc_row = mysqli_fetch_assoc($doc_result);
$doctor_id = $doc_row['doctor_id'];
$doctor_name = $doc_row['first_name'] . " " . $doc_row['last_name'];

/* filters */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_date = isset($_GET['appointment_date']) ? trim($_GET['appointment_date']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';

/* fetch summary stats */
$stats_query = "SELECT 
                COUNT(*) as total_appt,
                SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending_appt,
                SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed_appt,
                SUM(CASE WHEN status='Cancelled' THEN 1 ELSE 0 END) as cancelled_appt
                FROM appointments WHERE doctor_id='$doctor_id'";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

/* fetch appointments with filters */
$query = "SELECT appointments.*, patients.first_name, patients.last_name, patients.email
          FROM appointments
          JOIN patients ON appointments.patient_id = patients.patient_id
          WHERE appointments.doctor_id='$doctor_id'";

if (!empty($search)) {
    $search_term = mysqli_real_escape_string($con, $search);
    $query .= " AND (
                patients.first_name LIKE '%$search_term%'
                OR patients.last_name LIKE '%$search_term%'
                OR CONCAT(patients.first_name, ' ', patients.last_name) LIKE '%$search_term%'
                OR patients.email LIKE '%$search_term%'
              )";
}

if (!empty($filter_date)) {
    $safe_date = mysqli_real_escape_string($con, $filter_date);
    $query .= " AND appointments.appointment_date='$safe_date'";
}

if (!empty($filter_status)) {
    $safe_status = mysqli_real_escape_string($con, $filter_status);
    $query .= " AND appointments.status='$safe_status'";
}

$query .= " ORDER BY appointments.appointment_date ASC, appointments.appointment_time ASC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { background-color: #f8f9fa; }
  .navbar {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  position: sticky;
  top: 0;
  z-index: 1000;
}
  .stat-card { border-left: 4px solid #28a745; background: white; }
  .stat-card h4 { color: #28a745; }
  .appointment-card { border-left: 4px solid #0d6efd; }
</style>
</head>

<body>

<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand mb-0 h1" href="#">
      <img src="../assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-middle me-2 rounded-circle border border-white" style="object-fit: cover;">
      Doctor Panel
    </a>
    <div>
      <span class="text-white me-3">Dr. <?php echo $doctor_name; ?></span>
      <a href="dashboard.php" class="btn btn-light btn-sm me-2">Dashboard</a>
      <a href="manage_bills.php" class="btn btn-warning btn-sm me-2">Manage Bills</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

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

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search patient by name or email" value="<?php echo htmlspecialchars($search); ?>">
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

<h3 class="mb-3">Appointments</h3>

<?php if (mysqli_num_rows($result) > 0) { ?>
  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <div class="card appointment-card mb-3">
      <div class="card-header bg-light">
        <div class="row">
          <div class="col-md-6">
            <strong><?php echo $row['first_name'] . " " . $row['last_name']; ?></strong>
            <div class="text-muted small"><?php echo $row['email']; ?></div>
          </div>
          <div class="col-md-6 text-end">
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
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Date:</strong> <?php echo $row['appointment_date']; ?>
          </div>
          <div class="col-md-4">
            <strong>Time:</strong> <?php echo date("h:i A", strtotime($row['appointment_time'])); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <a href="add_notes.php?id=<?php echo $row['appointment_id']; ?>" class="btn btn-info btn-sm">Add/Edit Notes</a>

            <?php if ($row['status'] != 'Completed') { ?>
              <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=Completed"
                 class="btn btn-success btn-sm"
                 onclick="return confirm('Mark this appointment as completed? A consultation bill will be generated automatically.');">
                Mark Complete
              </a>
            <?php } ?>

            <?php if ($row['status'] != 'Cancelled') { ?>
              <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=Cancelled"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Are you sure you want to cancel this appointment?');">
                Cancel
              </a>
            <?php } ?>

            <?php if ($row['status'] == 'Completed') { ?>
              <div class="mt-2">
                <span class="badge bg-success">Consultation completed</span>
                <span class="badge bg-primary">Bill generated automatically</span>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
<?php } else { ?>
  <div class="alert alert-info">No appointments found for the selected filters.</div>
<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
