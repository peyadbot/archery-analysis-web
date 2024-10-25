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

$stmt = $pdo->prepare('
    SELECT p.*, ad.*, pg.program_name 
    FROM profiles p
    LEFT JOIN athlete_details ad ON p.user_id = ad.user_id
    LEFT JOIN programs pg ON ad.program = pg.program_id
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
$pdf->Cell(0, 10, 'Details Report', 0, 1, 'C');

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
$pdf->Cell(70, 8, 'Mareos ID: ' . displayValue($profile['mareos_id']), 0, 1);
$pdf->Cell(70, 8, 'Wareos ID: ' . displayValue($profile['wareos_id']), 0, 0);
$pdf->Cell(70, 8, 'Birth Date: ' . displayValue($profile['date_of_birth']), 0, 1);
$pdf->Cell(70, 8, 'Phone: ' . displayValue($profile['phone_number']), 0, 0);
$pdf->Cell(70, 8, 'Email: ' . displayValue($profile['email']), 0, 1);
$pdf->Cell(70, 8, 'IC Number: ' . displayValue($profile['ic_number']), 0, 0);
$pdf->Cell(70, 8, 'Passport Number: ' . displayValue($profile['passport_number']), 0, 1);
$pdf->Cell(70, 8, 'Passport Expiry: ' . displayValue($profile['passport_expiry_date']), 0, 0);
$pdf->Cell(70, 8, 'Issue Place: ' . displayValue($profile['passport_issue_place']), 0, 1);
$pdf->Cell(70, 8, 'Gender: ' . displayValue($profile['gender']), 0, 0);
$pdf->Cell(70, 8, 'Address: ' . displayValue($profile['home_address']), 0, 1);

// Parent Information
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Parent Information', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 8, "Father's Name: " . displayValue($profile['fathers_name']), 0, 0);
$pdf->Cell(70, 8, "Father's Phone: " . displayValue($profile['fathers_phone_number']), 0, 1);
$pdf->Cell(70, 8, "Mother's Name: " . displayValue($profile['mothers_name']), 0, 0);
$pdf->Cell(70, 8, "Mother's Phone: " . displayValue($profile['mothers_phone_number']), 0, 1);

// Athlete details
if (!empty($profile['athlete_id'])) {
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Athlete Information', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 8, 'Program: ' . displayValue($profile['program_name']), 0, 0);
    $pdf->Cell(70, 8, 'Coach Name: ' . displayValue($profile['coach_name']), 0, 1);
    $pdf->Cell(70, 8, 'School: ' . displayValue($profile['school']), 0, 1);
    $pdf->Cell(70, 8, 'T-Shirt Size: ' . displayValue($profile['t_shirt_size']), 0, 0);
    $pdf->Cell(70, 8, 'Pant Size: ' . displayValue($profile['pant_size']), 0, 1);
    $pdf->Cell(70, 8, 'Shoe Size: ' . displayValue($profile['shoe_size']), 0, 0);
    $pdf->Cell(70, 8, 'Medical Conditions: ' . displayValue($profile['medical_conditions']), 0, 1);

    // Archery History
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Archery History', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 8, 'Started Archery: ' . displayValue($profile['started_archery']), 0, 0);
    $pdf->Cell(70, 8, 'Joined Backup Squad: ' . displayValue($profile['joined_national_backup_squad']), 0, 1);
    $pdf->Cell(70, 8, 'Joined Podium Program: ' . displayValue($profile['joined_podium_program']), 0, 1);

    // Equipment Details
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Equipment Details', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 8, 'Bow Type: ' . displayValue($profile['bow_type']), 0, 0);
    $pdf->Cell(70, 8, 'Arrow Type: ' . displayValue($profile['arrow_type']), 0, 1);
    $pdf->Cell(70, 8, 'Arrow Length: ' . displayValue($profile['arrow_length']) . ' inches', 0, 0);
    $pdf->Cell(70, 8, 'Arrow Size: ' . displayValue($profile['arrow_size']), 0, 1);
    $pdf->Cell(70, 8, 'Limbs Type: ' . displayValue($profile['limbs_type']), 0, 0);
    $pdf->Cell(70, 8, 'Limbs Length: ' . displayValue($profile['limbs_length']), 0, 1);
    $pdf->Cell(70, 8, 'Limbs Weight: ' . displayValue($profile['limbs_weight']) . ' lbs', 0, 0);
    $pdf->Cell(70, 8, 'Clicking Poundage: ' . displayValue($profile['clicking_poundage']) . ' lbs', 0, 1);

    // Performance Metrics
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Performance Metrics', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 8, 'Previous PB: ' . displayValue($profile['personal_best_before']), 0, 0);
    $pdf->Cell(70, 8, 'Current PB: ' . displayValue($profile['current_personal_best']), 0, 1);
    $pdf->Cell(70, 8, 'KPI (72 Arrows): ' . displayValue($profile['kpi_72_arrows']), 0, 0);
    $pdf->Cell(70, 8, 'Average per Arrow: ' . displayValue($profile['kpi_avg_per_arrow']), 0, 1);

    // Physical Attributes
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Physical Attributes', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 8, 'Height: ' . displayValue($profile['height']) . ' cm', 0, 0);
    $pdf->Cell(70, 8, 'Weight: ' . displayValue($profile['weight']) . ' kg', 0, 1);
    $pdf->Cell(70, 8, 'BMI: ' . displayValue($profile['bmi']), 0, 1);

    // Fitness Test Results
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Fitness Test Results', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 8, 'Seat Reach: ' . displayValue($profile['seat_reach_cm']) . ' cm', 0, 0);
    $pdf->Cell(70, 8, 'Handgrip: ' . displayValue($profile['handgrip_kg']) . ' kg', 0, 1);
    $pdf->Cell(70, 8, 'Shuttle Run: ' . displayValue($profile['shuttle_run_sec']) . ' sec', 0, 0);
    $pdf->Cell(70, 8, 'Shoulder Rotation: ' . displayValue($profile['shoulder_rotation']), 0, 1);
    $pdf->Cell(70, 8, 'Sit-ups (30s): ' . displayValue($profile['sit_up_30_sec']), 0, 0);
    $pdf->Cell(70, 8, 'Push-ups (30s): ' . displayValue($profile['push_up_30_sec']), 0, 1);
    $pdf->Cell(70, 8, 'Broad Jump: ' . displayValue($profile['broad_jump_cm']) . ' cm', 0, 0);
    $pdf->Cell(70, 8, 'Counter Movement Jump: ' . displayValue($profile['counter_movement_jump_cm']) . ' cm', 0, 1);
    $pdf->Cell(70, 8, 'Sprint Test (10/30m): ' . displayValue($profile['sprint_test_10_30m_sec']) . ' sec', 0, 1);
    $pdf->Cell(70, 8, 'KTK Jumping Sideway: ' . displayValue($profile['ktk_jumping_sideway_sec']) . ' sec', 0, 0);
    $pdf->Cell(70, 8, 'KTK Moving Sideway: ' . displayValue($profile['kt_moving_sideway']), 0, 1);
    $pdf->Cell(70, 8, 'KTK Walking Backward: ' . displayValue($profile['ktk_walking_backward']), 0, 0);
    $pdf->Cell(70, 8, 'Bleep Test: ' . displayValue($profile['bleep_test']), 0, 1);
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

