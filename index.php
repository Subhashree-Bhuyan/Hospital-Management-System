<!DOCTYPE html>
<html>
<head>
    <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Hospital Management System</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
<!-- NAVBAR -->
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
        <li class="nav-item"><a class="nav-link" href="admin/login.php">Admin Login</a></li>
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

<!-- DEPARTMENTS -->
<div class="container mt-5 fade-in">


<h2 class="text-center mb-5 fw-bold">Our Departments</h2>

<div class="row">

<!-- CARDIOLOGY -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-heart-pulse"></i>
        </div>
        <h5 class="fw-bold">Cardiology</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Advanced heart care with modern diagnostics and expert cardiologists.
        </p>
    </div>
</div>

<!-- NEUROLOGY -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-brain"></i>
        </div>
        <h5 class="fw-bold">Neurology</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Specialized treatment for brain and nervous system disorders.
        </p>
    </div>
</div>

<!-- ORTHOPEDICS -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-bone"></i>
        </div>
        <h5 class="fw-bold">Orthopedics</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Expert care for bones, joints, and mobility restoration.
        </p>
    </div>
</div>

<!-- PEDIATRICS -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-baby"></i>
        </div>
        <h5 class="fw-bold">Pediatrics</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Compassionate care for infants, children, and adolescents.
        </p>
    </div>
</div>

<!-- GYNAECOLOGY -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-user-nurse"></i>
        </div>
        <h5 class="fw-bold">Gynaecology</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Complete women's healthcare and maternity services.
        </p>
    </div>
</div>

<!-- PSYCHIATRY -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-head-side-virus"></i>
        </div>
        <h5 class="fw-bold">Psychiatry</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Mental health support with expert counseling and treatment.
        </p>
    </div>
</div>

<!-- OPHTHALMOLOGY -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-eye"></i>
        </div>
        <h5 class="fw-bold">Ophthalmology</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Advanced eye care and vision treatment solutions.
        </p>
    </div>
</div>

<!-- GASTROENTEROLOGY -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
          <i class="fa-solid fa-prescription-bottle-medical"></i>
        </div>
        <h5 class="fw-bold">Gastroenterology</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Expert care for digestive system and liver disorders.
        </p>
    </div>
</div>

<!-- ENT -->
<div class="col-md-3 mb-4">
    <div class="card dept-card text-center p-4 h-100">
        <div class="dept-icon mb-3">
            <i class="fa-solid fa-ear-listen"></i>
        </div>
        <h5 class="fw-bold">ENT</h5>
        <div class="divider mx-auto my-2"></div>
        <p class="text-muted small">
            Treatment for ear, nose, and throat conditions.
        </p>
    </div>
</div>

</div>

</div>

<!-- CTA -->
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

<!-- ================= ABOUT US SECTION ================= -->
<div class="container mt-5 mb-5 animate">

  <div class="row align-items-center">

    <!-- LEFT IMAGE -->
    <div class="col-md-6 mb-4">
      <img src="assets/images/hospital2.png" 
           class="img-fluid rounded shadow-lg"
           style="border-radius:20px;">
    </div>

    <!-- RIGHT CONTENT -->
    <div class="col-md-6">

      <h2 class="fw-bold text-success mb-3">About City Care Hospital</h2>

      <p class="text-muted">
        City Care Hospital is a trusted healthcare provider dedicated to delivering 
        world-class medical services with compassion, innovation, and excellence.
      </p>

      <p>
        Our hospital is equipped with modern medical technology and a team of 
        highly qualified doctors committed to providing the best treatment 
        for every patient.
      </p>

      <!-- FEATURES -->
      <div class="row mt-4">

        <div class="col-6 mb-3">
          <div class="d-flex align-items-center">
            <i class="fa fa-check-circle text-success me-2"></i>
            <span>24/7 Emergency Care</span>
          </div>
        </div>

        <div class="col-6 mb-3">
          <div class="d-flex align-items-center">
            <i class="fa fa-check-circle text-success me-2"></i>
            <span>Expert Doctors</span>
          </div>
        </div>

        <div class="col-6 mb-3">
          <div class="d-flex align-items-center">
            <i class="fa fa-check-circle text-success me-2"></i>
            <span>Advanced Equipment</span>
          </div>
        </div>

        <div class="col-6 mb-3">
          <div class="d-flex align-items-center">
            <i class="fa fa-check-circle text-success me-2"></i>
            <span>Patient-Centered Care</span>
          </div>
        </div>

      </div>

      <!-- BUTTON -->
      <a href="public/doctors.php" class="btn btn-success mt-3 px-4">
        Explore Our Doctors
      </a>

    </div>

  </div>

</div>

<!-- ================= STATS SECTION ================= -->
<div class="container-fluid bg-success text-white text-center py-5">

  <div class="row">

    <div class="col-md-3">
     <h2 class="fw-bold counter" data-target="50">0</h2>
      <p>Doctors</p>
    </div>

    <div class="col-md-3">
      <h2 class="fw-bold counter" data-target="10000">0</h2>
      <p>Patients Treated</p>
    </div>

    <div class="col-md-3">
     <h2 class="fw-bold counter" data-target="15">0</h2>
      <p>Departments</p>
    </div>

    <div class="col-md-3">
      <h2 class="fw-bold counter" data-target="24">0</h2>
      <p>Emergency Service</p>
    </div>

  </div>

</div>

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

<script>
const elements = document.querySelectorAll('.animate');

function showOnScroll() {
    const triggerBottom = window.innerHeight * 0.85;

    elements.forEach(el => {
        const boxTop = el.getBoundingClientRect().top;

        if (boxTop < triggerBottom) {
            el.classList.add('show');
        }
    });
}

window.addEventListener('scroll', showOnScroll);
window.addEventListener('load', showOnScroll);
</script>
</body>

</html>