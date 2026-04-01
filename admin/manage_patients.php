<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM patients WHERE 1=1";

if (!empty($search)) {
    $search_term = mysqli_real_escape_string($con, $search);
    $query .= " AND (
                first_name LIKE '%$search_term%'
                OR last_name LIKE '%$search_term%'
                OR CONCAT(first_name, ' ', last_name) LIKE '%$search_term%'
                OR email LIKE '%$search_term%'
                OR phone LIKE '%$search_term%'
              )";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Patients</title>
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

<div class="container mt-5">
<h3 class="mb-4">Manage Patients</h3>

<form method="GET" class="row g-2 mb-4">
  <div class="col-md-9">
    <input type="text" name="search" class="form-control" placeholder="Search patient by name, email or phone" value="<?php echo htmlspecialchars($search); ?>">
  </div>
  <div class="col-md-3 d-flex gap-2">
    <button type="submit" class="btn btn-info w-100">Search</button>
    <a href="manage_patients.php" class="btn btn-secondary w-100">Reset</a>
  </div>
</form>

<table class="table table-bordered table-hover align-middle">
  <tr class="table-light">
    <th>Patient ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Gender</th>
    <th>Registered On</th>
    <th>Action</th>
  </tr>

  <?php if (mysqli_num_rows($result) > 0) { ?>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo $row['patient_id']; ?></td>
        <td><?php echo trim($row['first_name'] . ' ' . $row['last_name']); ?></td>
        <td><?php echo !empty($row['email']) ? $row['email'] : 'N/A'; ?></td>
        <td><?php echo !empty($row['phone']) ? $row['phone'] : 'N/A'; ?></td>
        <td><?php echo !empty($row['gender']) ? $row['gender'] : 'N/A'; ?></td>
        <td><?php echo !empty($row['created_at']) ? date("d M Y", strtotime($row['created_at'])) : 'N/A'; ?></td>
        <td>
          <a href="view_patient.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm btn-primary">View Details</a>
        </td>
      </tr>
    <?php } ?>
  <?php } else { ?>
    <tr>
      <td colspan="7" class="text-center text-muted">No patients found.</td>
    </tr>
  <?php } ?>
</table>

</div>
</body>
</html>
