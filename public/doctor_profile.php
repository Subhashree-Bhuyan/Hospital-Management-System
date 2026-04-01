<?php
session_start();
include('../config/db.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query = "SELECT doctors.*, departments.department_name 
          FROM doctors
          JOIN departments ON doctors.department_id = departments.department_id
          WHERE doctors.doctor_id='$id'
          LIMIT 1";

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Doctor not found");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .navbar { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
</style>
</head>

<body>

<nav class="navbar navbar-dark mb-4" style="position: sticky; top: 0; z-index: 1000;">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Hospital Management</span>
    <div>
      <a href="../index.php" class="btn btn-light btn-sm me-2">Home</a>
      <a href="doctors.php" class="btn btn-light btn-sm me-2">All Doctors</a>
      <?php if (isset($_SESSION['patient_id'])) { ?>
        <a href="../patients/dashboard.php" class="btn btn-light btn-sm me-2">My Appointments</a>
        <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
      <?php } else { ?>
        <a href="../register.php" class="btn btn-light btn-sm me-2">Register</a>
        <a href="../login.php" class="btn btn-light btn-sm">Login</a>
      <?php } ?>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow p-4">

        <?php
        $image = "../assets/images/default.png";
        if (!empty($row['image'])) {
            $image = "../assets/images/" . $row['image'];
        }
        ?>

        <div class="text-center">
          <img src="<?php echo $image; ?>"
               class="mx-auto mb-3"
               style="width:150px; height:150px; border-radius:50%; object-fit:cover;">
          <h2 class="mb-1"><?php echo $row['title'] . " " . $row['first_name'] . " " . $row['last_name']; ?></h2>
          <p class="text-muted mb-4"><?php echo $row['department_name']; ?></p>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="p-3 bg-light rounded mb-3">
              <p class="mb-2"><strong>Department:</strong> <?php echo $row['department_name']; ?></p>
              <p class="mb-2"><strong>Experience:</strong> <?php echo $row['experience']; ?> years</p>
              <p class="mb-0"><strong>Phone:</strong> <?php echo $row['phone']; ?></p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="p-3 bg-light rounded mb-3">
              <p class="mb-2"><strong>Available Days:</strong> <?php echo $row['available_days']; ?></p>
              <p class="mb-2"><strong>Timing:</strong> <?php echo date('h:i A', strtotime($row['start_time'])) . " - " . date('h:i A', strtotime($row['end_time'])); ?></p>
              <p class="mb-0"><strong>Consultation Fee:</strong> Rs. <?php echo number_format($row['consultation_fee'], 2); ?></p>
            </div>
          </div>
        </div>

        <div class="text-center mt-3">
          <?php if (isset($_SESSION['patient_id'])) { ?>
            <a href="../patients/book_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-success btn-lg">
              Book Appointment
            </a>
          <?php } else { ?>
            <div class="alert alert-info">
              Please login to book an appointment.<br><br>
              <a href="../login.php" class="btn btn-primary me-2">Login</a>
              <a href="../register.php" class="btn btn-success">Register</a>
            </div>
          <?php } ?>
        </div>

      </div>
    </div>
  </div>
</div>

</body>
</html>
