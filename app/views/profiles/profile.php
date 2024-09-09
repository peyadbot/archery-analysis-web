<?php
require_once __DIR__ . '/../../handlers/ProfileViewHandler.php';

// Ensure $profile is set
if (!isset($profile)) {
    echo "Profile information is not available.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .card-body {
            padding: 2rem;
        }

        .card img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .profile-section, .archery-section {
            margin-bottom: 2rem;
        }

        .container {
            max-width: 1200px;
        }
    </style>
</head>
<body>
    <div class="mt-3 text-center">
        <a href="javascript:history.back()" class="btn btn-secondary mb-4">Back</a>
    </div>
    
    <div class="container mt-5">
        <div class="row">
            <!-- Profile Section -->
            <div class="col-lg-6 profile-section">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2>My Profile</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($profile['profile_picture'])): ?>
                            <img src="<?php echo BASE_URL . 'public/images/user_img/' . htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail">
                        <?php else: ?>
                            <p>No profile picture uploaded.</p>
                        <?php endif; ?>

                        <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($profile['name'] ?? 'No name provided'); ?></p>
                        <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($profile['email'] ?? 'No email provided'); ?></p>
                        <p class="card-text"><strong>Phone Number:</strong> <?php echo htmlspecialchars($profile['phone_number'] ?? 'No phone number provided'); ?></p>
                        <p class="card-text"><strong>Date of Birth:</strong> <?php echo htmlspecialchars($profile['date_of_birth'] ?? 'No date of birth provided'); ?></p>
                        <p class="card-text"><strong>IC Number:</strong> <?php echo htmlspecialchars($profile['ic_number'] ?? 'No IC number provided'); ?></p>
                        <p class="card-text"><strong>Passport Number:</strong> <?php echo htmlspecialchars($profile['passport_number'] ?? 'No passport number provided'); ?></p>
                        <p class="card-text"><strong>Passport Expiry Date:</strong> <?php echo htmlspecialchars($profile['passport_expiry_date'] ?? 'No expiry date provided'); ?></p>
                        <p class="card-text"><strong>Passport Issue Place:</strong> <?php echo htmlspecialchars($profile['passport_issue_place'] ?? 'No passport issue place provided'); ?></p>
                        <p class="card-text"><strong>Home Address:</strong> <?php echo htmlspecialchars($profile['home_address'] ?? 'No address provided'); ?></p>
                        
                        <a href="profile-form.php" class="btn btn-primary mt-3">Update Profile</a>
                    </div>
                </div>
            </div>

            <!-- Archery Details Section -->
            <?php if ($isAthlete): ?>
                <div class="col-lg-6 archery-section">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h2>Archery Details</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($athlete): ?>
                                <!-- Display Archery Details -->
                                <p class="card-text"><strong>MAREOS ID:</strong> <?php echo htmlspecialchars($athlete['mareos_id'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>WAREOS ID:</strong> <?php echo htmlspecialchars($athlete['wareos_id'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Program:</strong> <?php echo htmlspecialchars($athlete['program'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Coach Name:</strong> <?php echo htmlspecialchars($athlete['coach_name'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>School:</strong> <?php echo htmlspecialchars($athlete['school'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>T-Shirt Size:</strong> <?php echo htmlspecialchars($athlete['t_shirt_size'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Pant Size:</strong> <?php echo htmlspecialchars($athlete['pant_size'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Shoe Size:</strong> <?php echo htmlspecialchars($athlete['shoe_size'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Medical Conditions:</strong> <?php echo htmlspecialchars($athlete['medical_conditions'] ?? 'None'); ?></p>
                                
                                <h4 class="mt-4">Archery Equipment</h4>
                                <p class="card-text"><strong>Bow Type:</strong> <?php echo htmlspecialchars($athlete['bow_type'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Arrow Type:</strong> <?php echo htmlspecialchars($athlete['arrow_type'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Arrow Length:</strong> <?php echo htmlspecialchars($athlete['arrow_length'] ?? 'Not provided'); ?> cm</p>
                                <p class="card-text"><strong>Limbs Weight:</strong> <?php echo htmlspecialchars($athlete['limbs_weight'] ?? 'Not provided'); ?> kg</p>

                                <h4 class="mt-4">Performance Metrics</h4>
                                <p class="card-text"><strong>Personal Best Before:</strong> <?php echo htmlspecialchars($athlete['personal_best_before'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>Current Personal Best:</strong> <?php echo htmlspecialchars($athlete['current_personal_best'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>KPI (72 Arrows):</strong> <?php echo htmlspecialchars($athlete['kpi_72_arrows'] ?? 'Not provided'); ?></p>
                                <p class="card-text"><strong>KPI Average Per Arrow:</strong> <?php echo htmlspecialchars($athlete['kpi_avg_per_arrow'] ?? 'Not provided'); ?></p>
                                
                                <a href="athlete-form.php" class="btn btn-primary mt-3">Update Details</a>
                            <?php else: ?>
                                <p>No archery details found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
