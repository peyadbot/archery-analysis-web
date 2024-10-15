<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SessionExpiryHandler.php';
checkSessionTimeout();

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = '';
$editMode = false;
$profile = null;

// Fetch current user profile if it's an edit mode or simply viewing profile
$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Create and Update
    $name = htmlspecialchars(trim($_POST['name']));
    $date_of_birth = htmlspecialchars(trim($_POST['date_of_birth']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $ic_number = htmlspecialchars(trim($_POST['ic_number']));
    $passport_number = htmlspecialchars(trim($_POST['passport_number']));
    $passport_expiry_date = htmlspecialchars(trim($_POST['passport_expiry_date']));
    $passport_issue_place = htmlspecialchars(trim($_POST['passport_issue_place']));
    $home_address = htmlspecialchars(trim($_POST['home_address']));
    $mareos_id = htmlspecialchars(trim($_POST['mareos_id']));
    $wareos_id = htmlspecialchars(trim($_POST['wareos_id']));

    // File uploads directories
    $profile_pic_dir = __DIR__ . '/../../public/images/profile_picture/';
    $ic_file_dir = __DIR__ . '/../../public/images/ic_file/';
    $passport_file_dir = __DIR__ . '/../../public/images/passport_file/';

    // Ensure directories exist
    if (!is_dir($profile_pic_dir)) mkdir($profile_pic_dir, 0777, true);
    if (!is_dir($ic_file_dir)) mkdir($ic_file_dir, 0777, true);
    if (!is_dir($passport_file_dir)) mkdir($passport_file_dir, 0777, true);

    // File variables
    $profile_picture = $profile['profile_picture'] ?? '';
    $ic_file = $profile['ic_file'] ?? '';
    $passport_file = $profile['passport_file'] ?? '';

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture_name = basename($_FILES['profile_picture']['name']);
        $profile_picture_path = $profile_pic_dir . $profile_picture_name;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_path);
        $profile_picture = $profile_picture_name;
    }

    // Handle IC file upload
    if (isset($_FILES['ic_file']) && $_FILES['ic_file']['error'] === UPLOAD_ERR_OK) {
        $ic_file_name = basename($_FILES['ic_file']['name']);
        $ic_file_path = $ic_file_dir . $ic_file_name;
        move_uploaded_file($_FILES['ic_file']['tmp_name'], $ic_file_path);
        $ic_file = $ic_file_name;
    }

    // Handle passport file upload
    if (isset($_FILES['passport_file']) && $_FILES['passport_file']['error'] === UPLOAD_ERR_OK) {
        $passport_file_name = basename($_FILES['passport_file']['name']);
        $passport_file_path = $passport_file_dir . $passport_file_name;
        move_uploaded_file($_FILES['passport_file']['tmp_name'], $passport_file_path);
        $passport_file = $passport_file_name;
    }

    if ($profile) {
        // Update existing profile
        try {
            $stmt = $pdo->prepare("
                UPDATE profiles SET 
                    name = ?, 
                    date_of_birth = ?, 
                    phone_number = ?, 
                    email = ?, 
                    ic_number = ?, 
                    passport_number = ?, 
                    passport_expiry_date = ?, 
                    passport_issue_place = ?, 
                    home_address = ?, 
                    profile_picture = ?, 
                    ic_file = ?, 
                    passport_file = ?,
                    mareos_id = ?,
                    wareos_id = ?
                WHERE user_id = ?
            ");
            $stmt->execute([
                $name, 
                $date_of_birth, 
                $phone_number, 
                $email, 
                $ic_number, 
                $passport_number, 
                $passport_expiry_date, 
                $passport_issue_place, 
                $home_address, 
                $profile_picture, 
                $ic_file, 
                $passport_file,
                $mareos_id,
                $wareos_id,
                $user_id
            ]);
            $_SESSION['success'] = 'Profile updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update profile: ' . $e->getMessage();
        }
    } else {
        // Insert new profile (only if needed)
        try {
            $stmt = $pdo->prepare("
                INSERT INTO profiles (name, date_of_birth, phone_number, email, ic_number, passport_number, passport_expiry_date, passport_issue_place, home_address, profile_picture, ic_file, passport_file, mareos_id, wareos_id, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $name, 
                $date_of_birth, 
                $phone_number, 
                $email, 
                $ic_number, 
                $passport_number, 
                $passport_expiry_date, 
                $passport_issue_place, 
                $home_address, 
                $profile_picture, 
                $ic_file, 
                $passport_file,
                $mareos_id,
                $wareos_id,
                $user_id
            ]);
            $_SESSION['success'] = 'Profile created successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to create profile: ' . $e->getMessage();
        }
    }

    header('Location: profile.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM profiles WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $_SESSION['success'] = 'Profile deleted successfully!';
        session_destroy(); // Log the user out after deletion
        header('Location: profile.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete profile: ' . $e->getMessage();
    }
}

// Fetch updated profile data
$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

$isLoggedIn = isset($_SESSION['user_id']);