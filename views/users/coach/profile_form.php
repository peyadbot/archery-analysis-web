<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';

// Ensure user is logged in and is an athlete
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile update
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $profile_picture = $_FILES['profile_picture'];
    $ic_number = $_POST['ic_number'];
    $passport_number = $_POST['passport_number'];
    $state = $_POST['state'];

    // Upload profile picture if provided
    if ($profile_picture['error'] === UPLOAD_ERR_OK) {
        $target_dir = __DIR__ . '/../../public/images/user_img/';
        $target_file = $target_dir . basename($profile_picture['name']);
        move_uploaded_file($profile_picture['tmp_name'], $target_file);
        $profile_picture_path = $profile_picture['name'];
    } else {
        $profile_picture_path = $profile['profile_picture'] ?? null;
    }

    // Update or insert profile data
    $stmt = $pdo->prepare('INSERT INTO profiles (user_id, first_name, last_name, email, phone_number, profile_picture, ic_number, passport_number, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name), email = VALUES(email), phone_number = VALUES(phone_number), profile_picture = VALUES(profile_picture), ic_number = VALUES(ic_number), passport_number = VALUES(passport_number), state = VALUES(state)');
    $stmt->execute([$user_id, $first_name, $last_name, $email, $phone_number, $profile_picture_path, $ic_number, $passport_number, $state]);

    header('Location: profile.php');
    exit();
}

// Fetch user profile data from the database
$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-label {
            font-weight: 500;
        }
        .img-thumbnail {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="profile-container">
            <h1 class="text-center mb-4"><?php echo $profile ? 'Edit Profile' : 'Update Profile'; ?></h1>
            <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-section mb-4">
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($profile['phone_number'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                        <?php if (!empty($profile['profile_picture'])): ?>
                            <img src="<?php echo '/public/images/user_img/' . htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail mt-2">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="ic_number" class="form-label">IC Number</label>
                        <input type="text" class="form-control" id="ic_number" name="ic_number" value="<?php echo htmlspecialchars($profile['ic_number'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="passport_number" class="form-label">Passport Number</label>
                        <input type="text" class="form-control" id="passport_number" name="passport_number" value="<?php echo htmlspecialchars($profile['passport_number'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>">
                    </div>
                </div>
            </div>
                <button type="submit" class="btn btn-primary w-100">Save Profile</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
