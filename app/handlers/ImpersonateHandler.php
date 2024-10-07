<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'coach')) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

if (isset($_GET['impersonate'])) {
    $impersonatedUserId = $_GET['impersonate'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->execute([$impersonatedUserId]);
    $impersonatedUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($impersonatedUser) {
        // Save the current admin or coach session data to restore later
        $_SESSION['original_user_id'] = $_SESSION['user_id'];
        $_SESSION['original_username'] = $_SESSION['username'];
        $_SESSION['original_role'] = $_SESSION['role'];
        $_SESSION['impersonating'] = true;  

        // Impersonate the new user by setting their details in the session
        $_SESSION['user_id'] = $impersonatedUser['user_id'];
        $_SESSION['username'] = $impersonatedUser['username'];
        $_SESSION['role'] = $impersonatedUser['role'];

        header('Location: ' . BASE_URL . 'app/views/users/' . $impersonatedUser['role'] . '/index.php');
        exit;
    } else {
        $_SESSION['error'] = 'User not found.';
        header('Location: ' . BASE_URL . 'app/views/users/admin/users.php');
        exit;
    }
}

