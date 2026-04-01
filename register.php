<?php
include("config/db.php");

$success_msg = "";
$error_msg = "";

if (isset($_POST['register'])) {
    $title = trim($_POST['title']);
    $first = trim($_POST['first_name']);
    $middle = trim($_POST['middle_name']);
    $last = trim($_POST['last_name']);
    $gender = trim($_POST['gender']);
    $dob = trim($_POST['dob']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if (empty($title) || empty($first) || empty($last) || empty($gender) || empty($dob) || empty($email) || empty($phone) || empty($password)) {
        $error_msg = "All required fields must be filled.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $first)) {
        $error_msg = "First name should contain only letters and spaces.";
    } elseif (!empty($middle) && !preg_match("/^[a-zA-Z ]+$/", $middle)) {
        $error_msg = "Middle name should contain only letters and spaces.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $last)) {
        $error_msg = "Last name should contain only letters and spaces.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email address.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error_msg = "Phone number must be exactly 10 digits.";
    } elseif (strtotime($dob) > time()) {
        $error_msg = "Date of birth cannot be in the future.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } else {
        $stmt = mysqli_prepare($con, "SELECT patient_id FROM patients WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $check = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check) > 0) {
            $error_msg = "Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($con, "INSERT INTO patients (title, first_name, middle_name, last_name, gender, date_of_birth, email, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssssss", $title, $first, $middle, $last, $gender, $dob, $email, $phone, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Registration successful. You can now login.";
            } else {
                $error_msg = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Patient Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
    --primary: #0f8b8d;
    --primary-dark: #0b5f61;
    --accent: #f4a261;
    --surface: rgba(255,255,255,0.95);
    --border: rgba(15, 139, 141, 0.16);
    --text: #1f2937;
    --muted: #6b7280;
}

*{
    box-sizing:border-box;
}

body{
    margin:0;
    min-height:100vh;
    font-family:'Poppins', sans-serif;
    background:
        linear-gradient(rgba(7, 33, 45, 0.78), rgba(7, 33, 45, 0.72)),
        url('assets/images/hospital1.png') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:32px 16px;
}

.register-shell{
    width:100%;
    max-width:1120px;
    display:grid;
    grid-template-columns: 1fr 1.1fr;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,255,255,0.14);
    backdrop-filter: blur(16px);
    border-radius:28px;
    overflow:hidden;
    box-shadow:0 25px 60px rgba(0,0,0,0.28);
}

.hero-panel{
    background:
        linear-gradient(160deg, rgba(15,139,141,0.92), rgba(11,95,97,0.94));
    color:#fff;
    padding:48px 40px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:relative;
}

.hero-panel::after{
    content:"";
    position:absolute;
    width:240px;
    height:240px;
    border-radius:50%;
    background:rgba(255,255,255,0.08);
    right:-60px;
    top:-60px;
}

.brand-block{
    position:relative;
    z-index:1;
}

.brand-logo{
    width:78px;
    height:78px;
    object-fit:cover;
    border-radius:18px;
    background:#fff;
    padding:10px;
    margin-bottom:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.18);
}

.brand-title{
    font-size:2rem;
    font-weight:700;
    margin-bottom:10px;
}

.brand-subtitle{
    font-size:0.98rem;
    line-height:1.8;
    color:rgba(255,255,255,0.88);
    max-width:420px;
}

.hero-points{
    margin-top:28px;
    display:grid;
    gap:14px;
    position:relative;
    z-index:1;
}

.point{
    display:flex;
    gap:12px;
    align-items:flex-start;
    background:rgba(255,255,255,0.10);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:16px;
    padding:14px 16px;
}

.point i{
    margin-top:4px;
    color:#ffe8d6;
}

.form-panel{
    background:var(--surface);
    padding:38px 34px;
}

.form-header{
    margin-bottom:24px;
}

.form-title{
    color:var(--text);
    font-size:1.8rem;
    font-weight:700;
    margin-bottom:8px;
}

.form-subtitle{
    color:var(--muted);
    margin:0;
}

.alert{
    border-radius:14px;
    font-size:0.95rem;
}

.form-label{
    font-weight:600;
    color:var(--text);
    margin-bottom:8px;
}

.input-group-text{
    background:#f8fafc;
    border:1px solid #dbe5ee;
    color:var(--primary-dark);
    min-width:46px;
    justify-content:center;
}

.form-control,
.form-select{
    border:1px solid #dbe5ee;
    padding:12px 14px;
    border-radius:12px;
    color:var(--text);
}

.form-control:focus,
.form-select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 0.2rem rgba(15,139,141,0.15);
}

.form-card{
    border:1px solid var(--border);
    border-radius:18px;
    padding:20px;
    background:#fff;
    box-shadow:0 10px 30px rgba(15, 23, 42, 0.04);
    margin-bottom:18px;
}

.section-title{
    font-size:1rem;
    font-weight:700;
    color:var(--primary-dark);
    margin-bottom:16px;
}

.password-toggle{
    cursor:pointer;
}

.btn-register{
    background:linear-gradient(135deg, var(--primary), var(--primary-dark));
    color:#fff;
    border:none;
    border-radius:14px;
    padding:13px 18px;
    font-weight:600;
    width:100%;
    transition:0.25s ease;
}

.btn-register:hover{
    transform:translateY(-1px);
    background:linear-gradient(135deg, var(--primary-dark), var(--primary));
}

.login-text{
    text-align:center;
    margin-top:18px;
    color:var(--muted);
}

.login-text a{
    color:var(--primary-dark);
    font-weight:700;
    text-decoration:none;
}

.login-text a:hover{
    text-decoration:underline;
}

@media (max-width: 991px){
    .register-shell{
        grid-template-columns:1fr;
    }

    .hero-panel{
        padding:32px 24px;
    }

    .form-panel{
        padding:28px 20px;
    }
}

@media (max-width: 576px){
    .brand-title{
        font-size:1.6rem;
    }

    .form-title{
        font-size:1.45rem;
    }

    .form-card{
        padding:16px;
    }
}
</style>
</head>
<body>

<div class="register-shell">
    <div class="hero-panel">
        <div class="brand-block">
            <img src="assets/images/logo.png" alt="Hospital Logo" class="brand-logo">
            <h1 class="brand-title">City Care Hospital</h1>
            <p class="brand-subtitle">
                Create your patient account to book appointments, download prescriptions, view bills,
                and manage your healthcare records in one place.
            </p>

            <div class="hero-points">
                <div class="point">
                    <i class="fa-solid fa-calendar-check"></i>
                    <div>
                        <strong>Easy Appointment Booking</strong><br>
                        Book doctors online with slot validation and availability checks.
                    </div>
                </div>

                <div class="point">
                    <i class="fa-solid fa-file-pdf"></i>
                    <div>
                        <strong>Instant PDF Records</strong><br>
                        Download appointment slips, prescriptions, and bills anytime.
                    </div>
                </div>

                <div class="point">
                    <i class="fa-solid fa-shield-heart"></i>
                    <div>
                        <strong>Secure Patient Access</strong><br>
                        Access your records safely with role-based login and password protection.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-header">
            <h2 class="form-title">Patient Registration</h2>
            <p class="form-subtitle">Fill in your details to create your account.</p>
        </div>

        <?php if (!empty($success_msg)) { ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php } ?>

        <?php if (!empty($error_msg)) { ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="form-card">
                <div class="section-title">Personal Information</div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Title</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <select name="title" class="form-select" required>
                                <option value="" selected disabled>Select</option>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Miss">Miss</option>
                                <option value="Ms">Ms</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">First Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" pattern="[A-Za-z ]+" required>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Middle Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" name="middle_name" class="form-control" placeholder="Middle Name" pattern="[A-Za-z ]*">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" pattern="[A-Za-z ]+" required>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-users"></i></span>
                            <select name="gender" class="form-select" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="prefer-not-to-say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            <input type="date" name="dob" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="section-title">Contact & Login Details</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            <input type="text" name="phone" class="form-control" placeholder="10-digit phone number" pattern="[0-9]{10}" maxlength="10" required>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 6 characters" minlength="6" required>
                        <span class="input-group-text password-toggle" onclick="togglePassword()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>

            <button type="submit" name="register" class="btn-register">
                <i class="fa fa-user-check me-2"></i>Create Patient Account
            </button>
        </form>

        <p class="login-text">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<script>
function togglePassword() {
    var x = document.getElementById("password");
    x.type = (x.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
