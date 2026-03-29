<?php
include('../config/db.php');

$id = $_GET['id'];

$query = "SELECT doctors.*, departments.department_name 
          FROM doctors
          JOIN departments
          ON doctors.department_id = departments.department_id
          WHERE doctor_id='$id'";

$result = mysqli_query($con,$query);

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Profile</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CSS -->
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
      <a class="navbar-brand" href="#">Hospital</a>

      <div>
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="doctors.php">Doctors</a></li>
          <li class="nav-item"><a class="nav-link" href="book_appointment.php">Appointment</a></li>
          <li class="nav-item"><a class="nav-link" href="../register.php">Register</a></li>
          <li class="nav-item"><a class="nav-link" href="../login.php">Login</a></li>
        </ul>
      </div>
  </div>
</nav>


<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <div class="card shadow p-4 text-center">

        <h2 class="mb-3">
        Dr. <?php echo $row['first_name']." ".$row['last_name']?>
        </h2>

        <p><strong>Department:</strong> <?php echo $row['department_name']; ?></p>

        <p><strong>Experience:</strong> <?php echo $row['experience']; ?> years</p>

        <p><strong>Available Days:</strong> <?php echo $row['available_days']; ?></p>

        <p><strong>Timing:</strong> <?php echo $row['start_time']." - ".$row['end_time']; ?></p>

        <p><strong>Consultation Fee:</strong> ₹<?php echo $row['consultation_fee']; ?></p>

        <a href="book_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" 
        class="btn btn-success mt-3">
        Book Appointment
        </a>

      </div>

    </div>
  </div>
</div>
</body>
</html>