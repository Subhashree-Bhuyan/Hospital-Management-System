<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* fetch departments for dropdown */
$dept_query = "SELECT * FROM departments";
$dept_result = mysqli_query($con,$dept_query);

/* add doctor */
if(isset($_POST['add'])){

    // USER TABLE (login)
    $name = $_POST['first_name'] . " " . $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user_query = "INSERT INTO users (name,email,password,role)
                   VALUES ('$name','$email','$password','doctor')";
    mysqli_query($con,$user_query);

    $user_id = mysqli_insert_id($con);

    // DOCTOR TABLE
    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $title = $_POST['title'];
    $dept = $_POST['department_id'];
    $phone = $_POST['phone'];
    $exp = $_POST['experience'];
    $days = $_POST['available_days'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $fee = $_POST['consultation_fee'];

    // IMAGE UPLOAD
    $image_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    if($image_name != ""){
        move_uploaded_file($tmp_name, "../assets/images/".$image_name);
    }

    $query = "INSERT INTO doctors
    (user_id, first_name, middle_name, last_name, department_id, phone, experience, available_days, start_time, end_time, consultation_fee, title, image)
    VALUES
    ('$user_id','$first','$middle','$last','$dept','$phone','$exp','$days','$start','$end','$fee','$title','$image_name')";

    mysqli_query($con,$query);

    echo "<script>alert('Doctor Added Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Doctor</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h3 class="mb-4">Add Doctor</h3>

<form method="POST" enctype="multipart/form-data">

<!-- USER LOGIN DETAILS -->
<h5>Login Details</h5>

<input type="email" name="email" placeholder="Email" class="form-control mb-2" required>

<input type="password" name="password" placeholder="Password" class="form-control mb-3" required>


<!-- DOCTOR DETAILS -->
<h5>Doctor Details</h5>

<select name="title" class="form-control mb-2">
<option value="Dr">Dr</option>
<option value="Mr">Mr</option>
<option value="Mrs">Mrs</option>
<option value="Miss">Miss</option>
<option value="Ms">Ms</option>
</select>

<input type="text" name="first_name" placeholder="First Name" class="form-control mb-2" required>

<input type="text" name="middle_name" placeholder="Middle Name (Optional)" class="form-control mb-2">

<input type="text" name="last_name" placeholder="Last Name" class="form-control mb-2" required>

<!-- Department Dropdown -->
<select name="department_id" class="form-control mb-2" required>
<option value="">Select Department</option>

<?php while($row = mysqli_fetch_assoc($dept_result)){ ?>
<option value="<?php echo $row['department_id']; ?>">
<?php echo $row['department_name']; ?>
</option>
<?php } ?>

</select>

<input type="text" name="phone" placeholder="Phone" class="form-control mb-2" required>

<input type="number" name="experience" placeholder="Experience (years)" class="form-control mb-2" required>

<input type="text" name="available_days" placeholder="Available Days (e.g. Mon,Wed,Fri)" class="form-control mb-2" required>

<input type="time" name="start_time" class="form-control mb-2" required>

<input type="time" name="end_time" class="form-control mb-2" required>

<input type="number" name="consultation_fee" placeholder="Consultation Fee" class="form-control mb-2" required>

<!-- IMAGE -->
<label class="form-label">Doctor Image</label>
<input type="file" name="image" class="form-control mb-3">

<button type="submit" name="add" class="btn btn-success w-100">
Add Doctor
</button>

</form>

</div>

</body>
</html>