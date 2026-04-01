<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid Request";
    exit();
}

$id = (int) $_GET['id'];

$dept_query = "SELECT department_id, department_name FROM departments ORDER BY department_name ASC";
$dept_result = mysqli_query($con, $dept_query);

/* fetch doctor */
$query = "SELECT * FROM doctors WHERE doctor_id='$id' LIMIT 1";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Doctor not found";
    exit();
}

$selected_days = array_map('trim', explode(",", $row['available_days']));

/* update doctor */
if (isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $first = trim($_POST['first_name']);
    $middle = trim($_POST['middle_name']);
    $last = trim($_POST['last_name']);
    $department_id = (int) $_POST['department_id'];
    $phone = trim($_POST['phone']);
    $experience = (int) $_POST['experience'];
    $available_days = isset($_POST['available_days']) ? implode(",", $_POST['available_days']) : "";
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $consultation_fee = floatval($_POST['consultation_fee']);

    if (empty($first) || empty($last) || empty($phone) || empty($available_days) || empty($start_time) || empty($end_time)) {
        $error_msg = "Please fill all required fields.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $first)) {
        $error_msg = "First name should contain only letters and spaces.";
    } elseif (!empty($middle) && !preg_match("/^[a-zA-Z ]+$/", $middle)) {
        $error_msg = "Middle name should contain only letters and spaces.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $last)) {
        $error_msg = "Last name should contain only letters and spaces.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error_msg = "Phone number must be exactly 10 digits.";
    } elseif ($experience < 0 || $experience > 60) {
        $error_msg = "Experience should be between 0 and 60 years.";
    } elseif ($consultation_fee < 0) {
        $error_msg = "Consultation fee cannot be negative.";
    } elseif ($start_time >= $end_time) {
        $error_msg = "End time must be greater than start time.";
    } else {
        $image_sql = "";

        if (!empty($_FILES['image']['name'])) {
            $image_name = basename($_FILES['image']['name']);
            $tmp_name = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp_name, "../assets/images/" . $image_name);
            $image_sql = ", image='$image_name'";
        }

        $update = "UPDATE doctors SET 
                    title='$title',
                    first_name='$first',
                    middle_name='$middle',
                    last_name='$last',
                    department_id='$department_id',
                    phone='$phone',
                    experience='$experience',
                    available_days='$available_days',
                    start_time='$start_time',
                    end_time='$end_time',
                    consultation_fee='$consultation_fee'
                    $image_sql
                   WHERE doctor_id='$id'";

        if (mysqli_query($con, $update)) {
            $success_msg = "Doctor updated successfully.";

            $result = mysqli_query($con, "SELECT * FROM doctors WHERE doctor_id='$id' LIMIT 1");
            $row = mysqli_fetch_assoc($result);
            $selected_days = array_map('trim', explode(",", $row['available_days']));
        } else {
            $error_msg = "Error updating doctor.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Doctor</title>
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
<h3 class="mb-4">Edit Doctor</h3>

<?php if (!empty($success_msg)) { ?>
  <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php } ?>

<?php if (!empty($error_msg)) { ?>
  <div class="alert alert-danger"><?php echo $error_msg; ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">
  <select name="title" class="form-control mb-2">
    <option value="Dr" <?php if ($row['title'] == 'Dr') echo 'selected'; ?>>Dr</option>
    <option value="Mr" <?php if ($row['title'] == 'Mr') echo 'selected'; ?>>Mr</option>
    <option value="Mrs" <?php if ($row['title'] == 'Mrs') echo 'selected'; ?>>Mrs</option>
    <option value="Miss" <?php if ($row['title'] == 'Miss') echo 'selected'; ?>>Miss</option>
    <option value="Ms" <?php if ($row['title'] == 'Ms') echo 'selected'; ?>>Ms</option>
  </select>

  <input type="text" name="first_name" value="<?php echo htmlspecialchars($row['first_name']); ?>" class="form-control mb-2" pattern="[A-Za-z ]+" required>
  <input type="text" name="middle_name" value="<?php echo htmlspecialchars($row['middle_name']); ?>" class="form-control mb-2" pattern="[A-Za-z ]*">
  <input type="text" name="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>" class="form-control mb-2" pattern="[A-Za-z ]+" required>

  <select name="department_id" class="form-control mb-2" required>
    <option value="">Select Department</option>
    <?php while ($dept = mysqli_fetch_assoc($dept_result)) { ?>
      <option value="<?php echo $dept['department_id']; ?>" <?php if ($row['department_id'] == $dept['department_id']) echo 'selected'; ?>>
        <?php echo htmlspecialchars($dept['department_name']); ?>
      </option>
    <?php } ?>
  </select>

  <input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" class="form-control mb-2" pattern="[0-9]{10}" maxlength="10" required>
  <input type="number" name="experience" value="<?php echo $row['experience']; ?>" class="form-control mb-2" min="0" max="60" required>

  <div class="mb-3">
    <label class="form-label"><strong>Available Days</strong></label>
    <div class="row">
      <div class="col-md-3 form-check">
        <input class="form-check-input" type="checkbox" name="available_days[]" value="Monday" id="edit_day_mon" <?php if (in_array("Monday", $selected_days)) echo "checked"; ?>>
        <label class="form-check-label" for="edit_day_mon">Monday</label>
      </div>
      <div class="col-md-3 form-check">
        <input class="form-check-input" type="checkbox" name="available_days[]" value="Tuesday" id="edit_day_tue" <?php if (in_array("Tuesday", $selected_days)) echo "checked"; ?>>
        <label class="form-check-label" for="edit_day_tue">Tuesday</label>
      </div>
      <div class="col-md-3 form-check">
        <input class="form-check-input" type="checkbox" name="available_days[]" value="Wednesday" id="edit_day_wed" <?php if (in_array("Wednesday", $selected_days)) echo "checked"; ?>>
        <label class="form-check-label" for="edit_day_wed">Wednesday</label>
      </div>
      <div class="col-md-3 form-check">
        <input class="form-check-input" type="checkbox" name="available_days[]" value="Thursday" id="edit_day_thu" <?php if (in_array("Thursday", $selected_days)) echo "checked"; ?>>
        <label class="form-check-label" for="edit_day_thu">Thursday</label>
      </div>
      <div class="col-md-3 form-check mt-2">
        <input class="form-check-input" type="checkbox" name="available_days[]" value="Friday" id="edit_day_fri" <?php if (in_array("Friday", $selected_days)) echo "checked"; ?>>
        <label class="form-check-label" for="edit_day_fri">Friday</label>
      </div>
      <div class="col-md-3 form-check mt-2">
        <input class="form-check-input" type="checkbox" name="available_days[]" value="Saturday" id="edit_day_sat" <?php if (in_array("Saturday", $selected_days)) echo "checked"; ?>>
        <label class="form-check-label" for="edit_day_sat">Saturday</label>
      </div>
      <div class="col-md-3 form-check mt-2">
        <input class="form-check-input" type="checkbox" name="available_days[]" value="Sunday" id="edit_day_sun" <?php if (in_array("Sunday", $selected_days)) echo "checked"; ?>>
        <label class="form-check-label" for="edit_day_sun">Sunday</label>
      </div>
    </div>
  </div>

  <input type="time" name="start_time" value="<?php echo $row['start_time']; ?>" class="form-control mb-2" required>
  <input type="time" name="end_time" value="<?php echo $row['end_time']; ?>" class="form-control mb-2" required>
  <input type="number" name="consultation_fee" value="<?php echo number_format($row['consultation_fee'], 2, '.', ''); ?>" class="form-control mb-2" min="0" step="0.01" required>

  <?php if (!empty($row['image'])) { ?>
    <img src="../assets/images/<?php echo $row['image']; ?>" width="100" class="mb-3"><br>
  <?php } ?>

  <label>Upload New Image</label>
  <input type="file" name="image" class="form-control mb-3">

  <button type="submit" name="update" class="btn btn-success w-100">Update Doctor</button>
</form>

</div>
</body>
</html>
