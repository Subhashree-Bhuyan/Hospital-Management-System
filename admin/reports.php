<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

/* overall stats */
$total_appointments = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments"))['total'];
$completed_appointments = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments WHERE status='Completed'"))['total'];
$pending_appointments = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments WHERE status='Pending'"))['total'];
$cancelled_appointments = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM appointments WHERE status='Cancelled'"))['total'];

$total_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT IFNULL(SUM(paid_amount),0) AS total FROM bills"))['total'];
$total_pending = mysqli_fetch_assoc(mysqli_query($con, "SELECT IFNULL(SUM(pending_amount),0) AS total FROM bills"))['total'];
$total_bills = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bills"))['total'];

/* bill status summary */
$paid_bills = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bills WHERE status='Paid'"))['total'];
$partial_bills = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bills WHERE status='Partial'"))['total'];
$pending_bills = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bills WHERE status='Pending'"))['total'];

/* doctor performance */
$doctor_performance_query = "SELECT 
                                d.first_name,
                                d.last_name,
                                COUNT(a.appointment_id) AS total_completed
                             FROM doctors d
                             LEFT JOIN appointments a 
                               ON d.doctor_id = a.doctor_id AND a.status='Completed'
                             GROUP BY d.doctor_id
                             ORDER BY total_completed DESC, d.first_name ASC";
$doctor_performance_result = mysqli_query($con, $doctor_performance_query);

/* department report */
$department_report_query = "SELECT 
                                dep.department_name,
                                COUNT(a.appointment_id) AS total_appointments
                            FROM departments dep
                            LEFT JOIN doctors d ON dep.department_id = d.department_id
                            LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                            GROUP BY dep.department_id
                            ORDER BY total_appointments DESC, dep.department_name ASC";
$department_report_result = mysqli_query($con, $department_report_query);

/* monthly appointment trend */
$monthly_appointments_query = "SELECT 
                                  DATE_FORMAT(appointment_date, '%Y-%m') AS month_label,
                                  COUNT(*) AS total_appointments
                               FROM appointments
                               GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
                               ORDER BY month_label DESC
                               LIMIT 6";
$monthly_appointments_result = mysqli_query($con, $monthly_appointments_query);

/* monthly revenue trend */
$monthly_revenue_query = "SELECT 
                             DATE_FORMAT(created_at, '%Y-%m') AS month_label,
                             IFNULL(SUM(paid_amount),0) AS total_revenue,
                             IFNULL(SUM(pending_amount),0) AS total_pending
                          FROM bills
                          GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                          ORDER BY month_label DESC
                          LIMIT 6";
$monthly_revenue_result = mysqli_query($con, $monthly_revenue_query);

/* top 5 doctors by total appointments */
$top_doctors_query = "SELECT 
                         d.first_name,
                         d.last_name,
                         dep.department_name,
                         COUNT(a.appointment_id) AS total_appointments
                      FROM doctors d
                      LEFT JOIN departments dep ON d.department_id = dep.department_id
                      LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                      GROUP BY d.doctor_id
                      ORDER BY total_appointments DESC, d.first_name ASC
                      LIMIT 5";
$top_doctors_result = mysqli_query($con, $top_doctors_query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { background-color: #f8f9fa; }
  .navbar { background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); position: sticky; top: 0; z-index: 1000; }
  .card-box { border: none; border-radius: 12px; }
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
  <h2 class="mb-4">Reports & Analytics</h2>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Total Appointments</h6>
          <h3><?php echo $total_appointments; ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Completed</h6>
          <h3><?php echo $completed_appointments; ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Revenue Collected</h6>
          <h3>Rs. <?php echo number_format($total_revenue, 2); ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-box shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Pending Amount</h6>
          <h3>Rs. <?php echo number_format($total_pending, 2); ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <h6 class="text-muted">Pending Appointments</h6>
          <h4><?php echo $pending_appointments; ?></h4>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-start border-danger border-4">
        <div class="card-body">
          <h6 class="text-muted">Cancelled Appointments</h6>
          <h4><?php echo $cancelled_appointments; ?></h4>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-start border-info border-4">
        <div class="card-body">
          <h6 class="text-muted">Total Bills</h6>
          <h4><?php echo $total_bills; ?></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
      <h5 class="mb-0">Bill Status Summary</h5>
    </div>
    <div class="card-body">
      <div class="row text-center">
        <div class="col-md-4">
          <div class="p-3 bg-success-subtle rounded">
            <h6>Paid Bills</h6>
            <h3><?php echo $paid_bills; ?></h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 bg-warning-subtle rounded">
            <h6>Partial Bills</h6>
            <h3><?php echo $partial_bills; ?></h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 bg-danger-subtle rounded">
            <h6>Pending Bills</h6>
            <h3><?php echo $pending_bills; ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white">
          <h5 class="mb-0">Monthly Appointment Trend</h5>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Month</th>
                <th>Total Appointments</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($monthly_appointments_result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($monthly_appointments_result)) { ?>
                  <tr>
                    <td><?php echo $row['month_label']; ?></td>
                    <td><?php echo $row['total_appointments']; ?></td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="2" class="text-center text-muted">No appointment trend data found.</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white">
          <h5 class="mb-0">Monthly Revenue Trend</h5>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Month</th>
                <th>Revenue</th>
                <th>Pending</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($monthly_revenue_result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($monthly_revenue_result)) { ?>
                  <tr>
                    <td><?php echo $row['month_label']; ?></td>
                    <td>Rs. <?php echo number_format($row['total_revenue'], 2); ?></td>
                    <td>Rs. <?php echo number_format($row['total_pending'], 2); ?></td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="3" class="text-center text-muted">No revenue trend data found.</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
      <h5 class="mb-0">Top Doctors By Appointment Volume</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Doctor</th>
            <th>Department</th>
            <th>Total Appointments</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($top_doctors_result)) { ?>
            <tr>
              <td>Dr. <?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
              <td><?php echo $row['department_name']; ?></td>
              <td><?php echo $row['total_appointments']; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white">
          <h5 class="mb-0">Doctor Performance</h5>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Doctor</th>
                <th>Completed Appointments</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($doctor_performance_result)) { ?>
                <tr>
                  <td>Dr. <?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                  <td><?php echo $row['total_completed']; ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white">
          <h5 class="mb-0">Department Performance</h5>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Department</th>
                <th>Total Appointments</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($department_report_result)) { ?>
                <tr>
                  <td><?php echo $row['department_name']; ?></td>
                  <td><?php echo $row['total_appointments']; ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
</body>
</html>
