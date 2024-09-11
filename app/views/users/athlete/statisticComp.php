<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';


// Ensure the user is logged in and is an athlete
if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id']; // user_id from the users table

// Query to fetch athlete_id from athlete_details table based on user_id
$stmt = $pdo->prepare('SELECT athlete_id FROM athlete_details WHERE user_id = ?');
$stmt->execute([$user_id]);
$athlete = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the athlete_id is found
if ($athlete) {
    $athlete_id = $athlete['athlete_id'];
} else {
    // Redirect to complete profile if athlete_id is not found
    $_SESSION['error'] = 'Athlete profile not found. Please complete your profile.';
    header('Location: ' . BASE_URL . 'app/views/profiles/profile.php');
    exit();
}

// Fetch all competition scores for the athlete
$stmt = $pdo->prepare('
    SELECT 
        s.competition_id, s.score_50m_1, s.score_50m_2, s.total_score, s.tens_and_x, s.x_count, 
        c.competition_name, c.start_date, c.end_date
    FROM scores s
    JOIN competitions c ON s.competition_id = c.competition_id
    WHERE s.athlete_id = ? 
    ORDER BY c.start_date DESC
');
$stmt->execute([$athlete_id]);
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch overall averages for the athlete
$stmt = $pdo->prepare('
    SELECT 
        AVG(score_50m_1) AS avg_50m_1, 
        AVG(score_50m_2) AS avg_50m_2, 
        AVG(total_score) AS avg_total_score, 
        AVG(tens_and_x) AS avg_tens_and_x, 
        AVG(x_count) AS avg_x_count
    FROM 
        scores
    WHERE 
        athlete_id = ?
');
$stmt->execute([$athlete_id]);
$avg_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch best and lowest performances for the athlete
$stmt_best_lowest = $pdo->prepare('
    SELECT 
        MAX(total_score) AS best_total_score, 
        MAX(tens_and_x) AS best_tens_and_x, 
        MAX(x_count) AS best_x_count,
        MIN(total_score) AS lowest_total_score,
        MIN(tens_and_x) AS lowest_tens_and_x,
        MIN(x_count) AS lowest_x_count
    FROM 
        scores
    WHERE 
        athlete_id = ?
');
$stmt_best_lowest->execute([$athlete_id]);
$best_lowest_stats = $stmt_best_lowest->fetch(PDO::FETCH_ASSOC);
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Your Competition Statistics</h3>
        </div>
    </div>

    <!-- Display the average statistics -->
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

    <!-- Display the best performances -->
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <div class="row">
                <!-- Best Performances (Left Column) -->
                <div class="col-md-6">
                    <h5 class="text-success">Best Performances</h5>
                    <p><strong>Best Total Score:</strong> <?php echo $best_lowest_stats['best_total_score']; ?></p>
                    <p><strong>Most 10+X Hits:</strong> <?php echo $best_lowest_stats['best_tens_and_x']; ?></p>
                    <p><strong>Most X Hits:</strong> <?php echo $best_lowest_stats['best_x_count']; ?></p>
                </div>

                <!-- Lowest Performances (Right Column) -->
                <div class="col-md-6">
                    <h5 class="text-danger">Lowest Performances</h5>
                    <p><strong>Lowest Total Score:</strong> <?php echo $best_lowest_stats['lowest_total_score']; ?></p>
                    <p><strong>Fewest 10+X Hits:</strong> <?php echo $best_lowest_stats['lowest_tens_and_x']; ?></p>
                    <p><strong>Fewest X Hits:</strong> <?php echo $best_lowest_stats['lowest_x_count']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table for Competition Scores -->
    <div class="table-responsive mb-2">
        <table class="table table-bordered table-hover shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>Competition</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>50m-1</th>
                    <th>50m-2</th>
                    <th>Total Score</th>
                    <th>10+X Count</th>
                    <th>X Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scores as $score): ?>
                <tr>
                    <td><?php echo htmlspecialchars($score['competition_name']); ?></td>
                    <td><?php echo htmlspecialchars($score['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($score['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($score['score_50m_1']); ?></td>
                    <td><?php echo htmlspecialchars($score['score_50m_2']); ?></td>
                    <td><?php echo htmlspecialchars($score['total_score']); ?></td>
                    <td><?php echo htmlspecialchars($score['tens_and_x']); ?></td>
                    <td><?php echo htmlspecialchars($score['x_count']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="row mb-5">
        <div class="col text-end">
            <a href="inputScoreComp.php" class="btn btn-primary btn-lg d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-2"></i> Add New Score
            </a>
        </div>
    </div>

    <!-- Chart to Show Performance Trends -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title text-primary">Performance Trends</h4>
            <!-- Canvas for the chart -->
            <div style="position: relative; height: 60vh; width: 100%;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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
                    data: [
                        <?php foreach ($scores as $score): ?>
                            <?php echo $score['score_50m_1']; ?>,
                        <?php endforeach; ?>
                    ],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true
                },
                {
                    label: '50m-2 Score',
                    data: [
                        <?php foreach ($scores as $score): ?>
                            <?php echo $score['score_50m_2']; ?>,
                        <?php endforeach; ?>
                    ],
                    borderColor: 'rgba(192, 75, 75, 1)',
                    backgroundColor: 'rgba(192, 75, 75, 0.2)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allows the chart to adjust its aspect ratio
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
