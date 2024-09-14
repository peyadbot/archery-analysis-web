<?php
ob_start();
// session_start();
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

    // File uploads
    $upload_dir = __DIR__ . '/../../public/images/user_img/';
    $profile_picture = $profile['profile_picture'] ?? '';
    $ic_file = $profile['ic_file'] ?? '';
    $passport_file = $profile['passport_file'] ?? '';

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture = basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $profile_picture);
    }

    if (isset($_FILES['ic_file']) && $_FILES['ic_file']['error'] === UPLOAD_ERR_OK) {
        $ic_file = basename($_FILES['ic_file']['name']);
        move_uploaded_file($_FILES['ic_file']['tmp_name'], $upload_dir . $ic_file);
    }

    if (isset($_FILES['passport_file']) && $_FILES['passport_file']['error'] === UPLOAD_ERR_OK) {
        $passport_file = basename($_FILES['passport_file']['name']);
        move_uploaded_file($_FILES['passport_file']['tmp_name'], $upload_dir . $passport_file);
    }

    // Insert or update user profile based on whether it exists
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
                    passport_file = ?
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
                INSERT INTO profiles (name, date_of_birth, phone_number, email, ic_number, passport_number, passport_expiry_date, passport_issue_place, home_address, profile_picture, ic_file, passport_file, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
                $user_id
            ]);
            $_SESSION['success'] = 'Profile created successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to create profile: ' . $e->getMessage();
        }
    }


    // Redirect to avoid form resubmission
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

