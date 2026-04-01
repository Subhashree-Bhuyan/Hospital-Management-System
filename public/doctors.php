<?php
session_start();
include('../config/db.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$department_id = isset($_GET['department_id']) ? (int) $_GET['department_id'] : 0;

/* fetch departments for filter */
$dept_result = mysqli_query($con, "SELECT department_id, department_name FROM departments ORDER BY department_name ASC");

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
                OR departments.department_name LIKE '%$search_term%'
              )";
}

if ($department_id > 0) {
    $query .= " AND doctors.department_id = '$department_id'";
}

$query .= " ORDER BY doctors.first_name ASC";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Our Doctors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
      .navbar {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      body { padding-top: 0; }
      .search-section {
        position: sticky;
        top: 60px;
        background: white;
        z-index: 999;
        padding: 15px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
    </style>
</head>

<body>

<nav class="navbar navbar-dark mb-0">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Hospital Management</span>
    <div>
      <a href="../index.php" class="btn btn-light btn-sm me-2">Home</a>
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

<div class="search-section">
  <div class="container">
    <div class="row align-items-end">
      <div class="col-md-4">
        <h2 class="mb-0">Our Doctors</h2>
      </div>
      <div class="col-md-8">
        <form method="GET" class="row g-2">
          <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search doctor or department..."
                   value="<?php echo htmlspecialchars($search); ?>">
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
            <button class="btn btn-success w-100" type="submit">Search</button>
            <a href="doctors.php" class="btn btn-secondary w-100">Reset</a>
          </div>
        </form>
      </div>
    </div>

    <?php if (!empty($search) || $department_id > 0) { ?>
      <div class="alert alert-info mt-3 mb-0">
        Filters applied. Matching doctors are shown below.
      </div>
    <?php } ?>
  </div>
</div>

<div class="container mt-4 mb-5">
<div class="row">

<?php
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
?>

<div class="col-md-4 mb-4">
  <div class="card shadow h-100 text-center p-3">
    <?php
    $image = "../assets/images/default.png";
    if (!empty($row['image'])) {
        $image = "../assets/images/" . $row['image'];
    }
    ?>

    <img src="<?php echo $image; ?>"
         class="card-img-top mx-auto mb-3"
         style="width:120px; height:120px; border-radius:50%; object-fit:cover;">

    <div class="card-body">
      <h5 class="card-title">Dr. <?php echo $row['first_name'] . " " . $row['last_name']; ?></h5>
      <span class="badge bg-success mb-2">Available</span>
      <p class="text-muted"><?php echo $row['department_name']; ?></p>
      <p class="mb-2"><strong>Experience:</strong> <?php echo $row['experience']; ?> years</p>
      <p class="mb-2"><strong>Timing:</strong><br><?php echo date('h:i A', strtotime($row['start_time'])) . " - " . date('h:i A', strtotime($row['end_time'])); ?></p>
      <p class="mb-3"><strong>Fee:</strong> Rs. <?php echo number_format($row['consultation_fee'], 2); ?></p>

      <div class="d-grid gap-2">
        <a href="book_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-success btn-sm">
          View Profile / Book
        </a>
      </div>
    </div>
  </div>
</div>

<?php
  }
} else {
  echo '<div class="col-12"><div class="alert alert-warning text-center">No doctors found for the selected search/filter.</div></div>';
}
?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
