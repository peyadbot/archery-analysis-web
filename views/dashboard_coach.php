<?php
session_start();
require_once __DIR__ . '/../includes/db_connection.php';

// Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
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
    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
        }
        .dashboard-header {
            background-color: #fd7e14;
            color: #ffffff;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
            margin-bottom: 1.5rem;
            position: relative; /* Ensures the logout button is positioned correctly */
        }
        .dashboard-header h1 {
            margin: 0;
        }
        .dashboard-content {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logout-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
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
            <!-- Admin-specific content goes here -->
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
