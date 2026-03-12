<?php
include('../config/db.php');

$id = $_GET['id'];

$query = "SELECT doctors.*, departments.department_name 
          FROM doctors
          JOIN departments
          ON doctors.department_id = departments.department_id
          WHERE doctor_id='$id'";

$result = mysqli_query($con,$query);

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Profile</title>
</head>

<body>
    <h1>Doctor Profile</h1>

    <p><strong>Name:</strong>
    <?php echo $row['first_name']." ".$row['last_name']?>
    </p>

    <p><strong>Experience:</strong> 
    <?php echo $row['experience']; ?> years
    </p>

    <p><strong>Available Days:</strong> 
    <?php echo $row['available_days']; ?>
    </p>

    <p><strong>Time:</strong> 
    <?php echo $row['start_time']." - ".$row['end_time']; ?>
    </p>

    <p><strong>Consultation Fee:</strong> 
    <?php echo $row['consultation_fee']; ?>
    </p>

    <br>

    <a href="book_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>">Book Appointment</a>
</body>
</html>