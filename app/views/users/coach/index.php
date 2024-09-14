<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

// Check if the user is logged in and has the coach role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

// Fetch dashboard data based on user role
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$profile_incomplete = empty($profile['name']) || empty($profile['ic_number']) || empty($profile['email']) || empty($profile['phone_number']);

try {
    $dashboardData = getDashboardData($userId, $role);
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
            <h3 class="m-0">Coach Dashboard</h3>
            <div class="d-flex flex-column align-items-end ms-auto">
                <p id="clock" class="mb-0"></p>
                <p class="mb-0"><?php echo date('j M Y'); ?></p>
            </div>
        </div>
    </div>

    <!-- Profile Incomplete Warning -->
    <?php if ($profile_incomplete): ?>
        <div class="alert alert-warning">
            <h4 class="text-danger">Profile Incomplete</h4>
            <p>Your profile is incomplete. Please complete your profile to access the dashboard features.</p>
            <a href="<?php echo BASE_URL . 'app/views/profiles/profile.php'; ?>" class="btn btn-primary">Complete Profile</a>
        </div>
    <?php else: ?>

        <!-- Dashboard Overview -->
        <div class="row gy-4 mb-5">
            <div class="col-lg-4 col-md-6">
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
            <div class="col-lg-4 col-md-6">
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
            <div class="col-lg-4 col-md-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                        <h5 class="card-title mt-3">Athletes</h5>
                        <h1>200</h1>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="card-link">Manage Athletes</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance & Matches Section -->
        <div class="row mb-5 d-flex align-items-stretch">
            <div class="col-lg-8 mb-5 d-flex flex-column">
                <div class="card shadow-sm flex-grow-1 p-4">
                    <h2 class="text-primary">Training Progress</h2>
                    <p class="text-muted">Monitor your training performance:</p>

                    <!-- Progress Bar 1 -->
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <span class="fw-bold">75% Complete</span>
                        </div>
                    </div>

                    <!-- Progress Bar 2 -->
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                            <span class="fw-bold">50% Complete</span>
                        </div>
                    </div>

                    <!-- Progress Bar 3 -->
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 35%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                            <span class="fw-bold">35% Complete</span>
                        </div>
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
                <div class="card mb-5 flex-grow-1">
                    <div class="card-body">
                        <h4>User Profile</h4>
                        <p>Update your profile information and preferences.</p>
                        <div class="profile-info">
                            <p><strong>First Name:</strong> <?php echo htmlspecialchars($profile['name']); ?></p>
                            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($profile['ic_number']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($profile['phone_number']); ?></p>
                            <p><strong>IC Number:</strong> <?php echo htmlspecialchars($profile['ic_number']); ?></p>
                            <p><strong>Passport Number:</strong> <?php echo htmlspecialchars($profile['passport_number']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
        document.getElementById('clock').textContent = `${formattedTime}`;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>