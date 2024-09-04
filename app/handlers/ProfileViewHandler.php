<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/LogoutHandler.php';

// Ensure user is logged in and is either athlete or coach
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['athlete', 'coach', 'admin'])) {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch user profile data from the database
$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if profile is incomplete
$profile_incomplete = empty($profile['first_name']) || empty($profile['last_name']) || empty($profile['email']) || empty($profile['phone_number']);
