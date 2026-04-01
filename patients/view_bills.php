<?php
session_start();
include("../config/db.php");

/* check patient login */
if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];

/* fetch patient data */
$patient_query = "SELECT * FROM patients WHERE patient_id='$patient_id' LIMIT 1";
$patient_result = mysqli_query($con, $patient_query);
$patient_row = mysqli_fetch_assoc($patient_result);
$patient_name = $patient_row['first_name'] . " " . $patient_row['last_name'];

/* fetch bills */
$query = "SELECT 
            b.*,
            d.first_name,
            d.last_name,
            a.appointment_date,
            a.appointment_time
          FROM bills b
          JOIN doctors d ON b.doctor_id = d.doctor_id
          JOIN appointments a ON b.appointment_id = a.appointment_id
          WHERE b.patient_id='$patient_id'
          ORDER BY b.created_at DESC";

$result = mysqli_query($con, $query);

/* check appointment status when no bills exist */
$pending_bill_query = "SELECT COUNT(*) AS total_pending_or_incomplete
                       FROM appointments
                       WHERE patient_id='$patient_id'
                       AND status != 'Completed'";
$pending_bill_result = mysqli_query($con, $pending_bill_query);
$pending_bill_row = mysqli_fetch_assoc($pending_bill_result);

$completed_no_bill_query = "SELECT COUNT(*) AS total_completed
                            FROM appointments
                            WHERE patient_id='$patient_id'
                            AND status='Completed'";
$completed_no_bill_result = mysqli_query($con, $completed_no_bill_query);
$completed_no_bill_row = mysqli_fetch_assoc($completed_no_bill_result);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Bills</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .navbar {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
  }
</style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand mb-0 h1" href="#">
      <img src="../assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-middle me-2 rounded-circle border border-white" style="object-fit: cover;">
      Patient Panel
    </a>
    <div>
      <span class="text-white me-3"><?php echo $patient_name; ?></span>
      <a href="../index.php" class="btn btn-light btn-sm me-2">Home</a>
      <a href="dashboard.php" class="btn btn-light btn-sm me-2">My Appointments</a>
      <a href="view_bills.php" class="btn btn-light btn-sm me-2">My Bills</a>
      <a href="../public/doctors.php" class="btn btn-light btn-sm me-2">View Doctors</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">

<h2 class="mb-4">My Bills</h2>

<?php if (mysqli_num_rows($result) > 0) { ?>
<table class="table table-bordered table-hover align-middle">
<thead class="table-success">
<tr>
<th>Bill ID</th>
<th>Doctor</th>
<th>Date</th>
<th>Consultation</th>
<th>Test</th>
<th>Medicine</th>
<th>Total</th>
<th>Paid</th>
<th>Pending</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<td><?php echo $row['bill_id']; ?></td>
<td>Dr. <?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
<td><?php echo $row['appointment_date']; ?></td>
<td>Rs. <?php echo number_format($row['consultation_fee'], 2); ?></td>
<td>Rs. <?php echo number_format((float) $row['test_fee'], 2); ?></td>
<td>Rs. <?php echo number_format((float) $row['medicine_fee'], 2); ?></td>
<td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
<td>Rs. <?php echo number_format($row['paid_amount'], 2); ?></td>
<td>Rs. <?php echo number_format($row['pending_amount'], 2); ?></td>
<td>
<?php
if ($row['status'] == 'Paid') {
    echo '<span class="badge bg-success">Paid</span>';
} elseif ($row['status'] == 'Partial') {
    echo '<span class="badge bg-warning text-dark">Partial</span>';
} else {
    echo '<span class="badge bg-danger">Pending</span>';
}
?>
</td>
<td>
  <a href="download_bill.php?id=<?php echo $row['bill_id']; ?>" class="btn btn-sm btn-primary">Download PDF</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
<?php } else { ?>

  <?php if (($pending_bill_row['total_pending_or_incomplete'] ?? 0) > 0) { ?>
    <div class="alert alert-warning">
      Bill has not been created yet. Bills are generated only after the appointment is completed by the doctor.
    </div>
  <?php } elseif (($completed_no_bill_row['total_completed'] ?? 0) > 0) { ?>
    <div class="alert alert-info">
      Your appointment has been completed, but bill details are not available yet. Please contact the hospital/admin.
    </div>
  <?php } else { ?>
    <div class="alert alert-info">
      No bills found because you do not have any completed appointments yet.
    </div>
  <?php } ?>

<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
