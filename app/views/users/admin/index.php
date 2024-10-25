<?php
$title = 'Admin Dashboard';
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
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

$latestCompetitions = $dashboardData['latestCompetitions'];
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h3 class="m-0">Admin Dashboard</h3>
            <div id="clock-container" class="text-end">
                <p id="clock-time" class="mb-0"></p>
                <p id="clock-date" class="mb-0"></p>
            </div>
        </div>
    </div>

    <!-- Dashboard Overview -->
    <div class="row gy-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg text-center">
                <div class="card-body">
                    <i class="bi bi-graph-up-arrow text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h5 class="card-title">Competitions</h5>
                    <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['competitionCount']); ?></h2>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="competition.php" class="btn btn-outline-primary">View Competitions</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg text-center">
                <div class="card-body">
                    <i class="bi bi-people text-info mb-3" style="font-size: 2.5rem;"></i>
                    <h5 class="card-title">Athletes</h5>
                    <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['athleteCount']); ?></h2>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="manageUsers.php?filterRole=athlete" class="btn btn-outline-info">Manage Athletes</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg text-center">
                <div class="card-body">
                    <i class="bi bi-people text-info mb-3" style="font-size: 2.5rem;"></i>
                    <h5 class="card-title">Coaches</h5>
                    <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['coachCount']); ?></h2>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="manageUsers.php?filterRole=coach" class="btn btn-outline-info">Manage Coaches</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <!-- Latest Competitions Section -->
        <div class="col-lg-8 d-flex flex-column">
            <div class="card shadow-lg mb-5 h-100"> <!-- Added h-100 -->
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Latest Competitions</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <ul class="list-group list-group-flush flex-grow-1">
                        <?php if (!empty($latestCompetitions)): ?>
                            <?php foreach ($latestCompetitions as $competition): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <h6 class="fw-bold"><?php echo htmlspecialchars($competition['ToName']); ?></h6>
                                        <small class="text-muted">Code: <?php echo htmlspecialchars($competition['ToCode']); ?></small><br>
                                        <small class="text-muted">Location: <?php echo htmlspecialchars($competition['ToWhere']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary rounded-pill">
                                            From: <?php echo htmlspecialchars($competition['ToWhenFrom']); ?>
                                        </span><br>
                                        <span class="badge bg-secondary rounded-pill">
                                            To: <?php echo htmlspecialchars($competition['ToWhenTo']); ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted">No competitions available at the moment.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent User Registrations -->
        <div class="col-lg-4 d-flex flex-column">
            <div class="card shadow-lg mb-5 h-100"> <!-- Added h-100 -->
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Recent User Registrations</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    <ul class="list-group list-group-flush flex-grow-1">
                        <?php if (!empty($dashboardData['recentUsers'])): ?>
                            <?php foreach ($dashboardData['recentUsers'] as $user): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <h6 class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></h6>
                                        <small class="text-muted">Role: <?php echo htmlspecialchars($user['role']); ?></small><br>
                                        <small class="text-muted">Registered on: <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($user['created_at']))); ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted">No recent user registrations found.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
        const formattedDate = now.toLocaleDateString('en-US', {
            year: 'numeric', 
            month: 'long', 
            day: 'numeric'
        });

        document.getElementById('clock-time').textContent = formattedTime;
        document.getElementById('clock-date').textContent = formattedDate;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>
