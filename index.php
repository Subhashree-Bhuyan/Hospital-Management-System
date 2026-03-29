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
        <p>Our Heart Center offers comprehensive cardiovascular care, from non-invasive stress testing and advanced echocardiography to personalized treatment plans for heart health and rehabilitation.</p>
    </div>
</div>

<!-- NEUROLOGY -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Neurology</h5>
        <p>Our Neurology department specializes in diagnosing and treating complex brain and spine disorders with cutting-edge diagnostic technology.</p>
    </div>
</div>

<!-- GENERAL MEDICINE -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>General Medicine</h5>
        <p>We provide comprehensive primary care, focusing on the prevention, diagnosis, and non-surgical treatment of adult internal diseases.</p>

    </div>
</div>

<!-- PAEDIATRICS -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Paediatrics</h5>
        <p>Our compassionate Paediatric team ensures the highest standard of medical care for infants, children, and adolescents in a child-friendly environment.</p>

    </div>
</div>

<!-- GYNAECOLOGY -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Gynaecology & Obstetrics</h5>
        <p>Dedicated to women's health, we offer expert care ranging from routine wellness exams to advanced maternity and prenatal services.</p>


    </div>
</div>

<!-- PSYCHIATRY -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Psychiatry</h5>
        <p>We offer supportive mental health services, providing professional counseling and clinical treatment for emotional and behavioral well-being.</p>


    </div>
</div>

<!-- OPHTHALMOLOGY -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Ophthalmology</h5>
        <p>Our eye care specialists provide precision diagnostics and advanced treatments to protect your vision and treat ocular conditions.</p>


    </div>
</div>

<!-- GASTROENTEROLOGY -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Gastroenterology</h5>
        <p>We focus on digestive health, offering expert management of liver, stomach, and intestinal disorders for improved quality of life.</p>
    </div>
</div>
<!-- ORTHOPEDICS -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>Orthopedics</h5>
        <p>Our Orthopaedic Center offers world-class expertise in joint replacements and sports medicine, helping you regain mobility and strength.</p>
    </div>
</div>
<!-- ENT -->
<div class="col-md-3">
    <div class="card text-center p-3 shadow">
        <h5>ENT</h5>
        <p>Specializing in disorders of the ear, nose, and throat, we provide expert medical and surgical care for all age groups.</p>
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


<!-- ---------------------FOOTER----------------------- -->
<footer class="bg-dark text-light py-5 mt-5 border-top border-primary border-4">
  <div class="container">
    <div class="row g-4">
      
      <!-- 1. Hospital Branding & Mission -->
      <div class="col-lg-4 col-md-6">
        <h5 class="text-primary mb-3 fw-bold">CITY GENERAL HOSPITAL</h5>
        <p class="small text-secondary">Providing world-class healthcare with precision and compassion. Our specialized clinics are equipped with the latest diagnostic technology.</p>
        <!-- Update your social icons section like this -->
<div class="mt-4">
    <a href="#" class="text-light me-3"><i class="fa-brands fa-facebook"></i></a>
    <a href="#" class="text-light me-3"><i class="fa-brands fa-twitter"></i></a>
    <a href="#" class="text-light me-3"><i class="fa-brands fa-linkedin"></i></a>
</div>

      </div>

      <!-- 2. Quick Navigation -->
      <div class="col-lg-2 col-md-6">
        <h6 class="text-uppercase fw-bold mb-3">Quick Links</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="#" class="footer-link">Find a Doctor</a></li>
          <li class="mb-2"><a href="#" class="footer-link">Our Services</a></li>
          <li class="mb-2"><a href="#" class="footer-link">Patient Portal</a></li>
          <li class="mb-2"><a href="#" class="footer-link">Contact Us</a></li>
        </ul>
      </div>

      <!-- 3. Contact & Location -->
      <div class="col-lg-3 col-md-6">
        <h6 class="text-uppercase fw-bold mb-3">Contact Details</h6>
        <p class="small mb-2"><i class="fa fa-map-marker-alt text-primary me-2"></i> 123 Health Ave, Medical District</p>
        <p class="small mb-2"><i class="fa fa-phone-alt text-primary me-2"></i> +1 (555) 000-1234</p>
        <p class="small mb-2"><i class="fa fa-envelope text-primary me-2"></i> info@hospital.com</p>
      </div>

      <!-- 4. Emergency Call-to-Action -->
      <div class="col-lg-3 col-md-6">
        <div class="emergency-box p-3 border border-danger rounded text-center">
          <h6 class="text-danger fw-bold mb-2"><i class="fa fa- ambulance"></i> EMERGENCY 24/7</h6>
          <p class="h5 mb-0 fw-bold">Call 9559-983-850 | 9450-123-465</p>
          <p class="small text-muted mt-2">Ambulance service available at all times.</p>
        </div>
      </div>

    </div>

    <!-- Bottom Bar -->
    <hr class="my-4 border-secondary">
    <div class="row align-items-center small text-secondary">
      <div class="col-md-6 text-center text-md-start">
        &copy; 2026 City General Hospital. All rights reserved.
      </div>
      <div class="col-md-6 text-center text-md-end">
        <a href="#" class="footer-link me-3">Privacy Policy</a>
        <a href="#" class="footer-link">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>
</html>