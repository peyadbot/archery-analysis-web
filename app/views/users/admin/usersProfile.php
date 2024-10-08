<?php
require_once __DIR__ . '/../../../../config/config.php';

if (!isset($_GET['user_id'])) {
    $_SESSION['error'] = 'User ID is missing.';
    header('Location: admin_view_users.php');
    exit;
}

$user_id = $_GET['user_id'];

$stmt = $pdo->prepare('SELECT u.*, p.*, a.* FROM users u 
    LEFT JOIN profiles p ON u.user_id = p.user_id 
    LEFT JOIN athlete_details a ON u.user_id = a.user_id 
    WHERE u.user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: admin_view_users.php');
    exit;
} 
?>

<!-- Modal users in admin manage user -->
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h2><?php echo htmlspecialchars($user['name'] ?? $user['username']); ?></h2>
        </div>
        <div class="card-body">
            <h4 class="mt-4 mb-2"><strong>Basic Information</strong></h4>
            <p><img src="<?php echo !empty($user['profile_picture']) ? BASE_URL . 'public/images/profile_picture/' . htmlspecialchars($user['profile_picture']) : BASE_URL . 'public/images/profile_picture/default_image.jpg'; ?>" alt="Profile Picture" class="img-thumbnail" style="max-width: 150px;"></p>
            <p><strong>Username:</strong> <?php echo !empty($user['username']) ? htmlspecialchars($user['username']) : 'Not filled in'; ?></p>
            <p><strong>Email:</strong> <?php echo !empty($user['email']) ? htmlspecialchars($user['email']) : 'Not filled in'; ?></p>
            <p><strong>Date of Birth:</strong> <?php echo !empty($user['date_of_birth']) ? htmlspecialchars($user['date_of_birth']) : 'Not filled in'; ?></p>
            <p><strong>Phone Number:</strong> <?php echo !empty($user['phone_number']) ? htmlspecialchars($user['phone_number']) : 'Not filled in'; ?></p>
            <p><strong>IC Number:</strong> <?php echo !empty($user['ic_number']) ? htmlspecialchars($user['ic_number']) : 'Not filled in'; ?></p>
            <p><strong>IC File:</strong> <?php echo !empty($user['ic_file']) ? '<a href="' . htmlspecialchars($user['ic_file']) . '" target="_blank">View File</a>' : 'Not uploaded'; ?></p>
            <p><strong>Passport Number:</strong> <?php echo !empty($user['passport_number']) ? htmlspecialchars($user['passport_number']) : 'Not filled in'; ?></p>
            <p><strong>Passport File:</strong> <?php echo !empty($user['passport_file']) ? '<a href="' . htmlspecialchars($user['passport_file']) . '" target="_blank">View File</a>' : 'Not uploaded'; ?></p>
            <p><strong>Passport Expiry Date:</strong> <?php echo !empty($user['passport_expiry_date']) ? htmlspecialchars($user['passport_expiry_date']) : 'Not filled in'; ?></p>
            <p><strong>Passport Issue Place:</strong> <?php echo !empty($user['passport_issue_place']) ? htmlspecialchars($user['passport_issue_place']) : 'Not filled in'; ?></p>
            <p><strong>Home Address:</strong> <?php echo !empty($user['home_address']) ? htmlspecialchars($user['home_address']) : 'Not filled in'; ?></p>
            <p><strong>Gender:</strong> <?php echo !empty($user['gender']) ? htmlspecialchars($user['gender']) : 'Not filled in'; ?></p>

            <?php if (!empty($user['athlete_id'])): ?>
                <h4 class="mt-4 mb-2"><strong>Family Information</strong></h4>
                <p><strong>Father's Name:</strong> <?php echo !empty($user['fathers_name']) ? htmlspecialchars($user['fathers_name']) : 'Not filled in'; ?></p>
                <p><strong>Father's Phone Number:</strong> <?php echo !empty($user['fathers_phone_number']) ? htmlspecialchars($user['fathers_phone_number']) : 'Not filled in'; ?></p>
                <p><strong>Mother's Name:</strong> <?php echo !empty($user['mothers_name']) ? htmlspecialchars($user['mothers_name']) : 'Not filled in'; ?></p>
                <p><strong>Mother's Phone Number:</strong> <?php echo !empty($user['mothers_phone_number']) ? htmlspecialchars($user['mothers_phone_number']) : 'Not filled in'; ?></p>

                <h4 class="mt-4 mb-2"><strong>Archery Details</strong></h4>
                <p><strong>MAREOS ID:</strong> <?php echo !empty($user['mareos_id']) ? htmlspecialchars($user['mareos_id']) : 'Not filled in'; ?></p>
                <p><strong>WAREOS ID:</strong> <?php echo !empty($user['wareos_id']) ? htmlspecialchars($user['wareos_id']) : 'Not filled in'; ?></p>
                <p><strong>Program:</strong> <?php echo !empty($user['program']) ? htmlspecialchars($user['program']) : 'Not filled in'; ?></p>
                <p><strong>Coach Name:</strong> <?php echo !empty($user['coach_name']) ? htmlspecialchars($user['coach_name']) : 'Not filled in'; ?></p>
                <p><strong>School:</strong> <?php echo !empty($user['school']) ? htmlspecialchars($user['school']) : 'Not filled in'; ?></p>
                <p><strong>T-Shirt Size:</strong> <?php echo !empty($user['t_shirt_size']) ? htmlspecialchars($user['t_shirt_size']) : 'Not filled in'; ?></p>
                <p><strong>Pant Size:</strong> <?php echo !empty($user['pant_size']) ? htmlspecialchars($user['pant_size']) : 'Not filled in'; ?></p>
                <p><strong>Shoe Size:</strong> <?php echo !empty($user['shoe_size']) ? htmlspecialchars($user['shoe_size']) : 'Not filled in'; ?></p>
                <p><strong>Medical Conditions:</strong> <?php echo !empty($user['medical_conditions']) ? htmlspecialchars($user['medical_conditions']) : 'Not filled in'; ?></p>
                <p><strong>Bow Type:</strong> <?php echo !empty($user['bow_type']) ? htmlspecialchars($user['bow_type']) : 'Not filled in'; ?></p>
                <p><strong>Started Archery:</strong> <?php echo !empty($user['started_archery']) ? htmlspecialchars($user['started_archery']) : 'Not filled in'; ?></p>
                <p><strong>Joined National Backup Squad:</strong> <?php echo !empty($user['joined_national_backup_squad']) ? htmlspecialchars($user['joined_national_backup_squad']) : 'Not filled in'; ?></p>
                <p><strong>Joined Podium Program:</strong> <?php echo !empty($user['joined_podium_program']) ? htmlspecialchars($user['joined_podium_program']) : 'Not filled in'; ?></p>
                <p><strong>Arrow Type:</strong> <?php echo !empty($user['arrow_type']) ? htmlspecialchars($user['arrow_type']) : 'Not filled in'; ?></p>
                <p><strong>Arrow Size:</strong> <?php echo !empty($user['arrow_size']) ? htmlspecialchars($user['arrow_size']) : 'Not filled in'; ?></p>
                <p><strong>Arrow Length:</strong> <?php echo !empty($user['arrow_length']) ? htmlspecialchars($user['arrow_length']) : 'Not filled in'; ?></p>
                <p><strong>Limbs Type:</strong> <?php echo !empty($user['limbs_type']) ? htmlspecialchars($user['limbs_type']) : 'Not filled in'; ?></p>
                <p><strong>Limbs Length:</strong> <?php echo !empty($user['limbs_length']) ? htmlspecialchars($user['limbs_length']) : 'Not filled in'; ?></p>
                <p><strong>Limbs Weight:</strong> <?php echo !empty($user['limbs_weight']) ? htmlspecialchars($user['limbs_weight']) : 'Not filled in'; ?></p>
                <p><strong>Clicking Poundage:</strong> <?php echo !empty($user['clicking_poundage']) ? htmlspecialchars($user['clicking_poundage']) : 'Not filled in'; ?></p>
                <p><strong>Personal Best Before:</strong> <?php echo !empty($user['personal_best_before']) ? htmlspecialchars($user['personal_best_before']) : 'Not filled in'; ?></p>
                <p><strong>Current Personal Best:</strong> <?php echo !empty($user['current_personal_best']) ? htmlspecialchars($user['current_personal_best']) : 'Not filled in'; ?></p>

                <h4 class="mt-4 mb-2"><strong>Performance KPI</strong></h4>
                <p><strong>KPI (72 Arrows):</strong> <?php echo !empty($user['kpi_72_arrows']) ? htmlspecialchars($user['kpi_72_arrows']) : 'Not filled in'; ?></p>
                <p><strong>KPI Average Per Arrow:</strong> <?php echo !empty($user['kpi_avg_per_arrow']) ? htmlspecialchars($user['kpi_avg_per_arrow']) : 'Not filled in'; ?></p>

                <h4 class="mt-4 mb-2"><strong>Physical Measurements</strong></h4>
                <p><strong>Height:</strong> <?php echo !empty($user['height']) ? htmlspecialchars($user['height']) . ' cm' : 'Not filled in'; ?></p>
                <p><strong>Weight:</strong> <?php echo !empty($user['weight']) ? htmlspecialchars($user['weight']) . ' kg' : 'Not filled in'; ?></p>
                <p><strong>BMI:</strong> <?php echo !empty($user['bmi']) ? htmlspecialchars($user['bmi']) : 'Not filled in'; ?></p>
                <p><strong>Seat Reach (cm):</strong> <?php echo !empty($user['seat_reach_cm']) ? htmlspecialchars($user['seat_reach_cm']) : 'Not filled in'; ?></p>
                <p><strong>Handgrip (kg):</strong> <?php echo !empty($user['handgrip_kg']) ? htmlspecialchars($user['handgrip_kg']) : 'Not filled in'; ?></p>
                <p><strong>Shuttle Run (sec):</strong> <?php echo !empty($user['shuttle_run_sec']) ? htmlspecialchars($user['shuttle_run_sec']) : 'Not filled in'; ?></p>
                <p><strong>Shoulder Rotation:</strong> <?php echo !empty($user['shoulder_rotation']) ? htmlspecialchars($user['shoulder_rotation']) : 'Not filled in'; ?></p>
                <p><strong>Sit-Ups (30 sec):</strong> <?php echo !empty($user['sit_up_30_sec']) ? htmlspecialchars($user['sit_up_30_sec']) : 'Not filled in'; ?></p>
                <p><strong>Push-Ups (30 sec):</strong> <?php echo !empty($user['push_up_30_sec']) ? htmlspecialchars($user['push_up_30_sec']) : 'Not filled in'; ?></p>
                <p><strong>Broad Jump (cm):</strong> <?php echo !empty($user['broad_jump_cm']) ? htmlspecialchars($user['broad_jump_cm']) : 'Not filled in'; ?></p>
                <p><strong>Counter Movement Jump (cm):</strong> <?php echo !empty($user['counter_movement_jump_cm']) ? htmlspecialchars($user['counter_movement_jump_cm']) : 'Not filled in'; ?></p>
                <p><strong>Sprint Test (10/30m sec):</strong> <?php echo !empty($user['sprint_test_10_30m_sec']) ? htmlspecialchars($user['sprint_test_10_30m_sec']) : 'Not filled in'; ?></p>
                <p><strong>KTK Jumping Sideways (sec):</strong> <?php echo !empty($user['ktk_jumping_sideway_sec']) ? htmlspecialchars($user['ktk_jumping_sideway_sec']) : 'Not filled in'; ?></p>
                <p><strong>KTK Moving Sideways:</strong> <?php echo !empty($user['kt_moving_sideway']) ? htmlspecialchars($user['kt_moving_sideway']) : 'Not filled in'; ?></p>
                <p><strong>KTK Walking Backward:</strong> <?php echo !empty($user['ktk_walking_backward']) ? htmlspecialchars($user['ktk_walking_backward']) : 'Not filled in'; ?></p>
                <p><strong>Bleep Test:</strong> <?php echo !empty($user['bleep_test']) ? htmlspecialchars($user['bleep_test']) : 'Not filled in'; ?></p>
            <?php else: ?>
                <p class="text-danger-emphasis"><strong>No archery details available for this user.</strong></p>
            <?php endif; ?>
        </div>
    </div>
</div>