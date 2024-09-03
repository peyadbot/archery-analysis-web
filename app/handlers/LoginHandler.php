<?php
session_start();

require_once __DIR__ . '/../../config/config.php';

// Redirect to the appropriate page if user already log in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

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
                header('Location: '. BASE_URL . 'app/views/users/admin/index.php');
                break;
            case 'coach':
                header('Location: '. BASE_URL . 'app/views/users/coach/index.php');
                break;
            case 'athlete':
                header('Location: '. BASE_URL . 'app/views/users/athlete/index.php');
                break;
            default:
                header('Location: ' . BASE_URL . 'app/views/auth/login.php');
                break;
        }
        exit();
    } else {
        $error = 'Invalid credentials';
    }
}