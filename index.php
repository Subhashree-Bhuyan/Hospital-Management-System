<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow">
  <div class="container">

    <!-- LOGO + NAME -->
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="assets/images/logo.png" 
        style="width:50px;height:50px;border-radius:50%;margin-right:10px;">

        <div>
            <h5 class="mb-0">City Care Hospital</h5>
            <small>Bhubaneswar, Odisha</small>
        </div>
    </a>

    <!-- MENU -->
    <div>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="public/doctors.php">Doctors</a></li>
        <li class="nav-item"><a class="nav-link" href="public/book_appointment.php">Appointment</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="doctor/login.php">Doctor Login</a></li>
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
      </ul>
    </div>

  </div>
</nav>


<!-- HERO SECTION -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">

  <div class="carousel-inner">

    <!-- ----------- IMAGE 1 ------------ -->
    <div class="carousel-item active">
      <img src="assets/images/hospital1.png" class="d-block w-100 hero-img">

      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
        <img src="assets/images/logo.png" class="hero-logo mb-3">
        <h1 class="hero-title">City Care Hospital</h1>
        <p class="hero-location">Bhubaneswar, Odisha</p>
      </div>
    </div>

    <!-- IMAGE 2 -->
    <div class="carousel-item">
      <img src="assets/images/hospital2.png" class="d-block w-100 hero-img">

      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
        <img src="assets/images/logo.png" class="hero-logo mb-3">
        <h1 class="hero-title">City Care Hospital</h1>
        <p class="hero-location">Bhubaneswar, Odisha</p>
      </div>
    </div>

    <!-- IMAGE 3 -->
    <div class="carousel-item">
      <img src="assets/images/hospital3.png" class="d-block w-100 hero-img">

      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
        <img src="assets/images/logo.png" class="hero-logo mb-3">
        <h1 class="hero-title">City Care Hospital</h1>
        <p class="hero-location">Bhubaneswar, Odisha</p>
      </div>
    </div>

  </div>

</div>

<div class="container-fluid bg-success text-white text-center p-5">
    ...
</div>

<div class="container mt-5 fade-in">

<div class="row text-center">

<div class="col-md-4">
    <div class="card p-4 shadow">
        <i class="fa-solid fa-calendar-check fa-3x text-success mb-3"></i>
        <h4>Easy Booking</h4>
        <p>Book doctor appointments easily.</p>
    </div>
</div>

<div class="col-md-4">
    <div class="card p-4 shadow">
        <i class="fa-solid fa-user-doctor fa-3x text-success mb-3"></i>
        <h4>Expert Doctors</h4>
        <p>Highly experienced specialists.</p>
    </div>
</div>

<div class="col-md-4">
    <div class="card p-4 shadow">
        <i class="fa-solid fa-phone fa-3x text-success mb-3"></i>
        <h4>24/7 Helpline</h4>
        <p>Call us anytime for support.</p>
    </div>
</div>

</div>

</div>

<div class="container mt-5 fade-in">

<h2 class="text-center mb-4">Our Departments</h2>

<div class="row">

<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Cardiology</h5>
    </div>
</div>

<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Neurology</h5>
    </div>
</div>

<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Orthopedics</h5>
    </div>
</div>

<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Pediatrics</h5>
    </div>
</div>



</div>

</div>

<div class="container mt-5 mb-5 text-center">

<h3>Need Medical Help?</h3>
<p>Book your appointment now and consult with our expert doctors.</p>

<a href="public/doctors.php" class="btn btn-success btn-lg">
    Get Started
</a>

</div>
<div class="bottom-marquee">
    <marquee>
        📞 Helpline Number: 9559983850 | 9450123465 | Available 24/7
    </marquee>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>