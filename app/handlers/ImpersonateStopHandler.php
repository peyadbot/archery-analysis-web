<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['impersonating']) || !$_SESSION['impersonating']) {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit;
}
// Handle restore request to stop impersonation and go back to the original admin/coach account
if (isset($_SESSION['original_user_id'])) {
    // Restore the original admin/coach session data
    $_SESSION['user_id'] = $_SESSION['original_user_id'];
    $_SESSION['username'] = $_SESSION['original_username'];
    $_SESSION['role'] = $_SESSION['original_role'];

    // Clear impersonation session data
    unset($_SESSION['original_user_id'], $_SESSION['original_username'], $_SESSION['original_role'], $_SESSION['impersonating']);

    // Redirect back to the admin or coach dashboard
    header('Location: ' . BASE_URL . 'app/views/users/' . $_SESSION['role'] . '/index.php');
    exit;
} else {
    $_SESSION['error'] = 'No impersonation session found.';
    header('Location: ' . BASE_URL . 'app/views/users/admin/users.php');
    exit;
}

