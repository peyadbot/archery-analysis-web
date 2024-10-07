<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$userId]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM athlete_details WHERE user_id = ?');
$stmt->execute([$userId]);
$athlete_details = $stmt->fetch(PDO::FETCH_ASSOC);

$profile_incomplete = empty($profile['name']) || empty($profile['ic_number']) || empty($profile['email']) || empty($profile['phone_number']);
$detail_incomplete = empty($athlete_details['mareos_id']) || empty($athlete_details['wareos_id']) || empty($athlete_details['bow_type']) || empty($athlete_details['bmi']);

try {
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
        <div class="d-flex justify-content-between align-items-center w-100">
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
        <div class="alert alert-warning">
            <h4 class="text-danger">Profile Incomplete</h4>
            <?php if ($profile_incomplete): ?>
                <p>Please complete your <strong>My Profile</strong> to access all dashboard features.</p>
                <a href="<?php echo BASE_URL . 'app/views/profiles/profile-form.php'; ?>" class="btn btn-primary">Complete Profile</a>
            <?php endif; ?>
            <?php if ($detail_incomplete): ?>
                <p>Please complete your <strong>Archery Details</strong> to access the dashboard.</p>
                <a href="<?php echo BASE_URL . 'app/views/profiles/athlete-form.php'; ?>" class="btn btn-primary">Complete Details</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Overview Cards -->
        <div class="row gy-4 mb-5 text-center">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg text-center">
                    <div class="card-body">
                        <i class="bi bi-trophy-fill text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title">Matches</h5>
                        <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['competitionCount']); ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="<?php echo BASE_URL . 'app/views/users/athlete/statCompetition.php'; ?>" class="btn btn-outline-primary">View Stats</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg text-center">
                    <div class="card-body">
                        <i class="bi bi-award text-success mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title">Best Local Score</h5>
                        <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['bestLocalScore']); ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="<?php echo BASE_URL . 'app/views/users/athlete/compScoring.php?view=local'; ?>" class="btn btn-outline-success">View Local Scores</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg text-center">
                    <div class="card-body">
                        <i class="bi bi-globe text-info mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title">Best International Score</h5>
                        <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['bestInternationalScore']); ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="<?php echo BASE_URL . 'app/views/users/athlete/compScoring.php?view=international'; ?>" class="btn btn-outline-info">View International Scores</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg text-center">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-fill text-warning mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title">Training Progress</h5>
                        <h2 class="display-4">80%</h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="#" class="btn btn-outline-warning">View Progress</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance & Matches Section -->
        <div class="row mb-5 d-flex align-items-stretch">
            <div class="col-lg-8 mb-5 d-flex flex-column">
                <div class="card flex-grow-1 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Monthly Total Score Trends</h5>
                        <div style="position: relative; height: 60vh; width: 100%;">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column">
                <!-- Competition -->
                <div class="card mb-5 flex-grow-1 shadow-lg">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Recent Competitions</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($dashboardData['recentCompetitions'])): ?>
                                <?php foreach ($dashboardData['recentCompetitions'] as $competition): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <h6 class="fw-bold"><?php echo htmlspecialchars($competition['competition_id']); ?></h6>
                                            <small class="text-muted">Saved at: <?php echo htmlspecialchars($competition['created_at']); ?></small><br>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary rounded-pill">
                                                Score: <?php echo htmlspecialchars($competition['total_score']); ?>
                                            </span><br>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">No recent competitions found.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Profile -->
                <div class="card mb-5 flex-grow-1 shadow-lg">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="profile-info">
                            <!-- Display Profile Info in Two Columns -->
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['name']); ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone_number']); ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>IC Number:</strong> <?php echo htmlspecialchars($profile['ic_number']); ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Passport:</strong> <?php echo htmlspecialchars($profile['passport_number']); ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($profile['home_address']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?php echo BASE_URL . 'app/handlers/AthleteReportHandler.php'; ?>" class="btn btn-outline-success">Download My Report</a>
                            <a href="<?php echo BASE_URL . 'app/views/profiles/profile.php'; ?>" class="btn btn-outline-primary">Edit Profile</a>
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
        const monthlyLocalTotals = <?php echo json_encode(array_column($dashboardData['monthlyScores'], 'local_total')); ?>;
        const monthlyInternationalTotals = <?php echo json_encode(array_column($dashboardData['monthlyScores'], 'international_total')); ?>;

        // Chart.js for Monthly Total Scores (Local and International)
        var ctx = document.getElementById('performanceChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Local',
                        data: monthlyLocalTotals,
                        borderColor: '#28a745',  
                        backgroundColor: 'rgba(40, 167, 69, 0.1)', 
                        fill: true,
                        tension: 0.3 
                    },
                    {
                        label: 'International',
                        data: monthlyInternationalTotals,
                        borderColor: '#007bff',  
                        backgroundColor: 'rgba(0, 123, 255, 0.1)', 
                        fill: true,
                        tension: 0.3 
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true, 
                        labels: {
                            boxWidth: 10,
                            padding: 25,
                            color: '#333', 
                            font: {
                                size: 12  
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.7)', 
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            color: '#333',
                            font: {
                                size: 14
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)' 
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            color: '#333',
                            font: {
                                size: 14
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    <?php endif; ?>
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>
