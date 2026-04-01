<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

/* add department */
if (isset($_POST['add_department'])) {
    $department_name = trim($_POST['department_name']);

    if (empty($department_name)) {
        $error_msg = "Department name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $department_name)) {
        $error_msg = "Department name should contain only letters and spaces.";
    } else {
        $stmt = mysqli_prepare($con, "SELECT department_id FROM departments WHERE department_name = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $department_name);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $error_msg = "Department already exists.";
        } else {
            $stmt = mysqli_prepare($con, "INSERT INTO departments (department_name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $department_name);

            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Department added successfully.";
            } else {
                $error_msg = "Error adding department.";
            }
        }
    }
}

/* delete department */
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $department_id = (int) $_GET['delete'];

    $doctor_check = mysqli_query($con, "SELECT doctor_id FROM doctors WHERE department_id='$department_id' LIMIT 1");

    if (mysqli_num_rows($doctor_check) > 0) {
        $error_msg = "Department cannot be deleted because doctors are assigned to it.";
    } else {
        $delete_query = mysqli_query($con, "DELETE FROM departments WHERE department_id='$department_id' LIMIT 1");

        if ($delete_query) {
            $success_msg = "Department deleted successfully.";
        } else {
            $error_msg = "Error deleting department.";
        }
    }
}

$list_result = mysqli_query($con, "SELECT * FROM departments ORDER BY department_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Department</title>
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
  <h3 class="mb-4">Add Department</h3>

  <?php if (!empty($success_msg)) { ?>
    <div class="alert alert-success"><?php echo $success_msg; ?></div>
  <?php } ?>

  <?php if (!empty($error_msg)) { ?>
    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
  <?php } ?>

  <form method="POST" class="mb-4">
    <div class="mb-3">
      <label class="form-label">Department Name</label>
      <input type="text" name="department_name" class="form-control" pattern="[A-Za-z ]+" required>
    </div>
    <button type="submit" name="add_department" class="btn btn-success">Add Department</button>
  </form>

  <div class="card shadow-sm">
    <div class="card-header bg-light">
      <strong>Department List</strong>
    </div>
    <div class="card-body">
      <table class="table table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Department Name</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($list_result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($list_result)) { ?>
              <tr>
                <td><?php echo $row['department_id']; ?></td>
                <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                <td>
                  <a href="add_department.php?delete=<?php echo $row['department_id']; ?>"
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('Are you sure you want to delete this department?');">
                     Delete
                  </a>
                </td>
              </tr>
            <?php } ?>
          <?php } else { ?>
            <tr>
              <td colspan="3" class="text-center text-muted">No departments found.</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
