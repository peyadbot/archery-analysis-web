<?php
require_once __DIR__ . '/../../../handlers/ProfileHandler.php';

// Ensure $profile is set
if (!isset($profile)) {
    // Handle the case where $profile is not set or not loaded properly
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
</head>
<body>
    <div class="mt-3">
        <a href="javascript:history.back()" class="btn btn-secondary">Previous Page</a>
    </div>
    <div class="container mt-5">
        <h1 class="text-center mb-4">My Profile</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo htmlspecialchars($profile['first_name'] ?? 'First Name') . ' ' . htmlspecialchars($profile['last_name'] ?? 'Last Name'); ?>
                </h5>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($profile['email'] ?? 'No email provided'); ?></p>
                <p class="card-text"><strong>Phone Number:</strong> <?php echo htmlspecialchars($profile['phone_number'] ?? 'No phone number provided'); ?></p>

                <?php if (!empty($profile['profile_picture'])): ?>
                    <img src="<?php echo BASE_URL . 'public/images/user_img/' . htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail" style="max-width: 150px;">
                <?php else: ?>
                    <p>No profile picture uploaded.</p>
                <?php endif; ?>

                <p class="card-text"><strong>IC Number:</strong> <?php echo htmlspecialchars($profile['ic_number'] ?? 'No IC number provided'); ?></p>
                <p class="card-text"><strong>Passport Number:</strong> <?php echo htmlspecialchars($profile['passport_number'] ?? 'No passport number provided'); ?></p>
                <p class="card-text"><strong>State:</strong> <?php echo htmlspecialchars($profile['state'] ?? 'No state provided'); ?></p>

                <a href="profile-form.php" class="btn btn-primary">Update Profile</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
