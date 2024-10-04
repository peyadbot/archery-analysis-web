<?php
ob_start();
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure the user is logged in and is a coach
if ($_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $athlete_user_id = $_POST['athlete_user_id'];

    // Retrieve the athlete's session data
    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = :athlete_user_id');
        $stmt->bindParam(':athlete_user_id', $athlete_user_id);
        $stmt->execute();
        $athlete = $stmt->fetch();

        if ($athlete) {
            // Backup the coach's current session
            $_SESSION['coach_backup'] = $_SESSION;

            // Set the session to impersonate the athlete
            $_SESSION['user_id'] = $athlete['user_id'];
            $_SESSION['username'] = $athlete['username'];
            $_SESSION['role'] = 'athlete'; // Ensures correct role is set
            $_SESSION['impersonating'] = true;

            // Redirect to the athlete's dashboard
            header('Location: ' . BASE_URL . 'app/views/users/athlete/index.php');
            exit();
        } else {
            $_SESSION['error'] = 'Athlete not found.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error accessing athlete account: ' . $e->getMessage();
    }

    // Redirect back to the coach's dashboard in case of error
    header('Location: ' . BASE_URL . 'app/views/users/coach/manage-athletes.php');
    exit();
}