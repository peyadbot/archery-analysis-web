<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

// Check if the user is logged in and has the coach role
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

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h3 class="m-0">Admin Dashboard</h3>
            <div class="d-flex flex-column align-items-end ms-auto">
                <p id="clock" class="mb-0"></p>
                <p class="mb-0"><?php echo date('j M Y'); ?></p>
            </div>
        </div>
    </div>

    <!-- Dashboard Overview -->
    <div class="row gy-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-graph-up text-primary" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-3">Competitions</h5>
                    <h1><?php echo htmlspecialchars($dashboardData['competitionCount']); ?></h1>
                </div>
                <div class="card-footer">
                    <a href="#" class="card-link">View Details</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-person text-success" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-3">Trainings</h5>
                    <h1><?php echo htmlspecialchars($dashboardData['trainingCount']); ?></h1>
                </div>
                <div class="card-footer">
                    <a href="#" class="card-link">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-3">Athletes</h5>
                    <h1><?php echo htmlspecialchars($dashboardData['athleteCount']); ?></h1>
                </div>
                <div class="card-footer">
                    <a href="#" class="card-link">Manage Athletes</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-3">Coach</h5>
                    <h1><?php echo htmlspecialchars($dashboardData['coachCount']); ?></h1>
                </div>
                <div class="card-footer">
                    <a href="#" class="card-link">Manage Coach</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Matches Section -->
    <div class="row mb-5 d-flex align-items-stretch">
        <div class="col-lg-4 d-flex flex-column">
            <div class="card mb-5 flex-grow-1">
                <div class="card-body">
                    <h4>Recesnt Activity</h4>
                    <ul class="list-group">
                        <li class="list-group-item">New user registered</li>
                        <li class="list-group-item">New competition added for October</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex flex-column">
            <div class="card mb-5 flex-grow-1">
                <div class="card-body">
                    <h4>Notification</h4>
                    <ul class="list-group">
                        <li class="list-group-item">Upcoming training session on Sept 20</li>
                        <li class="list-group-item">New competition added for October</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        var content = document.getElementById('mainContent');
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
    });

    // Clock
    function updateClock() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const formattedTime = `${hours}:${minutes}:${seconds} ${ampm}`;
        document.getElementById('clock').textContent = `${formattedTime}`;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>