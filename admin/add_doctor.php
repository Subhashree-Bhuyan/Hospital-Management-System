<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

$dept_query = "SELECT * FROM departments ORDER BY department_name ASC";
$dept_result = mysqli_query($con, $dept_query);

if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $first = trim($_POST['first_name']);
    $middle = trim($_POST['middle_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $dept = (int) $_POST['department_id'];
    $phone = trim($_POST['phone']);
    $exp = (int) $_POST['experience'];
    $days = isset($_POST['available_days']) ? implode(",", $_POST['available_days']) : "";
    $start = trim($_POST['start_time']);
    $end = trim($_POST['end_time']);
    $fee = floatval($_POST['consultation_fee']);

    $name = trim($first . " " . $last);

    if (empty($first) || empty($last) || empty($email) || empty($password) || empty($phone) || empty($days) || empty($start) || empty($end)) {
        $error_msg = "Please fill all required fields.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $first)) {
        $error_msg = "First name should contain only letters and spaces.";
    } elseif (!empty($middle) && !preg_match("/^[a-zA-Z ]+$/", $middle)) {
        $error_msg = "Middle name should contain only letters and spaces.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $last)) {
        $error_msg = "Last name should contain only letters and spaces.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email address.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error_msg = "Phone number must be exactly 10 digits.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } elseif ($exp < 0 || $exp > 60) {
        $error_msg = "Experience should be between 0 and 60 years.";
    } elseif ($fee < 0) {
        $error_msg = "Consultation fee cannot be negative.";
    } elseif ($start >= $end) {
        $error_msg = "End time must be greater than start time.";
    } else {
        $stmt = mysqli_prepare($con, "SELECT user_id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $check_user = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check_user) > 0) {
            $error_msg = "Doctor email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($con, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'doctor')");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $user_id = mysqli_insert_id($con);

                $image_name = "";
                if (!empty($_FILES['image']['name'])) {
                    $image_name = basename($_FILES['image']['name']);
                    $tmp_name = $_FILES['image']['tmp_name'];
                    move_uploaded_file($tmp_name, "../assets/images/" . $image_name);
                }

                $stmt = mysqli_prepare($con, "INSERT INTO doctors (user_id, first_name, middle_name, last_name, department_id, phone, experience, available_days, start_time, end_time, consultation_fee, title, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "isssississdss", $user_id, $first, $middle, $last, $dept, $phone, $exp, $days, $start, $end, $fee, $title, $image_name);

                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Doctor added successfully.";
                } else {
                    $error_msg = "Doctor record could not be created.";
                }
            } else {
                $error_msg = "Login account could not be created.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Doctor</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  body {
    background: #f4f7fb;
  }

  .navbar {
    background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  .page-wrap {
    max-width: 1100px;
    margin: 32px auto;
  }

  .page-title {
    font-weight: 700;
    color: #16324f;
  }

  .panel-card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08);
    overflow: hidden;
  }

  .panel-head {
    background: linear-gradient(135deg, #e3f2fd 0%, #f8fbff 100%);
    padding: 22px 24px;
    border-bottom: 1px solid #e5edf6;
  }

  .panel-head h3 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 700;
    color: #16324f;
  }

  .panel-body {
    padding: 24px;
    background: #fff;
  }

  .section-box {
    border: 1px solid #e7eef7;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fcfdff;
  }

  .section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0d47a1;
    margin-bottom: 16px;
  }

  .form-label {
    font-weight: 600;
    color: #26415c;
  }

  .form-control,
  .form-select {
    border-radius: 12px;
    min-height: 46px;
  }

  .form-control:focus,
  .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.12);
    border-color: #1565c0;
  }

  .days-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 10px;
  }

  .day-item {
    border: 1px solid #dce7f3;
    border-radius: 12px;
    padding: 10px 12px;
    background: #fff;
  }

  .day-item:hover {
    background: #f5faff;
  }

  .day-item label {
    margin-left: 8px;
    font-weight: 500;
    color: #26415c;
  }

  .btn-save {
    background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
    border: none;
    border-radius: 14px;
    font-weight: 600;
    padding: 12px 18px;
  }

  .btn-save:hover {
    background: linear-gradient(135deg, #0b3c8c 0%, #1565c0 100%);
  }
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
      <a href="manage_appointments.php" class="btn btn-light btn-sm">Appointments</a>
      <a href="manage_doctors.php" class="btn btn-primary btn-sm">Doctors</a>
      <a href="add_doctor.php" class="btn btn-success btn-sm">Add Doctor</a>
      <a href="manage_patients.php" class="btn btn-info btn-sm">Patients</a>
      <a href="manage_bills.php" class="btn btn-warning btn-sm">Bills</a>
      <a href="reports.php" class="btn btn-secondary btn-sm">Reports</a>
      <a href="add_department.php" class="btn btn-outline-light btn-sm">Departments</a>
      <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="page-title mb-1">Add Doctor</h2>
      <p class="text-muted mb-0">Create a doctor login and professional profile from one form.</p>
    </div>
  </div>

  <?php if (!empty($success_msg)) { ?>
    <div class="alert alert-success"><?php echo $success_msg; ?></div>
  <?php } ?>

  <?php if (!empty($error_msg)) { ?>
    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
  <?php } ?>

  <div class="card panel-card">
    <div class="panel-head">
      <h3><i class="fa-solid fa-user-doctor me-2"></i>Doctor Registration Form</h3>
    </div>

    <div class="panel-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="section-box">
          <div class="section-title">Login Details</div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" placeholder="doctor@hospital.com" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" placeholder="Minimum 6 characters" class="form-control" minlength="6" required>
            </div>
          </div>
        </div>

        <div class="section-box">
          <div class="section-title">Personal Details</div>
          <div class="row">
            <div class="col-md-2 mb-3">
              <label class="form-label">Title</label>
              <select name="title" class="form-select">
                <option value="Dr">Dr</option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Miss">Miss</option>
                <option value="Ms">Ms</option>
              </select>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" placeholder="First Name" class="form-control" pattern="[A-Za-z ]+" required>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middle_name" placeholder="Middle Name" class="form-control" pattern="[A-Za-z ]*">
            </div>

            <div class="col-md-4 mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" placeholder="Last Name" class="form-control" pattern="[A-Za-z ]+" required>
            </div>
          </div>
        </div>

        <div class="section-box">
          <div class="section-title">Professional Details</div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Department</label>
              <select name="department_id" class="form-select" required>
                <option value="">Select Department</option>
                <?php while ($row = mysqli_fetch_assoc($dept_result)) { ?>
                  <option value="<?php echo $row['department_id']; ?>">
                    <?php echo $row['department_name']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">Experience (Years)</label>
              <input type="number" name="experience" placeholder="0" class="form-control" min="0" max="60" required>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">Consultation Fee</label>
              <input type="number" name="consultation_fee" placeholder="500" class="form-control" min="0" step="0.01" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" placeholder="10-digit Phone Number" class="form-control" pattern="[0-9]{10}" maxlength="10" required>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">Start Time</label>
              <input type="time" name="start_time" class="form-control" required>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">End Time</label>
              <input type="time" name="end_time" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="section-box">
          <div class="section-title">Availability</div>
          <label class="form-label d-block mb-2">Available Days</label>

          <div class="days-grid">
            <div class="day-item"><input class="form-check-input" type="checkbox" name="available_days[]" value="Monday" id="day_mon"><label for="day_mon">Monday</label></div>
            <div class="day-item"><input class="form-check-input" type="checkbox" name="available_days[]" value="Tuesday" id="day_tue"><label for="day_tue">Tuesday</label></div>
            <div class="day-item"><input class="form-check-input" type="checkbox" name="available_days[]" value="Wednesday" id="day_wed"><label for="day_wed">Wednesday</label></div>
            <div class="day-item"><input class="form-check-input" type="checkbox" name="available_days[]" value="Thursday" id="day_thu"><label for="day_thu">Thursday</label></div>
            <div class="day-item"><input class="form-check-input" type="checkbox" name="available_days[]" value="Friday" id="day_fri"><label for="day_fri">Friday</label></div>
            <div class="day-item"><input class="form-check-input" type="checkbox" name="available_days[]" value="Saturday" id="day_sat"><label for="day_sat">Saturday</label></div>
            <div class="day-item"><input class="form-check-input" type="checkbox" name="available_days[]" value="Sunday" id="day_sun"><label for="day_sun">Sunday</label></div>
          </div>

          <small class="text-muted d-block mt-2">Select all days when the doctor is available for appointments.</small>
        </div>

        <div class="section-box mb-0">
          <div class="section-title">Profile Image</div>
          <div class="mb-3">
            <label class="form-label">Upload Doctor Image</label>
            <input type="file" name="image" class="form-control">
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" name="add" class="btn btn-save w-100" style="color: white">
            <i class="fa-solid fa-plus me-2"></i>Add Doctor
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
