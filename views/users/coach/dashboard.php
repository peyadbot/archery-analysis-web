<?php
session_start();

require_once __DIR__ . '/../../auth/logout.php';
require_once __DIR__ . '/../../../config/config.php';

// Ensure user is logged in and is a coach
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Dashboard - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <!-- Sidebar menu -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" id="toggleSidebar"><i class="bi bi-list"></i></button>
        <div class="brand">
            <i class="bi bi-airplane-engines-fill"></i>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="archery_styles.php">
                    <i class="bi bi-person-plus"></i>
                    <span>Add Archery Style</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="competitions.php">
                    <i class="bi bi-calendar-plus"></i>
                    <span>Add Match</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="training_sessions.php">
                    <i class="bi bi-bar-chart"></i>
                    <span>View Statistics</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add_athlete.php">
                    <i class="bi bi-gear"></i>
                    <span>Manage Coaches</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="training_scores.php">
                    <i class="bi bi-shield"></i>
                    <span>Training Scores</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="competition_scores.php">
                    <i class="bi bi-trophy"></i>
                    <span>Competition Scores</span>
                </a>
            </li>
        </ul>
        <!-- User profile section -->
        <div class="user-profile">
            <img src="../../assets/user_img/fencing.jpg" alt="Profile Picture" class="profile-pic">
            <div class="profile-info">
                <p class="profile-name"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <a href="profile-form.php" class="btn btn-outline-light btn-sm">View Profile</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="dashboard-header">
            <h1>Coach Dashboard</h1>
            <form method="POST" action="" class="logout-button">
                <button type="submit" name="logout" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
        <div class="dashboard-content">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <!-- Coach-specific content goes here -->
        </div>
    </div>

    <script>
        // Sidebar toggle
        document.getElementById("toggleSidebar").addEventListener("click", function () {
            document.getElementById("sidebar").classList.toggle("collapsed");
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
