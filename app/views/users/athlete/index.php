<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM athlete_details WHERE user_id = ?');
$stmt->execute([$user_id]);
$athlete_details = $stmt->fetch(PDO::FETCH_ASSOC);

$profile_incomplete = empty($profile['name']) || empty($profile['ic_number']) || empty($profile['email']) || empty($profile['phone_number']);
$detail_incomplete = empty($athlete_details['mareos_id']) || empty($athlete_details['wareos_id']) || empty($athlete_details['bow_type']) || empty($athlete_details['bmi']);

try {
    $profile_incomplete = empty($profile['name']) || empty($profile['email']) || empty($profile['phone_number']);
    
    // Only fetch dashboard data if the profile is complete
    if (!$profile_incomplete) {
        $dashboardData = getDashboardData($userId, $role);
    }
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Athlete Dashboard</h3>
        </div>
        <?php if (isset($_SESSION['impersonating']) && $_SESSION['impersonating'] === true): ?>
            <form method="POST" action="../../../handlers/ImpersonateStopHandler.php" class="pt-4">
                <button type="submit" class="btn btn-warning w-100">
                    <i class="bi bi-arrow-left-circle"></i> Return to Coach Account
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Profile Incomplete Warning -->
    <?php if ($profile_incomplete || $detail_incomplete): ?>
        <?php if ($profile_incomplete): ?>
            <div class="alert alert-warning">
                <h4 class="text-danger">Profile Incomplete</h4>
                <p>Your account is incomplete. Please complete your <strong>My Profile</strong> to access the dashboard features.</p>
                <a href="<?php echo BASE_URL . 'app/views/profiles/profile-form.php'; ?>" class="btn btn-primary">Complete Profile</a>
            </div>
        <?php endif; ?>
        <?php if ($detail_incomplete): ?>
            <div class="alert alert-warning">
                <h4 class="text-danger">Archery Detail Incomplete</h4>
                <p>Your account is incomplete. Please complete your <strong>Archery Details</strong> to access the dashboard features.</p>
                <a href="<?php echo BASE_URL . 'app/views/profiles/athlete-form.php'; ?>" class="btn btn-primary">Complete Detail</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Overview Cards -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="stat-box">
                    <h4>Matches</h4>
                    <p><?php echo $dashboardData['competitionCount']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h4>Win Rate</h4>
                    <p>98%</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h4>Best Score</h4>
                    <p><?php echo $dashboardData['bestScore']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h4>Training Progress</h4>
                    <p>80%</p>
                </div>
            </div>
        </div>

        <!-- Performance & Matches Section -->
        <div class="row mb-5 d-flex align-items-stretch">
            <div class="col-lg-8 mb-5 d-flex flex-column">
                <div class="card flex-grow-1">
                    <div class="card-body">
                        <h4>Performance Trends</h4>
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
                <div class="card mb-5 flex-grow-1">
                    <div class="card-body">
                        <h4>Recent Competitions</h4>
                        <?php if (!empty($dashboardData['recentCompetitions'])): ?>
                            <ul class="list-group ">
                                <?php foreach ($dashboardData['recentCompetitions'] as $competition): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold"><?php echo htmlspecialchars($competition['competition_name']); ?></div>
                                            <span class="text-muted">Date: <?php echo htmlspecialchars($competition['start_date']); ?></span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($competition['total_score']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No recent competitions found.</p>
                        <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php if (!$profile_incomplete): ?>
        // Prepare monthly scores for Chart.js
        const months = <?php echo json_encode(array_column($dashboardData['monthlyScores'], 'month')); ?>;
        const monthlyTotals = <?php echo json_encode(array_column($dashboardData['monthlyScores'], 'total')); ?>;

        // Chart.js for Monthly Total Scores
        var ctx = document.getElementById('performanceChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Total Score',
                    data: monthlyTotals,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true // Adjust depending on expected score range
                    }
                }
            }
        });
    <?php endif; ?>
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>