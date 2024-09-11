<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athlete Dashboard - Archery Stats</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Custom Styles */
        body {
            font-family: Arial, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 60px 0 0;
            width: 250px;
            background-color: #343a40;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .nav-link {
            color: #ffffff;
            white-space: nowrap;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .sidebar .nav-link span {
            display: inline-block;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .profile-name {
            display: inline-block;
            transition: all 0.3s ease;
        }

        .profile-img {
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .profile-name {
            display: none;
        }

        .sidebar.collapsed .profile-img {
            margin: 0;
        }

        .sidebar.collapsed .dropdown-menu {
            left: 50%;
            transform: translateX(-50%);
        }

        .main-content {
            margin-left: 250px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        /* Sidebar takes the full screen on mobile devices */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.collapsed {
                width: 80px;
            }

            .main-content.collapsed {
                margin-left: 80px;
            }
        }

        /* Toggle button styling */
        .sidebar-toggle-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 1.25rem;
            z-index: 110;
        }

        .stat-box {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background-color: #e9ecef;
            margin-bottom: 20px;
        }

        .stat-box h4 {
            margin-bottom: 10px;
        }

        .stat-box p {
            font-size: 1.25rem;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column text-white bg-dark" id="sidebar">
        <button class="btn btn-dark sidebar-toggle-btn" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <hr>

        <ul class="nav nav-pills flex-column mb-auto px-3">
            <?php if ($isAthlete): ?>
                <li>
                    <a href="<?php echo BASE_URL . 'app/views/users/athlete/index.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL . 'app/views/users/athlete/statisticComp.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>C.Statistics</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL . 'app/views/users/athlete/statisticTrain.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>T.Statistics</span>
                    </a>
                </li>
            <?php elseif ($isAdmin): ?>
                <li>
                    <a href="<?php echo BASE_URL . 'app/views/users/admin/index.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL . 'app\views\competition\competition.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Competition</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL . 'app\views\training\training.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Training</span>
                    </a>
                </li>
                <li>
                    <a href="program.php" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Program</span>
                    </a>
                </li>
                <li>
                    <a href="user.php" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>User Management</span>
                    </a>
                </li>
            <?php elseif ($isCoach): ?>
                <li>
                    <a href="<?php echo BASE_URL . 'app/views/users/coach/index.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL . 'app\views\competition\competition.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Competition</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL . 'app\views\training\training.php'; ?>" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Training</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Athlete Management</span>
                    </a>
                </li>
            <?php endif ?>
        </ul>

        <hr>
        <div class="dropdown m-2 text-center">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
                <!-- Use user's profile picture dynamically -->
                <img src="https://github.com/mdo.png" alt="Profile Picture" width="32" height="32" class="rounded-circle me-2 profile-img">
                <span class="profile-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark shadow">
                <li class="px-3 py-2">
                    <div class="d-flex align-items-center">
                        <img src="https://github.com/mdo.png" alt="Profile Picture" width="64" height="64" class="rounded-circle me-2">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($_SESSION['username']); ?></h6>
                            <small style="color: gray;"><?php echo htmlspecialchars($_SESSION['role']); ?></small>
                        </div>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL . 'index.php'; ?>"><i class="bi bi-house-door-fill me-2"></i>Home</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL . 'app/views/profiles/profile.php'; ?>"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="">
                        <button type="submit" name="logout" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->