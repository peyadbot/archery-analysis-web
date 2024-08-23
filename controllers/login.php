<?php
session_start();

// Redirect to the appropriate page if user already log in
if (isset($_SESSION['user_id'])) {
    header("Location: /archery-analysis-web/index.php");
    exit();
}

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header('Location: /archery-analysis-web/views/admin/dashboard.php');
                break;
            case 'coach':
                header('Location: /archery-analysis-web/views/coach/dashboard.php');
                break;
            default:
                header('Location: login.php');
                break;
        }
        exit();
    } else {
        $error = 'Invalid credentials';
    }
}
