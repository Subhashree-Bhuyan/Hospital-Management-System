<?php
session_start();
include("../config/db.php");

/* check admin login */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_bill'])) {
    $bill_id = (int) $_POST['bill_id'];
    $consultation_fee = floatval($_POST['consultation_fee']);
    $test_fee = floatval($_POST['test_fee']);
    $medicine_fee = floatval($_POST['medicine_fee']);
    $paid_amount = floatval($_POST['paid_amount']);

    if ($consultation_fee < 0) $consultation_fee = 0;
    if ($test_fee < 0) $test_fee = 0;
    if ($medicine_fee < 0) $medicine_fee = 0;

    $total_amount = $consultation_fee + $test_fee + $medicine_fee;

    if ($paid_amount < 0) {
        $paid_amount = 0;
    }

    if ($paid_amount > $total_amount) {
        $paid_amount = $total_amount;
    }

    $pending_amount = $total_amount - $paid_amount;

    if ($paid_amount <= 0) {
        $status = 'Pending';
    } elseif ($paid_amount < $total_amount) {
        $status = 'Partial';
    } else {
        $status = 'Paid';
        $pending_amount = 0;
    }

    $update_query = "UPDATE bills 
                     SET consultation_fee='$consultation_fee',
                         test_fee='$test_fee',
                         medicine_fee='$medicine_fee',
                         total_amount='$total_amount',
                         paid_amount='$paid_amount',
                         pending_amount='$pending_amount',
                         status='$status'
                     WHERE bill_id='$bill_id'";

    $update_result = mysqli_query($con, $update_query);

    if ($update_result) {
        $success_msg = "Bill updated successfully.";
    } else {
        $error_msg = "Error updating bill.";
    }
}

/* fetch all bills */
$query = "SELECT 
            b.*,
            d.first_name AS doc_first,
            d.last_name AS doc_last,
            p.first_name AS pat_first,
            p.last_name AS pat_last,
            a.appointment_date,
            a.appointment_time
          FROM bills b
          JOIN doctors d ON b.doctor_id = d.doctor_id
          JOIN patients p ON b.patient_id = p.patient_id
          JOIN appointments a ON b.appointment_id = a.appointment_id
          ORDER BY b.created_at DESC";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin - Manage Bills</title>
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

<div class="container mt-5 mb-5">

<h2 class="mb-4">Manage All Bills</h2>

<?php if (isset($success_msg)) { ?>
  <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php } ?>

<?php if (isset($error_msg)) { ?>
  <div class="alert alert-danger"><?php echo $error_msg; ?></div>
<?php } ?>

<?php if (mysqli_num_rows($result) > 0) { ?>
<table class="table table-bordered table-hover align-middle">
<thead class="table-info">
<tr>
<th>Bill ID</th>
<th>Doctor</th>
<th>Patient</th>
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
<td>Dr. <?php echo $row['doc_first'] . ' ' . $row['doc_last']; ?></td>
<td><?php echo $row['pat_first'] . ' ' . $row['pat_last']; ?></td>
<td><?php echo $row['appointment_date']; ?></td>
<td>Rs. <?php echo number_format($row['consultation_fee'], 2); ?></td>
<td>Rs. <?php echo number_format((float) $row['test_fee'], 2); ?></td>
<td>Rs. <?php echo number_format((float) $row['medicine_fee'], 2); ?></td>
<td><strong>Rs. <?php echo number_format($row['total_amount'], 2); ?></strong></td>
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
<button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['bill_id']; ?>">Edit</button>
</td>
</tr>

<div class="modal fade" id="editModal<?php echo $row['bill_id']; ?>" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header bg-info text-white">
<h5 class="modal-title">Edit Bill</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form method="POST" class="bill-form">
<input type="hidden" name="bill_id" value="<?php echo $row['bill_id']; ?>">

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Doctor</label>
    <input type="text" class="form-control" value="Dr. <?php echo $row['doc_first'] . ' ' . $row['doc_last']; ?>" readonly>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Patient</label>
    <input type="text" class="form-control" value="<?php echo $row['pat_first'] . ' ' . $row['pat_last']; ?>" readonly>
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Consultation Fee</label>
    <input type="number" name="consultation_fee" class="form-control fee-input" value="<?php echo number_format($row['consultation_fee'], 2, '.', ''); ?>" min="0" step="0.01" required>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Test Fee</label>
    <input type="number" name="test_fee" class="form-control fee-input" value="<?php echo number_format((float) $row['test_fee'], 2, '.', ''); ?>" min="0" step="0.01" required>
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Medicine Fee</label>
    <input type="number" name="medicine_fee" class="form-control fee-input" value="<?php echo number_format((float) $row['medicine_fee'], 2, '.', ''); ?>" min="0" step="0.01" required>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Paid Amount</label>
    <input type="number" name="paid_amount" class="form-control fee-input" value="<?php echo number_format($row['paid_amount'], 2, '.', ''); ?>" min="0" step="0.01" required>
  </div>
</div>

<div class="alert alert-info mt-3">
  <div class="row">
    <div class="col-md-6">
      <strong>Total Amount:</strong>
      <div>Rs. <span class="total-display">0.00</span></div>
    </div>
    <div class="col-md-6">
      <strong>Pending Amount:</strong>
      <div>Rs. <span class="pending-display">0.00</span></div>
    </div>
  </div>
</div>

<button type="submit" name="update_bill" class="btn btn-success w-100">Update Bill</button>
</form>
</div>
</div>
</div>
</div>
<?php } ?>
</tbody>
</table>
<?php } else { ?>
<div class="alert alert-info">No bills found.</div>
<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.bill-form').forEach(function(form) {
  const inputs = form.querySelectorAll('.fee-input');
  const totalDisplay = form.querySelector('.total-display');
  const pendingDisplay = form.querySelector('.pending-display');

  function getVal(name) {
    const el = form.querySelector('[name="' + name + '"]');
    return parseFloat(el.value) || 0;
  }

  function updateTotals() {
    const total = getVal('consultation_fee') + getVal('test_fee') + getVal('medicine_fee');
    let paid = getVal('paid_amount');

    if (paid > total) {
      paid = total;
    }

    const pending = Math.max(0, total - paid);
    totalDisplay.textContent = total.toFixed(2);
    pendingDisplay.textContent = pending.toFixed(2);
  }

  inputs.forEach(function(input) {
    input.addEventListener('input', updateTotals);
  });

  updateTotals();
});
</script>
</body>
</html>
