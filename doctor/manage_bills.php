<?php
session_start();
include("../config/db.php");

/* check doctor login */
if(!isset($_SESSION['doctor_id'])){
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Fetch bills for this doctor
$query = "SELECT bills.*, patients.first_name, patients.last_name, appointments.appointment_date
          FROM bills
          JOIN patients ON bills.patient_id = patients.patient_id
          JOIN appointments ON bills.appointment_id = appointments.appointment_id
          WHERE bills.doctor_id='$doctor_id'
          ORDER BY bills.created_at DESC";

$result = mysqli_query($con, $query);

if(isset($_POST['update_bill'])){
    $bill_id = $_POST['bill_id'];
    $test_fee = $_POST['test_fee'];
    $medicine_fee = $_POST['medicine_fee'];
    $paid_amount = $_POST['paid_amount'];

    // Calculate total
    $consultation_fee = $_POST['consultation_fee'];
    $total = $consultation_fee + $test_fee + $medicine_fee;
    $pending = $total - $paid_amount;

    $status = ($pending <= 0) ? 'Paid' : (($paid_amount > 0) ? 'Partial' : 'Pending');

    $update_query = "UPDATE bills SET test_fee='$test_fee', medicine_fee='$medicine_fee', total_amount='$total', paid_amount='$paid_amount', pending_amount='$pending', status='$status' WHERE bill_id='$bill_id'";
    mysqli_query($con, $update_query);

    header("Location: manage_bills.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Bills</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
<h2>Manage Bills</h2>

<table class="table table-bordered">
<thead class="table-success">
<tr>
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
<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
<td><?php echo $row['appointment_date']; ?></td>
<td><?php echo $row['consultation_fee']; ?></td>
<td><?php echo $row['test_fee'] ?? 0; ?></td>
<td><?php echo $row['medicine_fee'] ?? 0; ?></td>
<td><?php echo $row['total_amount']; ?></td>
<td><?php echo $row['paid_amount']; ?></td>
<td><?php echo $row['pending_amount']; ?></td>
<td><?php echo $row['status']; ?></td>
<td>
<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['bill_id']; ?>">Edit</button>
</td>
</tr>

<!-- Modal for editing -->
<div class="modal fade" id="editModal<?php echo $row['bill_id']; ?>" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Edit Bill</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form method="POST">
<input type="hidden" name="bill_id" value="<?php echo $row['bill_id']; ?>">
<input type="hidden" name="consultation_fee" value="<?php echo $row['consultation_fee']; ?>">
<div class="mb-3">
<label>Test Fee</label>
<input type="number" name="test_fee" class="form-control" value="<?php echo $row['test_fee'] ?? 0; ?>" step="0.01">
</div>
<div class="mb-3">
<label>Medicine Fee</label>
<input type="number" name="medicine_fee" class="form-control" value="<?php echo $row['medicine_fee'] ?? 0; ?>" step="0.01">
</div>
<div class="mb-3">
<label>Paid Amount</label>
<input type="number" name="paid_amount" class="form-control" value="<?php echo $row['paid_amount']; ?>" step="0.01">
</div>
<button type="submit" name="update_bill" class="btn btn-primary">Update</button>
</form>
</div>
</div>
</div>
</div>

<?php } ?>
</tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>