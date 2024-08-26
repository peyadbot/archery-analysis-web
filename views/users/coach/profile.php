<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';

// Ensure user is logged in and is an athlete
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile data from the database
$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if profile data was found
if (!$profile) {
    echo 'Profile not found.';
    exit();
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
    <div class="container mt-5">
        <h1 class="text-center mb-4">My Profile</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($profile['first_name']) . ' ' . htmlspecialchars($profile['last_name']); ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                <p class="card-text"><strong>Phone Number:</strong> <?php echo htmlspecialchars($profile['phone_number']); ?></p>
                <?php if ($profile['profile_picture']): ?>
                    <img src="<?php echo '/public/images/user_img/' . htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail" style="max-width: 150px;">
                <?php else: ?>
                    <p>No profile picture uploaded.</p>
                <?php endif; ?>
                <p class="card-text"><strong>IC Number:</strong> <?php echo htmlspecialchars($profile['ic_number']); ?></p>
                <p class="card-text"><strong>Passport Number:</strong> <?php echo htmlspecialchars($profile['passport_number']); ?></p>
                <p class="card-text"><strong>State:</strong> <?php echo htmlspecialchars($profile['state']); ?></p>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
