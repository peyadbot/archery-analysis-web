<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and is a coach or admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'coach' && $_SESSION['role'] !== 'admin')) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $training_id = $_GET['id'];

    $stmt = $pdo->prepare('DELETE FROM trainings WHERE training_id = ?');
    $stmt->execute([$training_id]);

    header('Location: index.php');
    exit();
}
