<?php
// load Profile
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Archery Stats'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
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
                    <a href="index.php" class="nav-link text-white">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="statisticHome.php?type=competition" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>C.Statistics</span>
                    </a>
                </li>
                <li>
                    <a href="statisticHome.php?type=training" class="nav-link text-white">
                        <i class="bi bi-bullseye"></i>
                        <span>T.Statistics</span>
                    </a>
                </li>
                <li>
                    <a href="statCompare.php" class="nav-link text-white">
                        <i class="bi bi-clipboard2-data-fill"></i>
                        <span>Compare Statistics</span>
                    </a>
                </li>
            <?php elseif ($isAdmin): ?>
                <li>
                    <a href="index.php" class="nav-link text-white">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="program.php" class="nav-link text-white">
                        <i class="bi bi-bank2"></i>
                        <span>Program</span>
                    </a>
                </li>
                <li>
                    <a href="manageUsers.php" class="nav-link text-white">
                        <i class="bi bi-people-fill"></i>
                        <span>Users Management</span>
                    </a>
                </li>
            <?php elseif ($isCoach): ?>
                <li>
                    <a href="index.php" class="nav-link text-white">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="manageAthletes.php" class="nav-link text-white">
                        <i class="bi bi-people-fill"></i>
                        <span>Athlete Management</span>
                    </a>
                </li>
                <li>
                    <a href="compScoring.php" class="nav-link text-white">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Competition Scoring</span>
                    </a>
                </li>
                <li>
                    <a href="trainScoring.php" class="nav-link text-white">
                        <i class="bi bi-bullseye"></i>
                        <span>Training Scoring</span>
                    </a>
                </li>
            <?php endif ?>
        </ul>

        <hr>
        <div class="dropdown m-2 text-center">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
                <img src="<?php echo !empty($profile['profile_picture']) ? BASE_URL . 'public/images/profile_picture/' . htmlspecialchars($profile['profile_picture']) : BASE_URL . 'public/images/page_img/gradient.jpg'; ?>" alt="Profile Picture" width="32" height="32" class="rounded-circle me-2 profile-img">
                <span class="profile-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark shadow">
                <li class="px-3 py-2">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo !empty($profile['profile_picture']) ? BASE_URL . 'public/images/profile_picture/' . htmlspecialchars($profile['profile_picture']) : BASE_URL . 'public/images/page_img/gradient.jpg'; ?>" alt="Profile Picture" width="32" height="32" class="rounded-circle me-2 profile-img">
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