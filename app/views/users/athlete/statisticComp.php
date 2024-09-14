<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/ChartDataHandler.php';

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

$athlete_id = $athlete['athlete_id'];

// Fetch data for charts and stats
$scores = getCompetitionScores($pdo, $athlete_id);
$avg_stats = getAverageStats($pdo, $athlete_id);
$best_lowest_stats = getBestAndLowestStats($pdo, $athlete_id);
$competition_stats = getWinLossStats($pdo, $athlete_id);
$monthly_competition_data = getMonthlyCompetitionData($pdo, $athlete_id);
$monthly_competitions = prepareMonthlyCompetitions($monthly_competition_data);

$wins = (int)$competition_stats['wins'];
$losses = (int)$competition_stats['losses'];
$total_competitions = (int)$competition_stats['total_competitions'];
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Your Competition Statistics</h3>
        </div>
    </div>

    <!-- Display average statistics -->
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <h4 class="card-title text-primary">Overall Averages</h4>
            <div class="row">
                <div class="col-md-4">
                    <p><strong>50m-1 Average Score:</strong> <?php echo round($avg_stats['avg_50m_1'], 2); ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>50m-2 Average Score:</strong> <?php echo round($avg_stats['avg_50m_2'], 2); ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total Average Score:</strong> <?php echo round($avg_stats['avg_total_score'], 2); ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Average 10+X Count:</strong> <?php echo round($avg_stats['avg_tens_and_x'], 2); ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Average X Count:</strong> <?php echo round($avg_stats['avg_x_count'], 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Display best and lowest performances -->
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <div class="row">
                <!-- Best Performances -->
                <div class="col-md-6">
                    <h5 class="text-success">Best Performances</h5>
                    <p><strong>Best Total Score:</strong> <?php echo $best_lowest_stats['best_total_score']; ?></p>
                    <p><strong>Most 10+X Hits:</strong> <?php echo $best_lowest_stats['best_tens_and_x']; ?></p>
                    <p><strong>Most X Hits:</strong> <?php echo $best_lowest_stats['best_x_count']; ?></p>
                </div>

                <!-- Lowest Performances -->
                <div class="col-md-6">
                    <h5 class="text-danger">Lowest Performances</h5>
                    <p><strong>Lowest Total Score:</strong> <?php echo $best_lowest_stats['lowest_total_score']; ?></p>
                    <p><strong>Fewest 10+X Hits:</strong> <?php echo $best_lowest_stats['lowest_tens_and_x']; ?></p>
                    <p><strong>Fewest X Hits:</strong> <?php echo $best_lowest_stats['lowest_x_count']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Competition Scores Table -->
    <div class="table-responsive mb-2">
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Competition</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>50m-1</th>
                    <th>50m-2</th>
                    <th>Total Score</th>
                    <th>10+X Count</th>
                    <th>X Count</th>
                    <th>W/L</th>
                    <th>Rank</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($scores)): ?>
                    <?php foreach ($scores as $score): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($score['competition_id']); ?></td>
                        <td><?php echo htmlspecialchars($score['competition_name']); ?></td>
                        <td><?php echo htmlspecialchars($score['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($score['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($score['score_50m_1']); ?></td>
                        <td><?php echo htmlspecialchars($score['score_50m_2']); ?></td>
                        <td><?php echo htmlspecialchars($score['total_score']); ?></td>
                        <td><?php echo htmlspecialchars($score['tens_and_x']); ?></td>
                        <td><?php echo htmlspecialchars($score['x_count']); ?></td>
                        <td><?php echo isset($score['win_status']) ? htmlspecialchars($score['win_status']) : '-'; ?></td>
                        <td><?php echo isset($score['rank']) ? htmlspecialchars($score['rank']) : '-'; ?></td>
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

    <!-- Add/Edit Score Button -->
    <div class="row mb-5">
        <div class="col text-end">
            <a href="inputScoreComp.php" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-2"></i> Add & Edit Score
            </a>
        </div>
    </div>

    <!-- Pie Chart for Win/Loss and Competition Statistics -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h4 class="card-title text-primary text-center mb-5">Competition Statistics</h4>
            <div class="row">
                <!-- Win/Loss Chart -->
                <div class="col-md-6 d-flex justify-content-center mb-2">
                    <div class="chart-container" style="position: relative; height: 30vh;">
                        <canvas id="winLossChart"></canvas>
                    </div>
                </div>
                <!-- Monthly Competitions Chart -->
                <div class="col-md-6 d-flex justify-content-center mb-2">
                    <div class="chart-container" style="position: relative; height: 40vh;">
                        <canvas id="monthlyCompetitionsDoughnut"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scoring Chart -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h4 class="card-title text-primary">Performance Trends</h4>
            <div style="position: relative; height: 60vh; width: 100%;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly competititons
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
        // Data from PHP
        const monthlyCompetitionsData = [
            <?php echo implode(',', $monthly_competitions); ?>
        ];

        const monthlyCompetitionsDoughnutCtx = document.getElementById('monthlyCompetitionsDoughnut').getContext('2d');
        const monthlyCompetitionsDoughnut = new Chart(monthlyCompetitionsDoughnutCtx, {
            type: 'doughnut',
            data: {
                labels: months,
                datasets: [{
                    label: 'Competitions per Month',
                    data: monthlyCompetitionsData,
                    backgroundColor: [
                        '#36a2eb', '#ff6384', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40', '#ff6384', '#36a2eb', '#4bc0c0', '#ffcd56', '#9966ff', '#ff9f40'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            },
                            boxWidth: 5, // Smaller legend boxes
                            padding: 10 // Space between legend items
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const value = tooltipItem.raw; // Value of the current segment
                                const total = tooltipItem.dataset.data.reduce((acc, cur) => acc + cur, 0); // Calculate total competitions
                                const percentage = ((value / total) * 100).toFixed(2); // Calculate percentage
                                return tooltipItem.label + ': ' + value + ' competitions (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Win&Lose chart
        const winLossData = {
            labels: ['Wins', 'Losses'],
            datasets: [{
                data: [<?php echo $wins; ?>, <?php echo $losses; ?>],
                backgroundColor: ['#4bc0c0', '#ff6384'], // Green for wins, Red for losses
                hoverBackgroundColor: ['#5cd3d3', '#ff8199'], // Lighter hover effect
                borderColor: '#ffffff', 
                borderWidth: 4
            }]
        };

        const winLossCtx = document.getElementById('winLossChart').getContext('2d');
        const winLossChart = new Chart(winLossCtx, {
            type: 'doughnut',
            data: winLossData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            },
                            boxWidth: 5,
                            padding: 10 
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const label = tooltipItem.label || '';
                                const value = tooltipItem.raw;
                                const percentage = ((value / <?php echo $total_competitions; ?>) * 100).toFixed(2);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Scoring chart
        // Function to calculate the linear regression (trend line)
        function calculateTrendLine(scores) {
            const n = scores.length;
            const sumX = scores.reduce((acc, val, i) => acc + i, 0);  // Sum of indices (x-axis values)
            const sumY = scores.reduce((acc, val) => acc + val, 0);   // Sum of scores (y-axis values)
            const sumXY = scores.reduce((acc, val, i) => acc + i * val, 0);  // Sum of x*y
            const sumX2 = scores.reduce((acc, val, i) => acc + i * i, 0);   // Sum of x^2

            const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);  // Calculate slope
            const intercept = (sumY - slope * sumX) / n;  // Calculate intercept

            // Return the trend line (y = slope * x + intercept) for all x values
            return scores.map((_, i) => slope * i + intercept);
        }

        // Extract data from PHP
        const score50m1Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['score_50m_1']; ?>,
            <?php endforeach; ?>
        ];

        const score50m2Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['score_50m_2']; ?>,
            <?php endforeach; ?>
        ];

        const tensAndXData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['tens_and_x']; ?>,
            <?php endforeach; ?>
        ];

        const xCountData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['x_count']; ?>,
            <?php endforeach; ?>
        ];

        // Calculate the average score for each competition
        const averageScores = score50m1Data.map((_, i) => {
            return (score50m1Data[i] + score50m2Data[i] + tensAndXData[i] + xCountData[i]) / 4;
        });

        const overallTrend = calculateTrendLine(averageScores);

        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($scores as $score): ?>
                        '<?php echo htmlspecialchars($score['competition_id']); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [
                    {
                        label: '50m-1 Score',
                        data: score50m1Data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    },
                    {
                        label: '50m-2 Score',
                        data: score50m2Data,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true
                    },
                    {
                        label: '10+X Count',
                        data: tensAndXData,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        fill: true
                    },
                    {
                        label: 'X Count',
                        data: xCountData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: true
                    },
                    // Overall Trend Line
                    {
                        label: 'Overall Trend',
                        data: overallTrend,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0)',
                        borderDash: [5, 5],
                        fill: false
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
                            boxWidth: 5,
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
