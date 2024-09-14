<?php
ob_start();
require_once __DIR__ . '/../../config/config.php';

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}