<?php
// session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SessionExpiryHandler.php';
require_once __DIR__ . '/LogoutHandler.php';
checkSessionTimeout();

// Ensure user is logged in and is either athlete, coach, or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['athlete', 'coach', 'admin'])) {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Fetch user_id from the session
$role = $_SESSION['role']; // Fetch user role

// Fetch current user data for further use
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = 'User not found';
    header('Location: ' . BASE_URL . 'public/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Update Credentials
    if (isset($_POST['update_credentials'])) {
        // Get the form data
        $current_password = trim($_POST['current_password']);
        $new_username = trim($_POST['username']);
        $new_password = trim($_POST['password']); // Password can be left empty for username-only updates

        // Validate current password
        if (password_verify($current_password, $user['password'])) {
            try {
                // Update username
                $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE user_id = ?");
                $stmt->execute([$new_username, $user_id]);

                // If new password is provided, hash and update it
                if (!empty($new_password)) {
                    if (strlen($new_password) < 8) {
                        throw new Exception("New password must be at least 8 characters.");
                    }

                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                    $stmt->execute([$hashed_new_password, $user_id]);
                }

                $_SESSION['success'] = 'Username and password updated successfully!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Failed to update credentials: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'The current password you entered is incorrect.';
        }

        header('Location: profile.php'); // Redirect to prevent form resubmission
        exit();
    }

    // Handle Account Deletion
    if (isset($_POST['delete_account'])) {
        $delete_password = trim($_POST['delete_password']); // Password for account deletion

        // Verify the password before deleting the account
        if (password_verify($delete_password, $user['password'])) {
            try {
                // Delete associated profile if it exists
                $stmt = $pdo->prepare('DELETE FROM profiles WHERE user_id = ?');
                $stmt->execute([$user_id]);

                // Now delete the user record from the 'users' table
                $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
                $stmt->execute([$user_id]);

                // Destroy the session and redirect to the homepage
                session_destroy();
                header('Location: ' . BASE_URL . 'index.php');
                exit();
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Failed to delete account: ' . $e->getMessage();
                header('Location: profile.php');
                exit();
            }
        } else {
            // Password is incorrect
            $_SESSION['error'] = 'The password you entered is incorrect.';
            header('Location: profile.php');
            exit();
        }
    }
}

// Check user roles for display purposes
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';
$isCoach = $isLoggedIn && $_SESSION['role'] === 'coach';
$isAthlete = $isLoggedIn && $_SESSION['role'] === 'athlete';
$isAdminOrCoach = $isAdmin || $isCoach;

// Fetch user profile data from the database
$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($isAthlete) {
    $stmt = $pdo->prepare('SELECT * FROM athlete_details WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $athlete = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if profile is incomplete
$profile_incomplete = empty($profile['name']) || empty($profile['ic_number']) || empty($profile['email']) || empty($profile['phone_number']);
