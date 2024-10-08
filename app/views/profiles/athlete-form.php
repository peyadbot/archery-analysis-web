<?php
require_once __DIR__ . '/../../handlers/AthleteDetailHandler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athlete Details Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

<div class="container mt-5">
    <h2 class="mb-4">Athlete Details Form</h2>
    <hr class="solid mt-2 mb-5">

    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Hidden input for profile_id -->
        <input type="hidden" name="profile_id" value="<?php echo isset($athlete['profile_id']) ? htmlspecialchars($athlete['profile_id']) : ''; ?>">

        <!-- Athlete Details -->
        <div class="form-section">
            <h4 class="mb-3">Athlete Details</h4>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mareos_id" class="form-label">MAREOS ID</label>
                        <input type="text" name="mareos_id" class="form-control" id="mareos_id" value="<?php echo isset($athlete['mareos_id']) ? htmlspecialchars($athlete['mareos_id']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="wareos_id" class="form-label">WAREOS ID</label>
                        <input type="text" name="wareos_id" class="form-control" id="wareos_id" value="<?php echo isset($athlete['wareos_id']) ? htmlspecialchars($athlete['wareos_id']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="program" class="form-label">Program</label>
                        <select name="program" class="form-select" id="program" >
                            <option value="">Select a Program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?php echo htmlspecialchars($program['program_id']); ?>"
                                    <?php echo (isset($athlete['program']) && $athlete['program'] == $program['program_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($program['program_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="started_archery" class="form-label">Started Archery</label>
                        <input type="date" name="started_archery" class="form-control" id="started_archery" value="<?php echo isset($athlete['started_archery']) ? htmlspecialchars($athlete['started_archery']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="joined_national_backup_squad" class="form-label">Joined National Backup Squad</label>
                        <input type="date" name="joined_national_backup_squad" class="form-control" id="joined_national_backup_squad" value="<?php echo isset($athlete['joined_national_backup_squad']) ? htmlspecialchars($athlete['joined_national_backup_squad']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="joined_podium_program" class="form-label">Joined Podium Program</label>
                        <input type="date" name="joined_podium_program" class="form-control" id="joined_podium_program" value="<?php echo isset($athlete['joined_podium_program']) ? htmlspecialchars($athlete['joined_podium_program']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="coach_name" class="form-label">Coach's Name</label>
                        <input type="text" name="coach_name" class="form-control" id="coach_name" value="<?php echo isset($athlete['coach_name']) ? htmlspecialchars($athlete['coach_name']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="school" class="form-label">School</label>
                        <input type="text" name="school" class="form-control" id="school" value="<?php echo isset($athlete['school']) ? htmlspecialchars($athlete['school']) : ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fathers_name" class="form-label">Father's Name</label>
                        <input type="text" class="form-control" id="fathers_name" name="fathers_name" value="<?php echo htmlspecialchars($athlete['fathers_name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fathers_phone_number" class="form-label">Father's Phone Number</label>
                        <input type="tel" class="form-control" id="fathers_phone_number" name="fathers_phone_number" value="<?php echo htmlspecialchars($athlete['fathers_phone_number'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mothers_name" class="form-label">Mother's Name</label>
                        <input type="text" class="form-control" id="mothers_name" name="mothers_name" value="<?php echo htmlspecialchars($athlete['mothers_name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mothers_phone_number" class="form-label">Mother's Phone Number</label>
                        <input type="tel" class="form-control" id="mothers_phone_number" name="mothers_phone_number" value="<?php echo htmlspecialchars($athlete['mothers_phone_number'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="t_shirt_size" class="form-label">T-shirt Size</label>
                        <input type="text" class="form-control" id="t_shirt_size" name="t_shirt_size" value="<?php echo htmlspecialchars($athlete['t_shirt_size'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="pant_size" class="form-label">Pant Size</label>
                        <input type="text" class="form-control" id="pant_size" name="pant_size" value="<?php echo htmlspecialchars($athlete['pant_size'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="shoe_size" class="form-label">Shoe Size</label>
                        <input type="text" class="form-control" id="shoe_size" name="shoe_size" value="<?php echo htmlspecialchars($athlete['shoe_size'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="medical_conditions" class="form-label">Medical Conditions</label>
                        <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="3"><?php echo htmlspecialchars($athlete['medical_conditions'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            <hr class="solid mt-5 mb-5">

            <!-- Archery Background -->
            <h4 class="mt-4 mb-3">Archery Detail</h4>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bow_type" class="form-label">Bow Type</label>
                        <select name="bow_type" class="form-select" id="bow_type" required>
                            <option value="recurve" <?php echo isset($athlete['bow_type']) && $athlete['bow_type'] === 'recurve' ? 'selected' : ''; ?>>Recurve</option>
                            <option value="compound" <?php echo isset($athlete['bow_type']) && $athlete['bow_type'] === 'compound' ? 'selected' : ''; ?>>Compound</option>
                            <option value="barebow" <?php echo isset($athlete['bow_type']) && $athlete['bow_type'] === 'barebow' ? 'selected' : ''; ?>>Barebow</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="arrow_type" class="form-label">Arrow Type</label>
                        <input type="text" name="arrow_type" class="form-control" id="arrow_type" value="<?php echo isset($athlete['arrow_type']) ? htmlspecialchars($athlete['arrow_type']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="arrow_size" class="form-label">Arrow Size</label>
                        <input type="text" name="arrow_size" class="form-control" id="arrow_size" value="<?php echo isset($athlete['arrow_size']) ? htmlspecialchars($athlete['arrow_size']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="arrow_length" class="form-label">Arrow Length</label>
                        <input type="number" step="0.1" name="arrow_length" class="form-control" id="arrow_length" value="<?php echo isset($athlete['arrow_length']) ? htmlspecialchars($athlete['arrow_length']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="clicking_poundage" class="form-label">Clicking Poundage</label>
                        <input type="number" step="0.1" name="clicking_poundage" class="form-control" id="clicking_poundage" value="<?php echo isset($athlete['clicking_poundage']) ? htmlspecialchars($athlete['clicking_poundage']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="limbs_type" class="form-label">Limbs Type</label>
                        <input type="text" name="limbs_type" class="form-control" id="limbs_type" value="<?php echo isset($athlete['limbs_type']) ? htmlspecialchars($athlete['limbs_type']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="limbs_length" class="form-label">Limbs Length</label>
                        <input type="number" step="0.1" name="limbs_length" class="form-control" id="limbs_length" value="<?php echo isset($athlete['limbs_length']) ? htmlspecialchars($athlete['limbs_length']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="limbs_weight" class="form-label">Limbs Weight</label>
                        <input type="number" step="0.1" name="limbs_weight" class="form-control" id="limbs_weight" value="<?php echo isset($athlete['limbs_weight']) ? htmlspecialchars($athlete['limbs_weight']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="personal_best_before" class="form-label">Personal Best (Before)</label>
                        <input type="number" step="0.01" name="personal_best_before" class="form-control" id="personal_best_before" value="<?php echo isset($athlete['personal_best_before']) ? htmlspecialchars($athlete['personal_best_before']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="current_personal_best" class="form-label">Current Personal Best</label>
                        <input type="number" step="0.01" name="current_personal_best" class="form-control" id="current_personal_best" value="<?php echo isset($athlete['current_personal_best']) ? htmlspecialchars($athlete['current_personal_best']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kpi_72_arrows" class="form-label">KPI 72 Arrows</label>
                        <input type="number" step="0.01" name="kpi_72_arrows" class="form-control" id="kpi_72_arrows" value="<?php echo isset($athlete['kpi_72_arrows']) ? htmlspecialchars($athlete['kpi_72_arrows']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kpi_average_per_arrow" class="form-label">KPI Average Per Arrow</label>
                        <input type="number" step="0.01" name="kpi_average_per_arrow" class="form-control" id="kpi_average_per_arrow" value="<?php echo isset($athlete['kpi_average_per_arrow']) ? htmlspecialchars($athlete['kpi_average_per_arrow']) : ''; ?>">
                    </div>
                </div>
            </div>

            <hr class="solid mt-5 mb-5">
            <!-- Fitness Test Fields -->
            <h4 class="mt-4 mb-3">Fitness Test</h4>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="weight" class="form-label">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" id="weight" value="<?php echo isset($athlete['weight']) ? htmlspecialchars($athlete['weight']) : ''; ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="height" class="form-label">Height (cm)</label>
                        <input type="number" step="0.1" name="height" class="form-control" id="height" value="<?php echo isset($athlete['height']) ? htmlspecialchars($athlete['height']) : ''; ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bmi" class="form-label">BMI</label>
                        <input type="number" step="0.01" name="bmi" class="form-control" id="bmi" value="<?php echo isset($athlete['bmi']) ? htmlspecialchars($athlete['bmi']) : ''; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="seat_reach" class="form-label">Seat & Reach (cm)</label>
                        <input type="number" step="0.1" name="seat_reach" class="form-control" id="seat_reach" value="<?php echo isset($athlete['seat_reach']) ? htmlspecialchars($athlete['seat_reach']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="handgrip" class="form-label">Handgrip (kg)</label>
                        <input type="number" step="0.1" name="handgrip" class="form-control" id="handgrip" value="<?php echo isset($athlete['handgrip']) ? htmlspecialchars($athlete['handgrip']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="shuttle_run" class="form-label">10x5m Shuttle Run (sec)</label>
                        <input type="number" step="0.01" name="shuttle_run" class="form-control" id="shuttle_run" value="<?php echo isset($athlete['shuttle_run']) ? htmlspecialchars($athlete['shuttle_run']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="shoulder_rotation" class="form-label">Shoulder Rotation</label>
                        <input type="number" step="0.1" name="shoulder_rotation" class="form-control" id="shoulder_rotation" value="<?php echo isset($athlete['shoulder_rotation']) ? htmlspecialchars($athlete['shoulder_rotation']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="sit_up" class="form-label">30 Sec Sit Up</label>
                        <input type="number" name="sit_up" class="form-control" id="sit_up" value="<?php echo isset($athlete['sit_up']) ? htmlspecialchars($athlete['sit_up']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="push_up" class="form-label">30 Sec Push Up</label>
                        <input type="number" name="push_up" class="form-control" id="push_up" value="<?php echo isset($athlete['push_up']) ? htmlspecialchars($athlete['push_up']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="standing_broad_jump" class="form-label">Standing Broad Jump (cm)</label>
                        <input type="number" step="0.1" name="standing_broad_jump" class="form-control" id="standing_broad_jump" value="<?php echo isset($athlete['standing_broad_jump']) ? htmlspecialchars($athlete['standing_broad_jump']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="counter_movement_jump" class="form-label">Counter Movement Jump (cm)</label>
                        <input type="number" step="0.1" name="counter_movement_jump" class="form-control" id="counter_movement_jump" value="<?php echo isset($athlete['counter_movement_jump']) ? htmlspecialchars($athlete['counter_movement_jump']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="sprint_test" class="form-label">Sprint Test 10/30M (sec)</label>
                        <input type="number" step="0.01" name="sprint_test" class="form-control" id="sprint_test" value="<?php echo isset($athlete['sprint_test']) ? htmlspecialchars($athlete['sprint_test']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="ktk_jumping_sideway" class="form-label">KTK-Jumping Sideway 15 Sec</label>
                        <input type="number" step="0.1" name="ktk_jumping_sideway" class="form-control" id="ktk_jumping_sideway" value="<?php echo isset($athlete['ktk_jumping_sideway']) ? htmlspecialchars($athlete['ktk_jumping_sideway']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="kt_moving_sideway" class="form-label">KT-Moving Sideway</label>
                        <input type="number" step="0.1" name="kt_moving_sideway" class="form-control" id="kt_moving_sideway" value="<?php echo isset($athlete['kt_moving_sideway']) ? htmlspecialchars($athlete['kt_moving_sideway']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="ktk_walking_backward" class="form-label">KTK-Walking Backward</label>
                        <input type="number" step="0.1" name="ktk_walking_backward" class="form-control" id="ktk_walking_backward" value="<?php echo isset($athlete['ktk_walking_backward']) ? htmlspecialchars($athlete['ktk_walking_backward']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bleep_test" class="form-label">Bleep Test</label>
                        <input type="number" step="0.1" name="bleep_test" class="form-control" id="bleep_test" value="<?php echo isset($athlete['bleep_test']) ? htmlspecialchars($athlete['bleep_test']) : ''; ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mt-4 mb-5">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

    <script>
        // Calculate BMI
        function calculateBMI() {
            var weight = document.getElementById('weight').value;
            var height = document.getElementById('height').value;

            if (height > 0) {
                height = height / 100;
            }

            if (weight > 0 && height > 0) {
                var bmi = weight / (height * height);
                document.getElementById('bmi').value = bmi.toFixed(2); 
            } else {
                document.getElementById('bmi').value = ''; 
            }
        }

        document.getElementById('weight').addEventListener('input', calculateBMI);
        document.getElementById('height').addEventListener('input', calculateBMI);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
