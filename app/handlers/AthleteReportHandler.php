<?php
require_once __DIR__ . '/../../public/library/fpdf186/fpdf.php'; 
require_once __DIR__ . '/../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['athlete', 'coach'])) {
    die('You need to be logged in as an athlete or a coach to access this.');
}

$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'];

// Determine if the report is for the logged-in athlete or a coach's athlete
$athlete_user_id = null;

// If the user is an athlete, they can only generate their own report
if ($userRole === 'athlete') {
    $athlete_user_id = $userId;
} elseif ($userRole === 'coach') {
    // If the user is a coach, they can select an athlete to generate a report for
    $athlete_user_id = $_GET['athlete_user_id'] ?? null;
    if (!$athlete_user_id) {
        die('Athlete ID is required for coaches.');
    }
}

// Fetch athlete's profile and details from the database
$stmt = $pdo->prepare('
    SELECT p.*, ad.* 
    FROM profiles p
    JOIN athlete_details ad ON p.user_id = ad.user_id
    WHERE p.user_id = ?
');
$stmt->execute([$athlete_user_id]);
$athlete = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$athlete) {
    die('Athlete not found.');
}

// Function to display the value or a dash if empty
function displayValue($value) {
    return !empty($value) ? $value : '-';
}

// Create a new PDF document
$pdf = new FPDF();
$pdf->AddPage();

// Add a title and styling
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 10, 'Generated on ' . date('Y-m-d'), 0, 1, 'R'); 
$pdf->SetDrawColor(70, 70, 100);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Athlete Report', 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Personal Information', 0, 1, 'L');

// Add Profile Information in two columns
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 8, 'Name: ' . displayValue($athlete['name']), 0, 0);
$pdf->Cell(70, 8, 'DOB: ' . displayValue($athlete['date_of_birth']), 0, 1);
$pdf->Cell(70, 8, 'Phone: ' . displayValue($athlete['phone_number']), 0, 0);
$pdf->Cell(70, 8, 'Email: ' . displayValue($athlete['email']), 0, 1);
$pdf->Cell(70, 8, 'IC Number: ' . displayValue($athlete['ic_number']), 0, 0);
$pdf->Cell(70, 8, 'Passport Number: ' . displayValue($athlete['passport_number']), 0, 1);
$pdf->Cell(70, 8, 'Passport Expiry: ' . displayValue($athlete['passport_expiry_date']), 0, 0);
$pdf->Cell(70, 8, 'Issue Place: ' . displayValue($athlete['passport_issue_place']), 0, 1);
$pdf->Cell(70, 8, 'Address: ' . displayValue($athlete['home_address']), 0, 1);


// Add line break for the next section
$pdf->Ln(5);

// Add Athlete Information
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Athlete Information', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 8, 'Mareos ID: ' . displayValue($athlete['mareos_id']), 0, 0);
$pdf->Cell(70, 8, 'Wareos ID: ' . displayValue($athlete['wareos_id']), 0, 1);
$pdf->Cell(70, 8, 'Program: ' . displayValue($athlete['program']), 0, 0);
$pdf->Cell(70, 8, 'Coach Name: ' . displayValue($athlete['coach_name']), 0, 1);
$pdf->Cell(70, 8, 'T-Shirt Size: ' . displayValue($athlete['t_shirt_size']), 0, 0);
$pdf->Cell(70, 8, 'Shoe Size: ' . displayValue($athlete['shoe_size']), 0, 1);
$pdf->Cell(70, 8, 'Medical Conditions: ' . displayValue($athlete['medical_conditions']), 0, 1);

// Add line break for the next section
$pdf->Ln(5);

// Physical Attributes and Performance
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Physical Attributes and Performance', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(70, 8, 'Height: ' . displayValue($athlete['height']) . ' cm', 0, 0);
$pdf->Cell(70, 8, 'Weight: ' . displayValue($athlete['weight']) . ' kg', 0, 1);
$pdf->Cell(70, 8, 'BMI: ' . displayValue($athlete['bmi']), 0, 0);
$pdf->Cell(70, 8, 'Seat Reach (cm): ' . displayValue($athlete['seat_reach_cm']), 0, 1);
$pdf->Cell(70, 8, 'Handgrip (kg): ' . displayValue($athlete['handgrip_kg']), 0, 0);
$pdf->Cell(70, 8, 'Broad Jump (cm): ' . displayValue($athlete['broad_jump_cm']), 0, 1);

$pdf->Cell(70, 8, 'Current PB: ' . displayValue($athlete['current_personal_best']), 0, 0);
$pdf->Cell(70, 8, 'KPI (72 Arrows): ' . displayValue($athlete['kpi_72_arrows']), 0, 1);
$pdf->Cell(70, 8, 'Average per Arrow: ' . displayValue($athlete['kpi_avg_per_arrow']), 0, 0);
$pdf->Cell(70, 8, 'Limbs Weight: ' . displayValue($athlete['limbs_weight']) . ' lbs', 0, 1);
$pdf->Cell(70, 8, 'Arrow Length: ' . displayValue($athlete['arrow_length']) . ' inches', 0, 0);
$pdf->Cell(70, 8, 'Arrow Size: ' . displayValue($athlete['arrow_size']), 0, 1);

// Output the PDF

// Download the PDF
// $pdf->Output('D', 'Athlete_Report_' . $athlete['name'] . '.pdf');

// Inline the PDF
$pdf->Output('I', 'Athlete_Report_' . $athlete['name'] . '.pdf');

