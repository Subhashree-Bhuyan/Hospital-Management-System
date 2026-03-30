<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    echo "Invalid Request";
    exit();
}

$id = $_GET['id'];

/* fetch doctor */
$query = "SELECT * FROM doctors WHERE doctor_id='$id'";
$result = mysqli_query($con,$query);
if(!isset($_GET['id'])){
    echo "Invalid Request";
    exit();
}
$row = mysqli_fetch_assoc($result);

/* update doctor */
if(isset($_POST['update'])){

    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $phone = $_POST['phone'];

    // image upload (optional)
    $image_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    if($image_name != ""){
        move_uploaded_file($tmp_name, "../assets/images/".$image_name);

        $update = "UPDATE doctors SET 
        first_name='$first',
        middle_name='$middle',
        last_name='$last',
        phone='$phone',
        image='$image_name'
        WHERE doctor_id='$id'";
    }else{
        $update = "UPDATE doctors SET 
        first_name='$first',
        middle_name='$middle',
        last_name='$last',
        phone='$phone'
        WHERE doctor_id='$id'";
    }

    mysqli_query($con,$update);

    echo "<script>alert('Doctor Updated');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Doctor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h3>Edit Doctor</h3>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="first_name" value="<?php echo $row['first_name']; ?>" class="form-control mb-2">

<input type="text" name="middle_name" value="<?php echo $row['middle_name']; ?>" class="form-control mb-2">

<input type="text" name="last_name" value="<?php echo $row['last_name']; ?>" class="form-control mb-2">

<input type="text" name="phone" value="<?php echo $row['phone']; ?>" class="form-control mb-2">

<!-- show current image -->
<?php if($row['image'] != ""){ ?>
<img src="../assets/images/<?php echo $row['image']; ?>" width="100"><br><br>
<?php } ?>

<label>Upload New Image</label>
<input type="file" name="image" class="form-control mb-3">

<button type="submit" name="update" class="btn btn-success">
Update Doctor
</button>

</form>

</div>

</body>
</html>