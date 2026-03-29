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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Our Doctors</h1>

<table border="1">
<tr>
<th>Name</th>
<th>Department</th>
<th>Available Days</th>
<th>Time</th>
<th>Consultation Fee</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
<td><?php echo $row['first_name']." ".$row['last_name']; ?></td>
<td><?php echo $row['department_name']; ?></td>
<td><?php echo $row['available_days']; ?></td>
<td><?php echo $row['start_time']." - ".$row['end_time']; ?></td>
<td><?php echo $row['consultation_fee']; ?></td>
<td><a href="doctor_profile.php?id=<?php echo $row['doctor_id']; ?>">View Profile</a></td>
</tr>

<?php } ?>

</table>

</body>
</html>