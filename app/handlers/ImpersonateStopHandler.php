<?php
ob_start();
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure impersonation is active
if (isset($_SESSION['impersonating']) && $_SESSION['impersonating'] === true) {
    // Restore the coach's session
    $_SESSION = $_SESSION['coach_backup'];
    unset($_SESSION['coach_backup']);
    unset($_SESSION['impersonating']);

    // Redirect back to coach's dashboard
    header('Location: ' . BASE_URL . 'app/views/users/coach/manage-athletes.php');
    exit();
}
