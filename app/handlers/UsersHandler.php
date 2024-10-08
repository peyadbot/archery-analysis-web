<?php
require_once __DIR__ . '/../../config/config.php'; 

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$success = $error = '';
$editMode = false;
$editUser = null;

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (isset($_POST['user_id'])) {
        // Update existing user
        $user_id = $_POST['user_id'];
        $new_password = $_POST['new_password'] ?? '';
        $confirm_new_password = $_POST['confirm_new_password'] ?? '';

        // Check if the username is already taken (except for the current user)
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND user_id != ?');
        $stmt->execute([$username, $user_id]);

        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username already taken by another user.';
        } else {
            try {
                // Update username and role
                $stmt = $pdo->prepare('UPDATE users SET username = ?, role = ? WHERE user_id = ?');
                $stmt->execute([$username, $role, $user_id]);

                // If password fields are filled in, update password
                if (!empty($new_password) && !empty($confirm_new_password)) {
                    if ($new_password !== $confirm_new_password) {
                        $_SESSION['error'] = 'New passwords do not match.';
                    } elseif (strlen($new_password) < 8) {
                        $_SESSION['error'] = 'Password must be at least 8 characters long.';
                    } else {
                        // Update the password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
                        $stmt->execute([$hashed_password, $user_id]);
                        $_SESSION['success'] = 'Password updated successfully!';
                    }
                }

                $_SESSION['success'] = 'User updated successfully!';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Failed to update user: ' . $e->getMessage();
            }
        }
    } else {
        // Insert new user
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Password validation
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match.';
        } elseif (strlen($password) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters long.';
        } else {
            // Check if username already exists
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Username already exists.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
                    $stmt->execute([$username, $hashed_password, $role]);
                    $_SESSION['success'] = 'User added successfully!';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Failed to add user: ' . $e->getMessage();
                }
            }
        }
    }

    // Redirect to avoid form resubmission
    header('Location: ' . BASE_URL . 'app/views/users/admin/manageUsers.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $_SESSION['success'] = 'User deleted successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete user: ' . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'app/views/users/admin/manageUsers.php');
    exit;
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $editUser = $stmt->fetch();

    if ($editUser) {
        $editMode = true;
    }
}

// Fetch all users
$stmt = $pdo->prepare('SELECT * FROM users');
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin'));
