<?php 
require_once __DIR__ . '/../../handlers/LogoutHandler.php';
require_once __DIR__ . '/../../../config/config.php';

// For a specific page css & title 
// (page css)    $page_specific_css = 'dashboard.css';
// (page title)  $title = 'Dashboard - Archery Stats';\
// ?php echo BASE_URL . 'public/css/' . htmlspecialchars($current_page_css); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Archery Stats'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/../../../public/css/userDashboard.php">
</head>

<style>
    html, body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
        overflow-x: hidden; /* Prevent horizontal scroll */
    }

    .container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px; /* Optional: Add some padding */
        box-sizing: border-box;
    }

    .mt-4 {
        margin-top: 1.5rem; /* Adjust margin as needed */
    }
</style>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid mx-3">
            <a class="navbar-brand" href="<?php echo BASE_URL . 'public/home.php'; ?>">
                <img src="https://github.com/mdo.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
                Archery Stats
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'home' || $current_page === '') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'public/home.php'; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Training</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Competition
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Local</a></li>
                            <li><a class="dropdown-item" href="#">International</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Athlete
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Compound</a></li>
                            <li><a class="dropdown-item" href="#">Barebow</a></li>
                            <li><a class="dropdown-item" href="#">Recurve</a></li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/profile.php'; ?>">Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL . 'views/users/' . htmlspecialchars($_SESSION['role']) . '/index.php'; ?>">Dashboard</a></li>
                                <li><a class="dropdown-item" href="#">Sign Out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL . 'app/views/auth/login.php'; ?>">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    

    <div class="container mt-4">
        <!-- Your content goes here -->
