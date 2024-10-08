<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = '';
$athlete = null;

// Fetch current athlete details if they exist
$stmt = $pdo->prepare('SELECT * FROM athlete_details WHERE user_id = ?');
$stmt->execute([$user_id]);
$athlete = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Create and Update
    $fathers_name = !empty($_POST['fathers_name']) ? htmlspecialchars(trim($_POST['fathers_name'])) : null;
    $fathers_phone_number = !empty($_POST['fathers_phone_number']) ? htmlspecialchars(trim($_POST['fathers_phone_number'])) : null;
    $mothers_name = !empty($_POST['mothers_name']) ? htmlspecialchars(trim($_POST['mothers_name'])) : null;
    $mothers_phone_number = !empty($_POST['mothers_phone_number']) ? htmlspecialchars(trim($_POST['mothers_phone_number'])) : null;
    $mareos_id = !empty($_POST['mareos_id']) ? htmlspecialchars(trim($_POST['mareos_id'])) : null;
    $wareos_id = !empty($_POST['wareos_id']) ? htmlspecialchars(trim($_POST['wareos_id'])) : null;
    $program = !empty($_POST['program']) ? htmlspecialchars(trim($_POST['program'])) : null;
    $coach_name = !empty($_POST['coach_name']) ? htmlspecialchars(trim($_POST['coach_name'])) : null;
    $school = !empty($_POST['school']) ? htmlspecialchars(trim($_POST['school'])) : null;
    $t_shirt_size = !empty($_POST['t_shirt_size']) ? htmlspecialchars(trim($_POST['t_shirt_size'])) : null;
    $pant_size = !empty($_POST['pant_size']) ? htmlspecialchars(trim($_POST['pant_size'])) : null;
    $shoe_size = !empty($_POST['shoe_size']) ? htmlspecialchars(trim($_POST['shoe_size'])) : null;
    $medical_conditions = !empty($_POST['medical_conditions']) ? htmlspecialchars(trim($_POST['medical_conditions'])) : null;

    // Handle dates
    $started_archery = !empty($_POST['started_archery']) ? $_POST['started_archery'] : null;
    $joined_national_backup_squad = !empty($_POST['joined_national_backup_squad']) ? $_POST['joined_national_backup_squad'] : null;
    $joined_podium_program = !empty($_POST['joined_podium_program']) ? $_POST['joined_podium_program'] : null;

    // Handle numeric/decimal 
    $bow_type = !empty($_POST['bow_type']) ? htmlspecialchars(trim($_POST['bow_type'])) : null;
    $arrow_type = !empty($_POST['arrow_type']) ? htmlspecialchars(trim($_POST['arrow_type'])) : null;
    $arrow_size = !empty($_POST['arrow_size']) ? htmlspecialchars(trim($_POST['arrow_size'])) : null;
    $arrow_length = !empty($_POST['arrow_length']) ? $_POST['arrow_length'] : null;
    $limbs_type = !empty($_POST['limbs_type']) ? htmlspecialchars(trim($_POST['limbs_type'])) : null;
    $limbs_length = !empty($_POST['limbs_length']) ? $_POST['limbs_length'] : null;
    $limbs_weight = !empty($_POST['limbs_weight']) ? $_POST['limbs_weight'] : null;
    $clicking_poundage = !empty($_POST['clicking_poundage']) ? $_POST['clicking_poundage'] : null;
    $personal_best_before = !empty($_POST['personal_best_before']) ? $_POST['personal_best_before'] : null;
    $current_personal_best = !empty($_POST['current_personal_best']) ? $_POST['current_personal_best'] : null;
    $kpi_72_arrows = !empty($_POST['kpi_72_arrows']) ? $_POST['kpi_72_arrows'] : null;
    $kpi_avg_per_arrow = !empty($_POST['kpi_avg_per_arrow']) ? $_POST['kpi_avg_per_arrow'] : null;

    // Handle fitness-related
    $height = !empty($_POST['height']) ? $_POST['height'] : null;
    $weight = !empty($_POST['weight']) ? $_POST['weight'] : null;
    $seat_reach_cm = !empty($_POST['seat_reach_cm']) ? $_POST['seat_reach_cm'] : null;
    $handgrip_kg = !empty($_POST['handgrip_kg']) ? $_POST['handgrip_kg'] : null;
    $shuttle_run_sec = !empty($_POST['shuttle_run_sec']) ? $_POST['shuttle_run_sec'] : null;
    $shoulder_rotation = !empty($_POST['shoulder_rotation']) ? $_POST['shoulder_rotation'] : null;
    $sit_up_30_sec = !empty($_POST['sit_up_30_sec']) ? $_POST['sit_up_30_sec'] : null;
    $push_up_30_sec = !empty($_POST['push_up_30_sec']) ? $_POST['push_up_30_sec'] : null;
    $broad_jump_cm = !empty($_POST['broad_jump_cm']) ? $_POST['broad_jump_cm'] : null;
    $counter_movement_jump_cm = !empty($_POST['counter_movement_jump_cm']) ? $_POST['counter_movement_jump_cm'] : null;
    $sprint_test_10_30m_sec = !empty($_POST['sprint_test_10_30m_sec']) ? $_POST['sprint_test_10_30m_sec'] : null;
    $ktk_jumping_sideway_sec = !empty($_POST['ktk_jumping_sideway_sec']) ? $_POST['ktk_jumping_sideway_sec'] : null;
    $kt_moving_sideway = !empty($_POST['kt_moving_sideway']) ? $_POST['kt_moving_sideway'] : null;
    $ktk_walking_backward = !empty($_POST['ktk_walking_backward']) ? $_POST['ktk_walking_backward'] : null;
    $bleep_test = !empty($_POST['bleep_test']) ? $_POST['bleep_test'] : null;
    $bmi = !empty($_POST['bmi']) ? $_POST['bmi'] : null;

    // Insert or update athlete details based on whether they exist
    if ($athlete) {
        // Update existing athlete details
        try {
            $stmt = $pdo->prepare("
                UPDATE athlete_details SET 
                    fathers_name = ?, 
                    fathers_phone_number = ?, 
                    mothers_name = ?, 
                    mothers_phone_number = ?, 
                    mareos_id = ?, 
                    wareos_id = ?, 
                    program = ?, 
                    coach_name = ?, 
                    school = ?, 
                    t_shirt_size = ?, 
                    pant_size = ?, 
                    shoe_size = ?, 
                    medical_conditions = ?, 
                    bow_type = ?, 
                    started_archery = ?, 
                    joined_national_backup_squad = ?, 
                    joined_podium_program = ?, 
                    arrow_type = ?, 
                    arrow_size = ?, 
                    arrow_length = ?, 
                    limbs_type = ?, 
                    limbs_length = ?, 
                    limbs_weight = ?, 
                    clicking_poundage = ?, 
                    personal_best_before = ?, 
                    current_personal_best = ?, 
                    kpi_72_arrows = ?, 
                    kpi_avg_per_arrow = ?, 
                    height = ?, 
                    weight = ?, 
                    seat_reach_cm = ?, 
                    handgrip_kg = ?, 
                    shuttle_run_sec = ?, 
                    shoulder_rotation = ?, 
                    sit_up_30_sec = ?, 
                    push_up_30_sec = ?, 
                    broad_jump_cm = ?, 
                    counter_movement_jump_cm = ?, 
                    sprint_test_10_30m_sec = ?, 
                    ktk_jumping_sideway_sec = ?, 
                    kt_moving_sideway = ?, 
                    ktk_walking_backward = ?, 
                    bleep_test = ?, 
                    bmi = ?
                WHERE user_id = ?
            ");
            $stmt->execute([
                $fathers_name, $fathers_phone_number, $mothers_name, $mothers_phone_number, $mareos_id, 
                $wareos_id, $program, $coach_name, $school, $t_shirt_size, $pant_size, $shoe_size, 
                $medical_conditions, $bow_type, $started_archery, $joined_national_backup_squad, $joined_podium_program, 
                $arrow_type, $arrow_size, $arrow_length, $limbs_type, $limbs_length, $limbs_weight, $clicking_poundage, 
                $personal_best_before, $current_personal_best, $kpi_72_arrows, $kpi_avg_per_arrow, $height, 
                $weight, $seat_reach_cm, $handgrip_kg, $shuttle_run_sec, $shoulder_rotation, $sit_up_30_sec, 
                $push_up_30_sec, $broad_jump_cm, $counter_movement_jump_cm, $sprint_test_10_30m_sec, 
                $ktk_jumping_sideway_sec, $kt_moving_sideway, $ktk_walking_backward, $bleep_test, $bmi, $user_id
            ]);
            $_SESSION['success'] = 'Athlete details updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update athlete details: ' . $e->getMessage();
        }
    } else {
        // Insert new athlete details
        try {
            $stmt = $pdo->prepare("
            INSERT INTO athlete_details (
                user_id, fathers_name, fathers_phone_number, mothers_name, mothers_phone_number, 
                mareos_id, wareos_id, program, coach_name, school, t_shirt_size, pant_size, shoe_size, 
                medical_conditions, bow_type, started_archery, joined_national_backup_squad, joined_podium_program, 
                arrow_type, arrow_size, arrow_length, limbs_type, limbs_length, limbs_weight, clicking_poundage, 
                personal_best_before, current_personal_best, kpi_72_arrows, kpi_avg_per_arrow, height, weight, 
                seat_reach_cm, handgrip_kg, shuttle_run_sec, shoulder_rotation, sit_up_30_sec, push_up_30_sec, 
                broad_jump_cm, counter_movement_jump_cm, sprint_test_10_30m_sec, ktk_jumping_sideway_sec, 
                kt_moving_sideway, ktk_walking_backward, bleep_test, bmi
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
    
        $stmt->execute([
            $user_id, $fathers_name, $fathers_phone_number, $mothers_name, $mothers_phone_number, $mareos_id, 
            $wareos_id, $program, $coach_name, $school, $t_shirt_size, $pant_size, $shoe_size, 
            $medical_conditions, $bow_type, $started_archery, $joined_national_backup_squad, $joined_podium_program, 
            $arrow_type, $arrow_size, $arrow_length, $limbs_type, $limbs_length, $limbs_weight, $clicking_poundage, 
            $personal_best_before, $current_personal_best, $kpi_72_arrows, $kpi_avg_per_arrow, $height, 
            $weight, $seat_reach_cm, $handgrip_kg, $shuttle_run_sec, $shoulder_rotation, $sit_up_30_sec, 
            $push_up_30_sec, $broad_jump_cm, $counter_movement_jump_cm, $sprint_test_10_30m_sec, 
            $ktk_jumping_sideway_sec, $kt_moving_sideway, $ktk_walking_backward, $bleep_test, $bmi
        ]);
            $_SESSION['success'] = 'Athlete details created successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to create athlete details: ' . $e->getMessage();
        }
    }

    // Redirect to avoid form resubmission
    header('Location: profile.php');
    exit;
}

// Handle Delete (optional if necessary)
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM athlete_details WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $_SESSION['success'] = 'Athlete details deleted successfully!';
        header('Location: profile.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete athlete details: ' . $e->getMessage();
    }
}

// Fetch updated athlete details
$stmt = $pdo->prepare('SELECT * FROM athlete_details WHERE user_id = ?');
$stmt->execute([$user_id]);
$athlete = $stmt->fetch();

$stmt = $pdo->prepare('SELECT program_id, program_name FROM programs');
$stmt->execute();
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isLoggedIn = isset($_SESSION['user_id']);

