<?php
// require_once __DIR__ . '/../../../handlers/ProfileViewHandler.php';
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

// Fetch dashboard data based on user role
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    $dashboardData = getDashboardData($userId, $role);
    $profile = getProfile($userId);
    $profile_incomplete = $profile['incomplete'];
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Archery Stats</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL . 'public/css/userDashboard.css'; ?>">
</head>

<body>
    <!-- Sidebar Admin -->
    <div id="sidebar" class="sidebar d-flex flex-column flex-shrink-0 text-white bg-dark">
        <button class="btn btn-dark position-fixed" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <svg class="bi me-2" width="40" height="32">
                <use xlink:href="#"></use>
            </svg>
            <span class="fs-4"></span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto p-3">
            <li class="nav-item">
                <a href="#" class="nav-link text-white" aria-current="page">
                    <i class="bi bi-person-plus"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="program.php" class="nav-link text-white" aria-current="page">
                    <i class="bi bi-person-plus"></i>
                    Programs
                </a>
            </li>
            <li class="nav-item">
                <a href="user.php" class="nav-link text-white" aria-current="page">
                    <i class="bi bi-person-plus"></i>
                    User Management
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL . 'app\views\competition\competition.php'; ?>" class="nav-link text-white">
                    <i class="bi bi-calendar-plus"></i>
                    Competitions
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL . 'app\views\training\training.php'; ?>" class="nav-link text-white">
                    <i class="bi bi-bar-chart"></i>
                    Training
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown px-3 pb-3">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="#">New stats...</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL . 'public/home.php'; ?>">Home</a></li>
                <li>
                    <form method="POST" action="">
                        <button type="submit" name="logout" class="dropdown-item">
                            Sign out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Header -->
    <div>
        <div class="dashboard-header py-2 ps-5 bg-dark text-white">
            <p>Admin Dashboard</p>
        </div>
    </div>

    <!-- Dashboard Table -->
    <div class="container mt-5">
        <div class="row row-cols-1 gy-4">
            <div class="col-md-6 col-lg-12">
                <div class="box">
                    <h2>Welcome to Admin Dashboard <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                    <p>Today's date: <?php echo date('j M Y'); ?></p>
                    <p id="clock"></p>
                    <p>Ready to get started? View detailed stats and insights. track your progress and optimize your performance.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-0">
                    <div class="card-body">
                        <h5 class="card-title">Athletes</h5>
                        <h1><?php echo htmlspecialchars($dashboardData['athleteCount']); ?></h1>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="card-link">View Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-0">
                    <div class="card-body">
                        <h5 class="card-title">Coaches</h5>
                        <h1><?php echo htmlspecialchars($dashboardData['coachCount']); ?></h1>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="card-link">View Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-0">
                    <div class="card-body">
                        <h5 class="card-title">Competitions</h5>
                        <h1><?php echo htmlspecialchars($dashboardData['competitionCount']); ?></h1>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="card-link">View Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-0">
                    <div class="card-body">
                        <h5 class="card-title">Trainings</h5>
                        <h1><?php echo htmlspecialchars($dashboardData['trainingCount']); ?></h1>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="card-link">View Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" style="height: 400px;">
                <div class="box">
                    <h2>Recent Activity</h2>
                    <p>View important updates and messages from your coach.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="box">
                    <h2>Profile</h2>
                    <?php if ($profile_incomplete): ?>
                        <p class="text-warning">Your profile is incomplete. Please complete your profile.</p>
                        <a href="profile-form.php" class="btn btn-primary">Complete Profile</a>
                    <?php else: ?>
                        <p>Update your profile information and preferences.</p>
                        <div class="profile-info">
                            <p><strong>First Name:</strong> <?php echo htmlspecialchars($profile['first_name']); ?></p>
                            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($profile['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($profile['phone_number']); ?></p>
                            <?php if (!empty($profile['profile_picture'])): ?>
                                <img src="<?php echo '/public/images/user_img/' . htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail" style="max-width: 150px;">
                            <?php endif; ?>
                            <p><strong>IC Number:</strong> <?php echo htmlspecialchars($profile['ic_number']); ?></p>
                            <p><strong>Passport Number:</strong> <?php echo htmlspecialchars($profile['passport_number']); ?></p>
                            <p><strong>State:</strong> <?php echo htmlspecialchars($profile['state']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="box">
                    <h2>Training Progress</h2>
                    <p>Monitor your training performance:</p>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 35%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">35%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script>
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                var sidebar = document.getElementById('sidebar');
                if (sidebar.style.width === '0px' || sidebar.style.width === '') {
                    sidebar.style.width = '280px';
                } else {
                    sidebar.style.width = '0';
                }
            });

            function updateClock() {
                const now = new Date();
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                const formattedTime = `${hours}:${minutes}:${seconds} ${ampm}`;
                document.getElementById('clock').textContent = `Current time: ${formattedTime}`;
            }

            setInterval(updateClock, 1000);
            updateClock();
        </script>
</body>

</html>