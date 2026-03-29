<?php
include('../config/db.php');

$query = "SELECT doctors.*, departments.department_name 
          FROM doctors
          JOIN departments 
          ON doctors.department_id = departments.department_id";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container mt-4">
<h2 class="text-center mb-4">Our Doctors</h2>

<div class="row">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<div class="col-md-4 mb-4">

    <div class="card shadow h-100 text-center p-3">

        <!-- Doctor Image -->
     <?php
$image = "../assets/images/default.png";

if($row['first_name'] == "Amit"){
    $image = "../assets/images/doctor1.png";
}
?>

<img src="<?php echo $image; ?>"
class="card-img-top mx-auto"
style="width:120px;height:120px;border-radius:50%;object-fit:cover;">

        <!-- Doctor Card Body -->
        <div class="card-body">

            <h5 class="card-title">
            Dr. <?php echo $row['first_name']." ".$row['last_name']; ?>
            </h5>

            <span class="badge bg-success">Available</span>

            <p class="text-muted"><?php echo $row['department_name']; ?></p>

            <p><strong>Experience:</strong> <?php echo $row['experience']; ?> yrs</p>

            <p><strong>Timing:</strong><br>
            <?php echo $row['start_time']." - ".$row['end_time']; ?>
            </p>

            <p><strong>Fee:</strong> ₹<?php echo $row['consultation_fee']; ?></p>

            <a href="doctor_profile.php?id=<?php echo $row['doctor_id']; ?>" 
            class="btn btn-success btn-sm">
            View Profile
            </a>

        </div>

    </div>

</div>

<?php } ?>

</div>
</div>
</body>
</html>