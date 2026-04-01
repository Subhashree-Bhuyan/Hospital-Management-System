<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_date = isset($_GET['appointment_date']) ? trim($_GET['appointment_date']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_department = isset($_GET['department_id']) ? (int) $_GET['department_id'] : 0;

/* department dropdown */
$dept_query = "SELECT department_id, department_name FROM departments ORDER BY department_name ASC";
$dept_result = mysqli_query($con, $dept_query);

/* build query */
$query = "SELECT 
            a.*,
            p.first_name AS patient_first,
            p.last_name AS patient_last,
            p.email AS patient_email,
            d.first_name AS doctor_first,
            d.last_name AS doctor_last,
            dep.department_name
          FROM appointments a
          LEFT JOIN patients p ON a.patient_id = p.patient_id
          LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
          LEFT JOIN departments dep ON d.department_id = dep.department_id
          WHERE 1=1";

if (!empty($search)) {
    $safe_search = mysqli_real_escape_string($con, $search);
    $query .= " AND (
                p.first_name LIKE '%$safe_search%'
                OR p.last_name LIKE '%$safe_search%'
                OR CONCAT(p.first_name, ' ', p.last_name) LIKE '%$safe_search%'
                OR p.email LIKE '%$safe_search%'
                OR d.first_name LIKE '%$safe_search%'
                OR d.last_name LIKE '%$safe_search%'
                OR CONCAT(d.first_name, ' ', d.last_name) LIKE '%$safe_search%'
              )";
}

if (!empty($filter_date)) {
    $safe_date = mysqli_real_escape_string($con, $filter_date);
    $query .= " AND a.appointment_date = '$safe_date'";
}

if (!empty($filter_status)) {
    $safe_status = mysqli_real_escape_string($con, $filter_status);
    $query .= " AND a.status = '$safe_status'";
}

if ($filter_department > 0) {
    $query .= " AND d.department_id = '$filter_department'";
}

$query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$result = mysqli_query($con, $query);

/* summary based on filters */
$summary_query = "SELECT 
                    COUNT(*) AS total_appointments,
                    SUM(CASE WHEN a.status='Pending' THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE WHEN a.status='Completed' THEN 1 ELSE 0 END) AS completed_count,
                    SUM(CASE WHEN a.status='Cancelled' THEN 1 ELSE 0 END) AS cancelled_count
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.patient_id
                  LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                  WHERE 1=1";

if (!empty($search)) {
    $summary_query .= " AND (
                        p.first_name LIKE '%$safe_search%'
                        OR p.last_name LIKE '%$safe_search%'
                        OR CONCAT(p.first_name, ' ', p.last_name) LIKE '%$safe_search%'
                        OR p.email LIKE '%$safe_search%'
                        OR d.first_name LIKE '%$safe_search%'
                        OR d.last_name LIKE '%$safe_search%'
                        OR CONCAT(d.first_name, ' ', d.last_name) LIKE '%$safe_search%'
                      )";
}

if (!empty($filter_date)) {
    $summary_query .= " AND a.appointment_date = '$safe_date'";
}

if (!empty($filter_status)) {
    $summary_query .= " AND a.status = '$safe_status'";
}

if ($filter_department > 0) {
    $summary_query .= " AND d.department_id = '$filter_department'";
}

$summary_result = mysqli_query($con, $summary_query);
$summary = mysqli_fetch_assoc($summary_result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Appointments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { background-color: #f8f9fa; }
  .navbar { background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); position: sticky; top: 0; z-index: 1000; }
  .stat-card { border: none; border-radius: 14px; }
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
  <h2 class="mb-4">Manage Appointments</h2>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="row g-2">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control" placeholder="Search patient or doctor" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
          <input type="date" name="appointment_date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="Pending" <?php if ($filter_status == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Completed" <?php if ($filter_status == 'Completed') echo 'selected'; ?>>Completed</option>
            <option value="Cancelled" <?php if ($filter_status == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="department_id" class="form-select">
            <option value="0">All Departments</option>
            <?php while ($dept = mysqli_fetch_assoc($dept_result)) { ?>
              <option value="<?php echo $dept['department_id']; ?>" <?php if ($filter_department == $dept['department_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($dept['department_name']); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100">Filter</button>
          <a href="manage_appointments.php" class="btn btn-secondary w-100">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card stat-card shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Total</h6>
          <h3><?php echo $summary['total_appointments'] ?? 0; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Pending</h6>
          <h3><?php echo $summary['pending_count'] ?? 0; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Completed</h6>
          <h3><?php echo $summary['completed_count'] ?? 0; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card shadow-sm text-center">
        <div class="card-body">
          <h6 class="text-muted">Cancelled</h6>
          <h3><?php echo $summary['cancelled_count'] ?? 0; ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Patient</th>
              <th>Doctor</th>
              <th>Department</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($result) > 0) { ?>
              <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                  <td><?php echo $row['appointment_id']; ?></td>
                  <td>
                    <?php echo $row['patient_first'] . ' ' . $row['patient_last']; ?><br>
                    <small class="text-muted"><?php echo $row['patient_email']; ?></small>
                  </td>
                  <td>Dr. <?php echo $row['doctor_first'] . ' ' . $row['doctor_last']; ?></td>
                  <td><?php echo $row['department_name']; ?></td>
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
                <td colspan="7" class="text-center text-muted">No appointments found.</td>
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
