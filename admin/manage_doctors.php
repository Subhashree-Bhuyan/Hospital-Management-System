<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
$success_msg = "";
$error_msg = "";

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'deleted') {
        $success_msg = "Doctor deleted successfully.";
    } elseif ($_GET['msg'] == 'hasappointments') {
        $error_msg = "Doctor cannot be deleted because appointment records already exist.";
    } elseif ($_GET['msg'] == 'notfound') {
        $error_msg = "Doctor record not found.";
    } elseif ($_GET['msg'] == 'invalid') {
        $error_msg = "Invalid delete request.";
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$department_id = isset($_GET['department_id']) ? (int) $_GET['department_id'] : 0;


/* fetch departments */
$dept_query = "SELECT department_id, department_name FROM departments ORDER BY department_name ASC";
$dept_result = mysqli_query($con, $dept_query);

/* build query */
$query = "SELECT doctors.*, departments.department_name 
          FROM doctors
          JOIN departments ON doctors.department_id = departments.department_id
          WHERE 1=1";

if (!empty($search)) {
    $search_term = mysqli_real_escape_string($con, $search);
    $query .= " AND (
                doctors.first_name LIKE '%$search_term%'
                OR doctors.last_name LIKE '%$search_term%'
                OR CONCAT(doctors.first_name, ' ', doctors.last_name) LIKE '%$search_term%'
                OR doctors.phone LIKE '%$search_term%'
              )";
}

if ($department_id > 0) {
    $query .= " AND doctors.department_id = '$department_id'";
}

$query .= " ORDER BY doctors.first_name ASC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Doctors</title>
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
<h3 class="mb-4">Manage Doctors</h3>

<?php if (!empty($success_msg)) { ?>
  <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php } ?>

<?php if (!empty($error_msg)) { ?>
  <div class="alert alert-danger"><?php echo $error_msg; ?></div>
<?php } ?>

<form method="GET" class="row g-2 mb-4">
  <div class="col-md-5">
    <input type="text" name="search" class="form-control" placeholder="Search by doctor name or phone" value="<?php echo htmlspecialchars($search); ?>">
  </div>
  <div class="col-md-4">
    <select name="department_id" class="form-select">
      <option value="0">All Departments</option>
      <?php while ($dept = mysqli_fetch_assoc($dept_result)) { ?>
        <option value="<?php echo $dept['department_id']; ?>" <?php if ($department_id == $dept['department_id']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($dept['department_name']); ?>
        </option>
      <?php } ?>
    </select>
  </div>
  <div class="col-md-3 d-flex gap-2">
    <button type="submit" class="btn btn-primary w-100">Search</button>
    <a href="manage_doctors.php" class="btn btn-secondary w-100">Reset</a>
  </div>
</form>

<table class="table table-bordered table-hover align-middle">
<tr class="table-light">
<th>Name</th>
<th>Department</th>
<th>Phone</th>
<th>Experience</th>
<th>Fee</th>
<th>Action</th>
</tr>

<?php if (mysqli_num_rows($result) > 0) { ?>
  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
  <tr>
    <td><?php echo $row['title'] . " " . $row['first_name'] . " " . $row['last_name']; ?></td>
    <td><?php echo $row['department_name']; ?></td>
    <td><?php echo $row['phone']; ?></td>
    <td><?php echo $row['experience']; ?> years</td>
    <td>Rs. <?php echo number_format($row['consultation_fee'], 2); ?></td>
    <td>
      <a href="edit_doctor.php?id=<?php echo $row['doctor_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
      <a href="delete_doctor.php?id=<?php echo $row['doctor_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this doctor?');">Delete</a>
    </td>
  </tr>
  <?php } ?>
<?php } else { ?>
  <tr>
    <td colspan="6" class="text-center text-muted">No doctors found.</td>
  </tr>
<?php } ?>

</table>
</div>
</body>
</html>
