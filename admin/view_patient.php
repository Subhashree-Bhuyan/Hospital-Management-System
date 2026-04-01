<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$patient_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($patient_id <= 0) {
    die("Invalid patient.");
}

$patient_query = "SELECT * FROM patients WHERE patient_id='$patient_id' LIMIT 1";
$patient_result = mysqli_query($con, $patient_query);
$patient = mysqli_fetch_assoc($patient_result);

if (!$patient) {
    die("Patient not found.");
}

$appointments_query = "SELECT a.*, d.first_name AS doctor_first, d.last_name AS doctor_last
                       FROM appointments a
                       LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                       WHERE a.patient_id='$patient_id'
                       ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments_result = mysqli_query($con, $appointments_query);

$bills_query = "SELECT b.*, d.first_name AS doctor_first, d.last_name AS doctor_last, a.appointment_date
                FROM bills b
                LEFT JOIN doctors d ON b.doctor_id = d.doctor_id
                LEFT JOIN appointments a ON b.appointment_id = a.appointment_id
                WHERE b.patient_id='$patient_id'
                ORDER BY b.created_at DESC";
$bills_result = mysqli_query($con, $bills_query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Patient Details</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .navbar { background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); position: sticky; top: 0; z-index: 1000; }
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
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
      <h4 class="mb-0">Patient Profile</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <p><strong>Name:</strong> <?php echo trim($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
          <p><strong>Email:</strong> <?php echo !empty($patient['email']) ? $patient['email'] : 'N/A'; ?></p>
          <p><strong>Phone:</strong> <?php echo !empty($patient['phone']) ? $patient['phone'] : 'N/A'; ?></p>
        </div>
        <div class="col-md-6">
          <p><strong>Gender:</strong> <?php echo !empty($patient['gender']) ? $patient['gender'] : 'N/A'; ?></p>
          <p><strong>Date of Birth:</strong> <?php echo !empty($patient['date_of_birth']) ? $patient['date_of_birth'] : 'N/A'; ?></p>
          <p><strong>Registered On:</strong> <?php echo !empty($patient['created_at']) ? date("d M Y", strtotime($patient['created_at'])) : 'N/A'; ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
      <h5 class="mb-0">Appointment History</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Doctor</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($appointments_result) > 0) { ?>
              <?php while ($row = mysqli_fetch_assoc($appointments_result)) { ?>
                <tr>
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
                <td colspan="4" class="text-center text-muted">No appointments found.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-5">
    <div class="card-header bg-light">
      <h5 class="mb-0">Billing History</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Bill ID</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Total</th>
              <th>Paid</th>
              <th>Pending</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($bills_result) > 0) { ?>
              <?php while ($row = mysqli_fetch_assoc($bills_result)) { ?>
                <tr>
                  <td><?php echo $row['bill_id']; ?></td>
                  <td>Dr. <?php echo $row['doctor_first'] . ' ' . $row['doctor_last']; ?></td>
                  <td><?php echo $row['appointment_date']; ?></td>
                  <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                  <td>Rs. <?php echo number_format($row['paid_amount'], 2); ?></td>
                  <td>Rs. <?php echo number_format($row['pending_amount'], 2); ?></td>
                  <td>
                    <?php
                    if ($row['status'] == 'Paid') {
                        echo '<span class="badge bg-success">Paid</span>';
                    } elseif ($row['status'] == 'Partial') {
                        echo '<span class="badge bg-warning text-dark">Partial</span>';
                    } else {
                        echo '<span class="badge bg-danger">Pending</span>';
                    }
                    ?>
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="7" class="text-center text-muted">No bills found.</td>
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
