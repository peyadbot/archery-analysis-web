<?php
require_once __DIR__ . '/../../public/library/fpdf186/fpdf.php'; 
require_once __DIR__ . '/../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['athlete', 'coach', 'admin'])) {
    die('You need to be logged in as an athlete, coach, or admin to access this.');
}

$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$athlete_user_id = null;

if ($userRole === 'athlete') {
    $athlete_user_id = $userId;
} elseif ($userRole === 'coach') {
    $athlete_user_id = $_GET['athlete_user_id'] ?? null;
    if (!$athlete_user_id) {
        $athlete_user_id = $userId;
    }
} else {
    $athlete_user_id = $_GET['athlete_user_id'] ?? null;
    if (!$athlete_user_id) {
        die('Athlete or Coach ID is required.');
    }
}

// Fetch profile and athlete details (if the user is an athlete)
$stmt = $pdo->prepare('
    SELECT p.*, ad.* 
    FROM profiles p
    LEFT JOIN athlete_details ad ON p.user_id = ad.user_id
    WHERE p.user_id = ?
');
$stmt->execute([$athlete_user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    die('User not found or Profile not complited.');
}

function displayValue($value) {
    return !empty($value) ? $value : '-';
}

$pdf = new FPDF();
$pdf->AddPage();

// Title and styling
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 10, 'Generated on ' . date('Y-m-d'), 0, 1, 'R'); 
$pdf->SetDrawColor(70, 70, 100);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'User Report', 0, 1, 'C');

// Add profile picture if available
if (!empty($profile['profile_picture'])) {
    $profile_picture_path = __DIR__ . '/../../public/images/profile_picture/' . $profile['profile_picture'];

    if (file_exists($profile_picture_path)) {
        $pdf->Image($profile_picture_path, 10, 30, 40, 53);
    } else {
        $pdf->Cell(0, 10, 'Profile picture not found.', 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No profile picture uploaded.', 0, 1);
}

$pdf->Ln(60);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Personal Information', 0, 1, 'L');

// Profile Information 
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 8, 'Name: ' . displayValue($profile['name']), 0, 0);
$pdf->Cell(70, 8, 'DOB: ' . displayValue($profile['date_of_birth']), 0, 1);
$pdf->Cell(70, 8, 'Phone: ' . displayValue($profile['phone_number']), 0, 0);
$pdf->Cell(70, 8, 'Email: ' . displayValue($profile['email']), 0, 1);
$pdf->Cell(70, 8, 'IC Number: ' . displayValue($profile['ic_number']), 0, 0);
$pdf->Cell(70, 8, 'Passport Number: ' . displayValue($profile['passport_number']), 0, 1);
$pdf->Cell(70, 8, 'Passport Expiry: ' . displayValue($profile['passport_expiry_date']), 0, 0);
$pdf->Cell(70, 8, 'Issue Place: ' . displayValue($profile['passport_issue_place']), 0, 1);
$pdf->Cell(70, 8, 'Address: ' . displayValue($profile['home_address']), 0, 1);

// Athlete details
if (!empty($profile['mareos_id'])) {
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Athlete Information', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 8, 'Mareos ID: ' . displayValue($profile['mareos_id']), 0, 0);
    $pdf->Cell(70, 8, 'Wareos ID: ' . displayValue($profile['wareos_id']), 0, 1);
    $pdf->Cell(70, 8, 'Program: ' . displayValue($profile['program']), 0, 0);
    $pdf->Cell(70, 8, 'Coach Name: ' . displayValue($profile['coach_name']), 0, 1);
    $pdf->Cell(70, 8, 'T-Shirt Size: ' . displayValue($profile['t_shirt_size']), 0, 0);
    $pdf->Cell(70, 8, 'Shoe Size: ' . displayValue($profile['shoe_size']), 0, 1);
    $pdf->Cell(70, 8, 'Medical Conditions: ' . displayValue($profile['medical_conditions']), 0, 1);

    // Add physical attributes and performance for athletes
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Physical Attributes and Performance', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);

    $pdf->Cell(70, 8, 'Height: ' . displayValue($profile['height']) . ' cm', 0, 0);
    $pdf->Cell(70, 8, 'Weight: ' . displayValue($profile['weight']) . ' kg', 0, 1);
    $pdf->Cell(70, 8, 'BMI: ' . displayValue($profile['bmi']), 0, 0);
    $pdf->Cell(70, 8, 'Seat Reach (cm): ' . displayValue($profile['seat_reach_cm']), 0, 1);
    $pdf->Cell(70, 8, 'Handgrip (kg): ' . displayValue($profile['handgrip_kg']), 0, 0);
    $pdf->Cell(70, 8, 'Broad Jump (cm): ' . displayValue($profile['broad_jump_cm']), 0, 1);

    $pdf->Cell(70, 8, 'Current PB: ' . displayValue($profile['current_personal_best']), 0, 0);
    $pdf->Cell(70, 8, 'KPI (72 Arrows): ' . displayValue($profile['kpi_72_arrows']), 0, 1);
    $pdf->Cell(70, 8, 'Average per Arrow: ' . displayValue($profile['kpi_avg_per_arrow']), 0, 0);
    $pdf->Cell(70, 8, 'Limbs Weight: ' . displayValue($profile['limbs_weight']) . ' lbs', 0, 1);
    $pdf->Cell(70, 8, 'Arrow Length: ' . displayValue($profile['arrow_length']) . ' inches', 0, 0);
    $pdf->Cell(70, 8, 'Arrow Size: ' . displayValue($profile['arrow_size']), 0, 1);
}

// IC document if available
if (!empty($profile['ic_file'])) {
    $ic_file_path = __DIR__ . '/../../public/images/ic_file/' . $profile['ic_file'];

    if (file_exists($ic_file_path)) {
        $pdf->AddPage(); 

        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'IC File', 0, 1, 'R');

        // 3/4 size (75%)
        $scaled_width = 210 * 0.75;
        $scaled_height = 297 * 0.75;

        $x = (210 - $scaled_width) / 2;
        $y = (297 - $scaled_height) / 2;

        $pdf->Image($ic_file_path, $x, $y, $scaled_width, $scaled_height);
    } else {
        $pdf->AddPage();
        $pdf->Cell(0, 10, 'IC file not found.', 0, 1);
    }
} else {
    $pdf->AddPage();
    $pdf->Cell(0, 10, 'No IC file uploaded.', 0, 1);
}

// Passport document if available
if (!empty($profile['passport_file'])) {
    $passport_file_path = __DIR__ . '/../../public/images/passport_file/' . $profile['passport_file'];

    if (file_exists($passport_file_path)) {
        $pdf->AddPage(); 

        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Passport File', 0, 1, 'R');

        // 3/4 size (75%)
        $scaled_width = 210 * 0.75;
        $scaled_height = 297 * 0.75;

        $x = (210 - $scaled_width) / 2;
        $y = (297 - $scaled_height) / 2;

        $pdf->Image($passport_file_path, $x, $y, $scaled_width, $scaled_height);
    } else {
        $pdf->AddPage(); 
        $pdf->Cell(0, 10, 'Passport file not found.', 0, 1);
    }
} else {
    $pdf->AddPage(); 
    $pdf->Cell(0, 10, 'No passport file uploaded.', 0, 1);
}


// Output the PDF
$pdf->Output('I', 'User_Report_' . $profile['name'] . '.pdf');

