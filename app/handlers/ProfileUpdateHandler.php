<!-- Use in edit and update profile -->

<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and is either athlete or coach
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['athlete', 'coach'])) {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
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