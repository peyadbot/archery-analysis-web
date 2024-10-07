<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/StatCompetitionHandler.php';

// Ensure the user is logged in and is an athlete
if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch athlete data
$athlete = getAthleteData($pdo, $user_id);

if (!$athlete) {
    $_SESSION['error'] = 'Athlete profile not found. Please complete your profile.';
    header('Location: ' . BASE_URL . 'app/views/profiles/profile.php');
    exit();
}

$mareos_id = $athlete['mareos_id'];

// Set default competition type if not set
if (!isset($_SESSION['competition_type'])) {
    $_SESSION['competition_type'] = 'local';
}

// Switch between local and international competitions
if (isset($_GET['competition_type'])) {
    $_SESSION['competition_type'] = $_GET['competition_type'];
}

$competition_type = $_SESSION['competition_type'];

// Fetch data for charts and stats
$scores = getCompetitionScores($pdo, $mareos_id, $competition_type);
$avg_stats = getAverageStats($pdo, $mareos_id, $competition_type);
$best_lowest_stats = getBestAndLowestStats($pdo, $mareos_id, $competition_type);
$monthly_competition_data = getMonthlyCompetitionData($pdo, $mareos_id, $competition_type);
$monthly_competitions = prepareMonthlyCompetitions($monthly_competition_data);
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Your Competition Statistics</h3>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-5">
        <div class="btn-group">
            <!-- Local Competitions Button -->
            <a href="?competition_type=local" class="btn <?php echo ($_SESSION['competition_type'] == 'local') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                Local Competitions
            </a>

            <!-- International Competitions Button -->
            <a href="?competition_type=international" class="btn <?php echo ($_SESSION['competition_type'] == 'international') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                International Competitions
            </a>
        </div>
    </div>

    <!-- Display average statistics -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="text-primary mb-3">Overall Averages</h5>
            <div class="row text-center">
                <div class="col-md-4 mb-2">
                    <p class="mb-1">Average M1 Score</p>
                    <p class="text-muted"><?php echo round($avg_stats['avg_m1'], 2); ?></p>
                </div>
                <div class="col-md-4 mb-2">
                    <p class="mb-1">Average M2 Score</p>
                    <p class="text-muted"><?php echo round($avg_stats['avg_m2'], 2); ?></p>
                </div>
                <div class="col-md-4 mb-2">
                    <p class="mb-1">Total Average Score</p>
                    <p class="text-muted"><?php echo round($avg_stats['avg_total_score'], 2); ?></p>
                </div>
                <div class="col-md-4 mb-2">
                    <p class="mb-1">Average 10s Count</p>
                    <p class="text-muted"><?php echo round($avg_stats['avg_total_10'], 2); ?></p>
                </div>
                <div class="col-md-4 mb-2">
                    <p class="mb-1">Average 9s Count</p>
                    <p class="text-muted"><?php echo round($avg_stats['avg_total_9'], 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Display best and lowest performances -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="text-primary mb-3">Best and Lowest Performances</h5>
            <div class="row">
                <!-- Best Performances -->
                <div class="col-md-6">
                    <div class="p-2">
                        <p class="text-muted">Best Performances</p>
                        <ul class="list-unstyled">
                            <li>Best Total Score: <?php echo $best_lowest_stats['best_total_score']; ?></li>
                            <li>Most 10s: <?php echo $best_lowest_stats['best_total_10']; ?></li>
                            <li>Most 9s: <?php echo $best_lowest_stats['best_total_9']; ?></li>
                        </ul>
                    </div>
                </div>

                <!-- Lowest Performances -->
                <div class="col-md-6">
                    <div class="p-2">
                        <p class="text-muted">Lowest Performances</p>
                        <ul class="list-unstyled">
                            <li>Lowest Total Score: <?php echo $best_lowest_stats['lowest_total_score']; ?></li>
                            <li>Fewest 10s: <?php echo $best_lowest_stats['lowest_total_10']; ?></li>
                            <li>Fewest 9s: <?php echo $best_lowest_stats['lowest_total_9']; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Score Button -->
    <div class="row mb-3">
        <div class="col text-end">
            <a href="compScoring.php" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-2"></i> Add Score
            </a>
        </div>
    </div>

    <!-- Competition Scores Table -->
    <div class="table-responsive mb-2">
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Event Distance</th>
                    <th>M1 Score</th>
                    <th>M2 Score</th>
                    <th>Total Score</th>
                    <th>10s</th>
                    <th>9s</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($scores)): ?>
                    <?php foreach ($scores as $score): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($score['competition_id']); ?></td>
                            <td><?php echo htmlspecialchars($score['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($score['event_distance']); ?></td>
                            <td><?php echo htmlspecialchars($score['m_1_score']); ?></td>
                            <td><?php echo htmlspecialchars($score['m_2_score']); ?></td>
                            <td><?php echo htmlspecialchars($score['total_score']); ?></td>
                            <td><?php echo htmlspecialchars($score['total_10']); ?></td>
                            <td><?php echo htmlspecialchars($score['total_9']); ?></td>
                            <td><?php echo htmlspecialchars($score['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No competition found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Competition Chart -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h4 class="text-primary">Competition Counter</h4>
            <div style="position: relative; height: 60vh; width: 100%;">
                <canvas id="monthlyCompetitionsBar"></canvas>
            </div>
        </div>
    </div>

    <!-- Scoring Chart -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h4 class="text-primary">Performance Trends</h4>
            <div style="position: relative; height: 60vh; width: 100%;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly competitions horizontal bar chart
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

        const monthlyCompetitionsData = [
            <?php echo implode(',', $monthly_competitions); ?>
        ];

        // Bar chart
        const monthlyCompetitionsBarCtx = document.getElementById('monthlyCompetitionsBar').getContext('2d');
        const monthlyCompetitionsBar = new Chart(monthlyCompetitionsBarCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Competitions per Month',
                    data: monthlyCompetitionsData,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const value = tooltipItem.raw; // Value of the current bar
                                const total = tooltipItem.dataset.data.reduce((acc, cur) => acc + cur, 0); // Calculate total competitions
                                const percentage = ((value / total) * 100).toFixed(2); // Calculate percentage
                                return `${tooltipItem.label}: ${value} competitions (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Scoring performance chart
        const scoreM1Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m_1_score']; ?>,
            <?php endforeach; ?>
        ];

        const scoreM2Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m_2_score']; ?>,
            <?php endforeach; ?>
        ];

        const total10sData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['total_10']; ?>,
            <?php endforeach; ?>
        ];

        const total9sData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['total_9']; ?>,
            <?php endforeach; ?>
        ];

        const totalScoreData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['total_score']; ?>,
            <?php endforeach; ?>
        ];

        const averageScores = scoreM1Data.map((_, i) => {
            return (scoreM1Data[i] + scoreM2Data[i] + total10sData[i] + total9sData[i]) / 4;
        });

        // Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($scores as $score): ?> '<?php echo htmlspecialchars($score['competition_id']); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                        label: 'M1 Score',
                        data: scoreM1Data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'M2 Score',
                        data: scoreM2Data,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: '10s Count',
                        data: total10sData,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: '9s Count',
                        data: total9sData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Total Score',
                        data: totalScoreData,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
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
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>