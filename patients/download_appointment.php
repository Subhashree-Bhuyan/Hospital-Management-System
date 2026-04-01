<?php
session_start();
include("../config/db.php");
require("../lib/fpdf.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid appointment request.");
}

$appointment_id = (int) $_GET['id'];

$stmt = mysqli_prepare($con, "
    SELECT
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        a.created_at AS booked_on,
        p.first_name AS patient_first_name,
        p.middle_name AS patient_middle_name,
        p.last_name AS patient_last_name,
        p.email AS patient_email,
        p.phone AS patient_phone,
        p.gender AS patient_gender,
        p.date_of_birth AS patient_dob,
        d.first_name AS doctor_first_name,
        d.middle_name AS doctor_middle_name,
        d.last_name AS doctor_last_name,
        d.phone AS doctor_phone,
        d.consultation_fee,
        dep.department_name
    FROM appointments a
    LEFT JOIN patients p ON a.patient_id = p.patient_id
    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
    LEFT JOIN departments dep ON d.department_id = dep.department_id
    WHERE a.appointment_id = ?
      AND a.patient_id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $patient_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Appointment not found.");
}

$row = mysqli_fetch_assoc($result);

function pdf_text($text)
{
    $text = (string) $text;
    $text = str_replace(array("\r", "\n"), " ", $text);
    return iconv("UTF-8", "windows-1252//TRANSLIT", $text);
}

function format_name($first, $middle, $last)
{
    return trim($first . " " . $middle . " " . $last);
}

function format_date_india($date)
{
    if (empty($date) || $date === "0000-00-00") {
        return "N/A";
    }
    return date("d M Y", strtotime($date));
}

function format_time_india($time)
{
    if (empty($time)) {
        return "N/A";
    }
    return date("h:i A", strtotime($time));
}

function calculate_age($dob)
{
    if (empty($dob) || $dob === "0000-00-00") {
        return "N/A";
    }

    $birthDate = new DateTime($dob);
    $today = new DateTime();
    return $today->diff($birthDate)->y . " years";
}

$patient_name = format_name(
    $row['patient_first_name'],
    $row['patient_middle_name'],
    $row['patient_last_name']
);

$doctor_name = format_name(
    $row['doctor_first_name'],
    $row['doctor_middle_name'],
    $row['doctor_last_name']
);

$department_name = !empty($row['department_name']) ? $row['department_name'] : "General Medicine";
$age = calculate_age($row['patient_dob']);
$gender = !empty($row['patient_gender']) ? $row['patient_gender'] : "N/A";
$fee = number_format((float) ($row['consultation_fee'] ?? 0), 2);

class AppointmentPDF extends FPDF
{
    public $logoPath = "";

    function Header()
    {
        if (!empty($this->logoPath) && file_exists($this->logoPath)) {
            $this->Image($this->logoPath, 12, 10, 22);
        }

        $this->SetXY(38, 12);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(11, 61, 145);
        $this->Cell(0, 8, pdf_text('CityCare Hospital & Research Center'), 0, 1);

        $this->SetX(38);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 6, pdf_text('123 Health Avenue, Bhubaneswar, Odisha | +91 98765 43210'), 0, 1);

        $this->SetX(38);
        $this->Cell(0, 6, pdf_text('Email: support@citycarehospital.com | www.citycarehospital.com'), 0, 1);

        $this->SetDrawColor(11, 61, 145);
        $this->SetLineWidth(0.8);
        $this->Line(10, 34, 200, 34);

        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetDrawColor(180, 180, 180);
        $this->Line(10, $this->GetY(), 200, $this->GetY());

        $this->Ln(3);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 6, pdf_text('Please carry this appointment slip during hospital visit.'), 0, 1, 'C');

        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, pdf_text('Page ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new AppointmentPDF();
$pdf->logoPath = realpath(__DIR__ . "/../assets/images/logo.png");
$pdf->SetTitle(pdf_text("Appointment_" . $appointment_id));
$pdf->SetAuthor(pdf_text("Hospital Management System"));
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

$pdf->SetTextColor(0, 0, 0);

$pdf->SetFillColor(230, 240, 255);
$pdf->SetDrawColor(180, 205, 240);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, pdf_text('APPOINTMENT REGISTRATION SLIP'), 1, 1, 'C', true);
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(248, 249, 250);
$pdf->Cell(95, 8, pdf_text('Registration No: APT-' . str_pad($row['appointment_id'], 5, '0', STR_PAD_LEFT)), 1, 0, 'L', true);
$pdf->Cell(95, 8, pdf_text('Booked On: ' . date("d M Y, h:i A", strtotime($row['booked_on']))), 1, 1, 'L', true);

$pdf->Cell(95, 8, pdf_text('Appointment Date: ' . format_date_india($row['appointment_date'])), 1, 0, 'L', true);
$pdf->Cell(95, 8, pdf_text('Appointment Time: ' . format_time_india($row['appointment_time'])), 1, 1, 'L', true);

$pdf->Cell(95, 8, pdf_text('Status: ' . $row['status']), 1, 0, 'L', true);
$pdf->Cell(95, 8, pdf_text('Department: ' . $department_name), 1, 1, 'L', true);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(11, 61, 145);
$pdf->Cell(0, 8, pdf_text('Patient Details'), 0, 1);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Rect(10, $pdf->GetY(), 190, 30);

$startY = $pdf->GetY() + 3;
$pdf->SetXY(13, $startY);
$pdf->Cell(90, 6, pdf_text('Name: ' . $patient_name), 0, 0);
$pdf->Cell(84, 6, pdf_text('Gender: ' . $gender), 0, 1);

$pdf->SetX(13);
$pdf->Cell(90, 6, pdf_text('Age: ' . $age), 0, 0);
$pdf->Cell(84, 6, pdf_text('Phone: ' . (!empty($row['patient_phone']) ? $row['patient_phone'] : 'N/A')), 0, 1);

$pdf->SetX(13);
$pdf->Cell(174, 6, pdf_text('Email: ' . (!empty($row['patient_email']) ? $row['patient_email'] : 'N/A')), 0, 1);

$pdf->Ln(18);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(11, 61, 145);
$pdf->Cell(0, 8, pdf_text('Doctor Details'), 0, 1);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Rect(10, $pdf->GetY(), 190, 24);

$startY = $pdf->GetY() + 3;
$pdf->SetXY(13, $startY);
$pdf->Cell(90, 6, pdf_text('Doctor: Dr. ' . $doctor_name), 0, 0);
$pdf->Cell(84, 6, pdf_text('Department: ' . $department_name), 0, 1);

$pdf->SetX(13);
$pdf->Cell(90, 6, pdf_text('Doctor Phone: ' . (!empty($row['doctor_phone']) ? $row['doctor_phone'] : 'N/A')), 0, 0);
$pdf->Cell(84, 6, pdf_text('Consultation Fee: Rs. ' . $fee), 0, 1);

$pdf->Ln(12);

$pdf->SetFillColor(255, 249, 230);
$pdf->SetDrawColor(230, 210, 140);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 8, pdf_text('Instructions For Patient'), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(
    190,
    7,
    pdf_text("1. Please arrive at least 15 minutes before the appointment time.\n2. Carry this slip and a valid ID proof.\n3. Bring previous prescriptions/reports if available.\n4. If you are unable to attend, contact the hospital in advance."),
    1,
    'L'
);

$pdf->Ln(12);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(110, 6, '', 0, 0);
$pdf->Cell(70, 6, pdf_text('Reception / Registration Desk'), 0, 1, 'C');

$pdf->Cell(110, 10, '', 0, 0);
$pdf->Cell(70, 10, '', 'B', 1, 'C');

$filename = "Appointment_" . $row['appointment_id'] . ".pdf";
$pdf->Output("D", $filename);
exit();
?>
