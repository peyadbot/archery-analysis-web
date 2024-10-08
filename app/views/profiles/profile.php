<?php
require_once __DIR__ . '/../../handlers/ProfileViewHandler.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

if (!isset($profile)) {
    echo "Profile information is not available.";
    exit;
}

$user_id = $_SESSION['user_id'];
$user = null;

$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>

    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="text-start mb-4">
            <a href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/index.php'; ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </div>

        <?php if ($isAthlete || $isCoach): ?>
            <div class="row">            
                <!-- Profile Section -->
                <div class="col-lg-12 profile-section mb-4">

                    <!-- Success and Error Messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success'];
                                                                                                unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error'];
                                                                                unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card shadow-sm border-dark">
                        <div class="card-header bg-dark text-white">
                            <h2>My Profile</h2>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($profile['profile_picture'])): ?>
                                <img src="<?php echo BASE_URL . 'public/images/profile_picture/' . htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail" style="width: 120px; height: 160px; object-fit: cover;">
                            <?php else: ?>
                                <p>No profile picture uploaded.</p>
                            <?php endif; ?>

                            <p class="card-text"><strong>Name:</strong> <?php echo !empty($profile['name']) ? htmlspecialchars($profile['name']) : 'No name provided'; ?></p>
                            <p class="card-text"><strong>Email:</strong> <?php echo !empty($profile['email']) ? htmlspecialchars($profile['email']) : 'No email provided'; ?></p>
                            <p class="card-text"><strong>Phone Number:</strong> <?php echo !empty($profile['phone_number']) ? htmlspecialchars($profile['phone_number']) : 'No phone number provided'; ?></p>
                            <p class="card-text"><strong>Date of Birth:</strong> <?php echo !empty($profile['date_of_birth']) ? htmlspecialchars($profile['date_of_birth']) : 'No date of birth provided'; ?></p>
                            <p class="card-text"><strong>IC Number:</strong> <?php echo !empty($profile['ic_number']) ? htmlspecialchars($profile['ic_number']) : 'No IC number provided'; ?></p>
                            <p class="card-text"><strong>Passport Number:</strong> <?php echo !empty($profile['passport_number']) ? htmlspecialchars($profile['passport_number']) : 'No passport number provided'; ?></p>
                            <p class="card-text"><strong>Passport Expiry Date:</strong> <?php echo !empty($profile['passport_expiry_date']) ? htmlspecialchars($profile['passport_expiry_date']) : 'No expiry date provided'; ?></p>
                            <p class="card-text"><strong>Passport Issue Place:</strong> <?php echo !empty($profile['passport_issue_place']) ? htmlspecialchars($profile['passport_issue_place']) : 'No passport issue place provided'; ?></p>
                            <p class="card-text"><strong>Home Address:</strong> <?php echo !empty($profile['home_address']) ? htmlspecialchars($profile['home_address']) : 'No address provided'; ?></p>

                            <a href="profile-form.php" class="btn btn-primary mt-3">Update Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <!-- Archery Details Section -->
        <?php if ($isAthlete): ?>
            <div class="row">
                <div class="col-lg-12 archery-section mb-4">
                    <div class="card shadow-sm border-dark">
                        <div class="card-header bg-dark text-white">
                            <h2>Archery Details</h2>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><strong>MAREOS ID:</strong> <?php echo !empty($athlete['mareos_id']) ? htmlspecialchars($athlete['mareos_id']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>WAREOS ID:</strong> <?php echo !empty($athlete['wareos_id']) ? htmlspecialchars($athlete['wareos_id']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>Program:</strong> <?php echo !empty($athlete['program']) ? htmlspecialchars($athlete['program']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>Coach Name:</strong> <?php echo !empty($athlete['coach_name']) ? htmlspecialchars($athlete['coach_name']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>School:</strong> <?php echo !empty($athlete['school']) ? htmlspecialchars($athlete['school']) : 'Not provided'; ?></p>

                            <h4 class="mt-4">Archery Equipment</h4>
                            <p class="card-text"><strong>Bow Type:</strong> <?php echo !empty($athlete['bow_type']) ? htmlspecialchars($athlete['bow_type']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>Arrow Type:</strong> <?php echo !empty($athlete['arrow_type']) ? htmlspecialchars($athlete['arrow_type']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>Arrow Length:</strong> <?php echo !empty($athlete['arrow_length']) ? htmlspecialchars($athlete['arrow_length']) : 'Not provided'; ?> cm</p>
                            <p class="card-text"><strong>Limbs Weight:</strong> <?php echo !empty($athlete['limbs_weight']) ? htmlspecialchars($athlete['limbs_weight']) : 'Not provided'; ?> kg</p>

                            <h4 class="mt-4">Performance Metrics</h4>
                            <p class="card-text"><strong>Personal Best Before:</strong> <?php echo !empty($athlete['personal_best_before']) ? htmlspecialchars($athlete['personal_best_before']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>Current Personal Best:</strong> <?php echo !empty($athlete['current_personal_best']) ? htmlspecialchars($athlete['current_personal_best']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>KPI (72 Arrows):</strong> <?php echo !empty($athlete['kpi_72_arrows']) ? htmlspecialchars($athlete['kpi_72_arrows']) : 'Not provided'; ?></p>
                            <p class="card-text"><strong>KPI Average Per Arrow:</strong> <?php echo !empty($athlete['kpi_avg_per_arrow']) ? htmlspecialchars($athlete['kpi_avg_per_arrow']) : 'Not provided'; ?></p>

                            <a href="athlete-form.php" class="btn btn-primary mt-3">Update Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
        
        <!-- Danger Zone Section (Username/Password & Account Deletion) -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h2>Security Settings</h2>
                    </div>
                    <div class="card-body">

                        <!-- Update Username & Password Form -->
                        <form action="" method="POST">
                            <h4 class="text-danger">Danger Zone</h4>

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>

                            <!-- Update Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required value="<?php echo htmlspecialchars($user['username']); ?>">
                            </div>

                            <!-- New Password with Strength Meter -->
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" name="password" id="password-input">
                                <div id="password-strength" class="form-text">Password strength: <span id="strength-value"></span></div>
                            </div>

                            <!-- Update Button -->
                            <button type="submit" name="update_credentials" class="btn btn-warning">Update Username & Password</button>
                        </form>

                        <!-- Account Deletion Form -->
                        <div class="danger-zone mt-4">
                            <h4 class="danger-heading text-danger">Delete Account</h4>
                            <p class="text-danger">This action is irreversible. Your account and all data will be permanently deleted.</p>

                            <!-- Trigger for the Modal -->
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete My Account</button>

                            <!-- Delete Account Modal -->
                            <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteAccountLabel">Confirm Account Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to permanently delete your account? This action cannot be undone.</p>

                                            <!-- Account Deletion Form -->
                                            <form action="" method="POST">
                                                <!-- Checkbox Confirmation -->
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" id="delete-confirm-checkbox">
                                                    <label class="form-check-label" for="delete-confirm-checkbox">I understand the consequences</label>
                                                </div>

                                                <!-- Password Confirmation -->
                                                <div class="mb-3">
                                                    <label for="delete_password" class="form-label">Enter your password to confirm:</label>
                                                    <input type="password" class="form-control" id="delete_password" name="delete_password" required>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <!-- Disable delete button unless checkbox is checked -->
                                                    <button type="submit" name="delete_account" id="delete-button" class="btn btn-danger" disabled>Delete My Account</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript for Password Strength Meter & Confirmation -->
    <script>
        setTimeout(function() {
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 5000);

        const passwordInput = document.getElementById('password-input');
        const strengthValue = document.getElementById('strength-value');
        const strengthMeter = {
            0: 'Weak',
            1: 'Fair',
            2: 'Good',
            3: 'Strong'
        };

        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[\W]/.test(val)) score++;
            strengthValue.textContent = strengthMeter[score] || 'Weak';
        });

        // Account Deletion Confirmation
        const deleteCheckbox = document.getElementById('delete-confirm-checkbox');
        const deleteButton = document.getElementById('delete-button');
        deleteCheckbox.addEventListener('change', function() {
            deleteButton.disabled = !deleteCheckbox.checked;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>