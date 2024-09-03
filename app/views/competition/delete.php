<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and has the role of coach or admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'coach' && $_SESSION['role'] !== 'admin')) {
    header('Location: ../login.php');
    exit();
}

// Check if ID is provided and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $competition_id = (int) $_GET['id'];

    // Prepare and execute the DELETE statement
    $stmt = $pdo->prepare('DELETE FROM competitions WHERE competition_id = ?');
    $stmt->execute([$competition_id]);

    // Set success message
    $success = 'Competition deleted successfully!';
    header('Location: index.php');
    exit();
} else {
    // Redirect or handle the error if ID is not valid
    header('Location: index.php');
    exit();
}
