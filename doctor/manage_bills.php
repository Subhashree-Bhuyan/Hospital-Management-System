<?php
session_start();
include("../config/db.php");

/* check doctor login */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$doc_query = "SELECT doctor_id, first_name, last_name FROM doctors WHERE user_id='$user_id' LIMIT 1";
$doc_result = mysqli_query($con, $doc_query);
$doc_row = mysqli_fetch_assoc($doc_result);

if (!$doc_row) {
    header("Location: ../login.php");
    exit();
}

$doctor_id = (int) $doc_row['doctor_id'];
$doctor_name = $doc_row['first_name'] . " " . $doc_row['last_name'];

if (isset($_POST['update_bill'])) {
    $bill_id = (int) $_POST['bill_id'];
    $paid_amount = floatval($_POST['paid_amount']);

    $bill_query = "SELECT consultation_fee, doctor_id FROM bills WHERE bill_id='$bill_id' AND doctor_id='$doctor_id' LIMIT 1";
    $bill_result = mysqli_query($con, $bill_query);

    if ($bill_result && mysqli_num_rows($bill_result) == 1) {
        $bill_data = mysqli_fetch_assoc($bill_result);

        $consultation_fee = floatval($bill_data['consultation_fee']);

        if ($paid_amount < 0) {
            $paid_amount = 0;
        }

        if ($paid_amount > $consultation_fee) {
            $paid_amount = $consultation_fee;
        }

        $pending_amount = $consultation_fee - $paid_amount;

        if ($paid_amount <= 0) {
            $status = 'Pending';
        } elseif ($paid_amount < $consultation_fee) {
            $status = 'Partial';
        } else {
            $status = 'Paid';
            $pending_amount = 0;
        }

        $update_query = "UPDATE bills 
                         SET paid_amount='$paid_amount',
                             pending_amount='$pending_amount',
                             status='$status'
                         WHERE bill_id='$bill_id' AND doctor_id='$doctor_id'";
        mysqli_query($con, $update_query);
    }

    header("Location: manage_bills.php");
    exit();
}

/* fetch bills for this doctor */
$query = "SELECT 
            b.bill_id,
            b.appointment_id,
            b.consultation_fee,
            b.paid_amount,
            b.pending_amount,
            b.status,
            b.created_at,
            p.first_name,
            p.last_name,
            a.appointment_date,
            a.appointment_time
          FROM bills b
          JOIN patients p ON b.patient_id = p.patient_id
          JOIN appointments a ON b.appointment_id = a.appointment_id
          WHERE b.doctor_id='$doctor_id'
          ORDER BY b.created_at DESC";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Bills</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .navbar { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); position: sticky; top: 0; z-index: 1000; }
</style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand mb-0 h1" href="#">
      <img src="../assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-middle me-2 rounded-circle border border-white" style="object-fit: cover;">
      Doctor Panel
    </a>
    <div>
      <span class="text-white me-3">Dr. <?php echo $doctor_name; ?></span>
      <a href="dashboard.php" class="btn btn-light btn-sm me-2">Dashboard</a>
      <a href="manage_bills.php" class="btn btn-warning btn-sm me-2">Manage Bills</a>
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
<h2 class="mb-4">Consultation Bills</h2>

<?php if (mysqli_num_rows($result) > 0) { ?>
<table class="table table-bordered table-hover align-middle">
<thead class="table-success">
<tr>
<th>Bill ID</th>
<th>Patient</th>
<th>Date</th>
<th>Time</th>
<th>Consultation Fee</th>
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
<td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
<td><?php echo $row['appointment_date']; ?></td>
<td><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></td>
<td>Rs. <?php echo number_format($row['consultation_fee'], 2); ?></td>
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
<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['bill_id']; ?>">Update</button>
</td>
</tr>

<div class="modal fade" id="editModal<?php echo $row['bill_id']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Update Consultation Payment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
          <input type="hidden" name="bill_id" value="<?php echo $row['bill_id']; ?>">

          <div class="mb-3">
            <label class="form-label">Consultation Fee</label>
            <input class="form-control" type="text" value="Rs. <?php echo number_format($row['consultation_fee'], 2); ?>" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Paid Amount</label>
            <input class="form-control" type="number" name="paid_amount" value="<?php echo number_format($row['paid_amount'], 2, '.', ''); ?>" min="0" max="<?php echo number_format($row['consultation_fee'], 2, '.', ''); ?>" step="0.01" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Pending Amount</label>
            <input class="form-control" type="text" value="Rs. <?php echo number_format($row['pending_amount'], 2); ?>" readonly>
          </div>

          <button type="submit" name="update_bill" class="btn btn-success w-100">Save Payment</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>
</tbody>
</table>
<?php } else { ?>
<div class="alert alert-info">No bills available yet. Bills are created when appointments are marked completed.</div>
<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
