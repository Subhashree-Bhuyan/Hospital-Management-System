<?php
session_start();
include('../config/db.php');

$id = $_GET['id'];

$query = "SELECT doctors.*, departments.department_name 
          FROM doctors
          JOIN departments
          ON doctors.department_id = departments.department_id
          WHERE doctor_id='$id'";

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if(!$row){
    die("Doctor not found");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
  .navbar { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
</style>
</head>

<body>

<!-- STICKY NAVBAR -->
<nav class="navbar navbar-dark mb-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">🏥 Hospital Management</span>
    <div>
      <a href="../index.php" class="btn btn-light btn-sm me-2">🏠 Home</a>
      <a href="doctors.php" class="btn btn-light btn-sm me-2">👨‍⚕️ All Doctors</a>
      <?php if(isset($_SESSION['patient_id'])) { ?>
        <a href="../patients/dashboard.php" class="btn btn-light btn-sm me-2">📋 My Appointments</a>
        <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
      <?php } else { ?>
        <a href="../register.php" class="btn btn-light btn-sm me-2">📝 Register</a>
        <a href="../login.php" class="btn btn-light btn-sm">🔓 Login</a>
      <?php } ?>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-7">

      <div class="card shadow p-5 text-center">

        <!-- Doctor Image -->
        <?php
        $image = "../assets/images/default.png";
        if(!empty($row['image'])){
            $image = "../assets/images/" . $row['image'];
        }
        ?>
        <img src="<?php echo $image; ?>" 
        class="mx-auto mb-3"
        style="width:150px; height:150px; border-radius:50%; object-fit:cover;">

        <h2 class="mb-3">
        Dr. <?php echo $row['first_name'] . " " . $row['last_name']; ?>
        </h2>

        <div class="text-start mb-4 p-3 bg-light rounded">
          <p class="mb-2"><strong>🏥 Department:</strong> <?php echo $row['department_name']; ?></p>
          <p class="mb-2"><strong>⭐ Experience:</strong> <?php echo $row['experience']; ?> years</p>
          <p class="mb-2"><strong>📅 Available Days:</strong> <?php echo $row['available_days']; ?></p>
          <p class="mb-2"><strong>🕐 Timing:</strong> <?php echo $row['start_time'] . " - " . $row['end_time']; ?></p>
          <p class="mb-0"><strong>💰 Consultation Fee:</strong> ₹<?php echo $row['consultation_fee']; ?></p>
        </div>

        <?php if(isset($_SESSION['patient_id'])) { ?>
          <!-- Logged in patient can book -->
          <a href="../patients/book_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" 
          class="btn btn-success btn-lg mt-3">
          📅 Book Appointment
          </a>
        <?php } else { ?>
          <!-- Not logged in, show login prompt -->
          <div class="alert alert-info mt-3">
            <p class="mb-2">🔒 Please login to book an appointment</p>
            <a href="../login.php" class="btn btn-primary me-2">🔓 Login</a>
            <a href="../register.php" class="btn btn-success">📝 Register</a>
          </div>
        <?php } ?>

      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>