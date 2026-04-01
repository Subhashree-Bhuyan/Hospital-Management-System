<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

/* summary counts */
$doctor_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM doctors"))['total'];
$patient_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM patients"))['total'];
$appointment_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments"))['total'];
$pending_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments WHERE status='Pending'"))['total'];
$completed_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments WHERE status='Completed'"))['total'];
$cancelled_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments WHERE status='Cancelled'"))['total'];

$total_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT IFNULL(SUM(paid_amount),0) AS total FROM bills"))['total'];
$total_pending = mysqli_fetch_assoc(mysqli_query($con, "SELECT IFNULL(SUM(pending_amount),0) AS total FROM bills"))['total'];

/* recent appointments */
$recent_query = "SELECT a.*, 
                        p.first_name AS patient_first, p.last_name AS patient_last,
                        d.first_name AS doctor_first, d.last_name AS doctor_last
                 FROM appointments a
                 LEFT JOIN patients p ON a.patient_id = p.patient_id
                 LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                 ORDER BY a.created_at DESC
                 LIMIT 8";
$recent_result = mysqli_query($con, $recent_query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { background-color: #f8f9fa; }
  .navbar { background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); position: sticky; top: 0; z-index: 1000; }
  .card-box { border: none; border-radius: 14px; }
</style>
</head>

<body>
<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand mb-0 h1" href="#">
      <img src="../assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-middle me-2 rounded-circle border border-white" style="object-fit: cover;">
      Admin Panel
    </a>
    <div>
      <a href="dashboard.php" class="btn btn-dark btn-sm">Dashboard</a>
      <a href="manage_doctors.php" class="btn btn-primary btn-sm">Doctors</a>
      <a href="add_doctor.php" class="btn btn-success btn-sm">Add Doctor</a>
      <a href="manage_patients.php" class="btn btn-info btn-sm">Patients</a>
      <a href="manage_appointments.php" class="btn btn-light btn-sm">Appointments</a>
      <a href="manage_bills.php" class="btn btn-warning btn-sm">Manage Bills</a>
      <a href="add_department.php" class="btn btn-secondary btn-sm">Departments</a>
      <a href="reports.php" class="btn btn-light btn-sm">Reports</a>
      <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h2 class="mb-4">Admin Dashboard</h2>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Total Doctors</h6>
          <h2><?php echo $doctor_count; ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Total Patients</h6>
          <h2><?php echo $patient_count; ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Appointments</h6>
          <h2><?php echo $appointment_count; ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Revenue Collected</h6>
          <h2>Rs. <?php echo number_format($total_revenue, 2); ?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <h6 class="text-muted">Pending Appointments</h6>
          <h3><?php echo $pending_count; ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-start border-success border-4">
        <div class="card-body">
          <h6 class="text-muted">Completed Appointments</h6>
          <h3><?php echo $completed_count; ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-start border-danger border-4">
        <div class="card-body">
          <h6 class="text-muted">Cancelled Appointments</h6>
          <h3><?php echo $cancelled_count; ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Billing Summary</h5>
          <p class="mb-2"><strong>Total Revenue Collected:</strong> Rs. <?php echo number_format($total_revenue, 2); ?></p>
          <p class="mb-0"><strong>Total Pending Amount:</strong> Rs. <?php echo number_format($total_pending, 2); ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Quick Actions</h5>
          <div class="d-grid gap-2">
            <a href="manage_doctors.php" class="btn btn-primary">Manage Doctors</a>
            <a href="manage_patients.php" class="btn btn-info">Manage Patients</a>
            <a href="manage_bills.php" class="btn btn-warning">Manage Bills</a>
            <a href="reports.php" class="btn btn-dark">View Reports</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Recent Appointments</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Patient</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($recent_result) > 0) { ?>
              <?php while ($row = mysqli_fetch_assoc($recent_result)) { ?>
                <tr>
                  <td><?php echo $row['patient_first'] . ' ' . $row['patient_last']; ?></td>
                  <td>Dr. <?php echo $row['doctor_first'] . ' ' . $row['doctor_last']; ?></td>
                  <td><?php echo $row['appointment_date']; ?></td>
                  <td><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></td>
                  <td>
                    <?php
                    if ($row['status'] == 'Completed') {
                        echo '<span class="badge bg-success">Completed</span>';
                    } elseif ($row['status'] == 'Pending') {
                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                    } else {
                        echo '<span class="badge bg-danger">Cancelled</span>';
                    }
                    ?>
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="5" class="text-center text-muted">No recent appointments found.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
