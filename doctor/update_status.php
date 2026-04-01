<?php
session_start();
include("../config/db.php");

/* check doctor login */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$doc_query = "SELECT doctor_id FROM doctors WHERE user_id='$user_id' LIMIT 1";
$doc_result = mysqli_query($con, $doc_query);
$doc_row = mysqli_fetch_assoc($doc_result);

if (!$doc_row) {
    header("Location: dashboard.php");
    exit();
}

$doctor_id = (int) $doc_row['doctor_id'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$allowed_status = array('Pending', 'Completed', 'Cancelled');
if ($id <= 0 || !in_array($status, $allowed_status)) {
    header("Location: dashboard.php");
    exit();
}

/* make sure doctor owns appointment */
$check_q = "SELECT a.*, d.consultation_fee
            FROM appointments a
            LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
            WHERE a.appointment_id='$id' AND a.doctor_id='$doctor_id'
            LIMIT 1";
$check_r = mysqli_query($con, $check_q);

if (mysqli_num_rows($check_r) != 1) {
    header("Location: dashboard.php");
    exit();
}

$appointment = mysqli_fetch_assoc($check_r);

/* update appointment status */
$query = "UPDATE appointments 
          SET status='$status' 
          WHERE appointment_id='$id' AND doctor_id='$doctor_id'";
mysqli_query($con, $query);

/* auto create bill only when appointment is completed */
if ($status == 'Completed') {
    $bill_check = "SELECT bill_id FROM bills WHERE appointment_id='$id' LIMIT 1";
    $bill_result = mysqli_query($con, $bill_check);

    if (mysqli_num_rows($bill_result) == 0) {
        $patient_id = (int) $appointment['patient_id'];
        $consultation_fee = (float) $appointment['consultation_fee'];
        $test_fee = 0.00;
        $medicine_fee = 0.00;
        $total_amount = $consultation_fee;
        $paid_amount = 0.00;
        $pending_amount = $total_amount;
        $bill_status = 'Pending';

        $insert_bill = "INSERT INTO bills (
                            appointment_id,
                            patient_id,
                            doctor_id,
                            consultation_fee,
                            test_fee,
                            medicine_fee,
                            total_amount,
                            paid_amount,
                            pending_amount,
                            status
                        ) VALUES (
                            '$id',
                            '$patient_id',
                            '$doctor_id',
                            '$consultation_fee',
                            '$test_fee',
                            '$medicine_fee',
                            '$total_amount',
                            '$paid_amount',
                            '$pending_amount',
                            '$bill_status'
                        )";
        mysqli_query($con, $insert_bill);
    }
}

/* redirect back */
header("Location: dashboard.php");
exit();
?>
