<?php
session_start();
require_once __DIR__ . '/../../config/config.php'; // Database connection, session config, etc.

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
    $fathers_name = htmlspecialchars(trim($_POST['fathers_name']));
    $fathers_phone_number = htmlspecialchars(trim($_POST['fathers_phone_number']));
    $mothers_name = htmlspecialchars(trim($_POST['mothers_name']));
    $mothers_phone_number = htmlspecialchars(trim($_POST['mothers_phone_number']));
    $mareos_id = htmlspecialchars(trim($_POST['mareos_id']));
    $wareos_id = htmlspecialchars(trim($_POST['wareos_id']));
    $program = htmlspecialchars(trim($_POST['program']));
    $coach_name = htmlspecialchars(trim($_POST['coach_name']));
    $school = htmlspecialchars(trim($_POST['school']));
    $t_shirt_size = htmlspecialchars(trim($_POST['t_shirt_size']));
    $pant_size = htmlspecialchars(trim($_POST['pant_size']));
    $shoe_size = htmlspecialchars(trim($_POST['shoe_size']));
    $medical_conditions = htmlspecialchars(trim($_POST['medical_conditions']));
    $bow_type = htmlspecialchars(trim($_POST['bow_type']));
    $started_archery = htmlspecialchars(trim($_POST['started_archery']));
    $joined_national_backup_squad = htmlspecialchars(trim($_POST['joined_national_backup_squad']));
    $joined_podium_program = htmlspecialchars(trim($_POST['joined_podium_program']));
    $arrow_type = htmlspecialchars(trim($_POST['arrow_type']));
    $arrow_size = htmlspecialchars(trim($_POST['arrow_size']));
    $arrow_length = htmlspecialchars(trim($_POST['arrow_length']));
    $limbs_type = htmlspecialchars(trim($_POST['limbs_type']));
    $limbs_length = htmlspecialchars(trim($_POST['limbs_length']));
    $limbs_weight = htmlspecialchars(trim($_POST['limbs_weight']));
    $clicking_poundage = htmlspecialchars(trim($_POST['clicking_poundage']));
    $personal_best_before = htmlspecialchars(trim($_POST['personal_best_before']));
    $current_personal_best = htmlspecialchars(trim($_POST['current_personal_best']));
    $kpi_72_arrows = htmlspecialchars(trim($_POST['kpi_72_arrows']));
    $kpi_avg_per_arrow = htmlspecialchars(trim($_POST['kpi_avg_per_arrow']));
    $height = htmlspecialchars(trim($_POST['height']));
    $weight = htmlspecialchars(trim($_POST['weight']));
    $seat_reach_cm = htmlspecialchars(trim($_POST['seat_reach_cm']));
    $handgrip_kg = htmlspecialchars(trim($_POST['handgrip_kg']));
    $shuttle_run_sec = htmlspecialchars(trim($_POST['shuttle_run_sec']));
    $shoulder_rotation = htmlspecialchars(trim($_POST['shoulder_rotation']));
    $sit_up_30_sec = htmlspecialchars(trim($_POST['sit_up_30_sec']));
    $push_up_30_sec = htmlspecialchars(trim($_POST['push_up_30_sec']));
    $broad_jump_cm = htmlspecialchars(trim($_POST['broad_jump_cm']));
    $counter_movement_jump_cm = htmlspecialchars(trim($_POST['counter_movement_jump_cm']));
    $sprint_test_10_30m_sec = htmlspecialchars(trim($_POST['sprint_test_10_30m_sec']));
    $ktk_jumping_sideway_sec = htmlspecialchars(trim($_POST['ktk_jumping_sideway_sec']));
    $kt_moving_sideway = htmlspecialchars(trim($_POST['kt_moving_sideway']));
    $ktk_walking_backward = htmlspecialchars(trim($_POST['ktk_walking_backward']));
    $bleep_test = htmlspecialchars(trim($_POST['bleep_test']));
    $bmi = htmlspecialchars(trim($_POST['bmi']));

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

$isLoggedIn = isset($_SESSION['user_id']);

