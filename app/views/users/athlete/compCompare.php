<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/StatCompetitionHandler.php';

if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$athlete = getAthleteData($pdo, $user_id);

if (!$athlete) {
    $_SESSION['error'] = 'Athlete profile not found. Please complete your profile.';
    header('Location: ' . BASE_URL . 'app/views/profiles/profile.php');
    exit();
}

$mareos_id = $athlete['mareos_id'];
$competitions = getAllCompetitions($pdo, $mareos_id);

// Initialize $comparison_data
$comparison_data = [];
$selected_competitions = [];
$selected_metrics = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['competitions'])) {
        $selected_competitions = $_POST['competitions'];
        $comparison_data = getCompetitionComparison($pdo, $mareos_id, $selected_competitions);
    }
    if (!empty($_POST['metrics'])) {
        $selected_metrics = $_POST['metrics'];
    }
}

// Define all available metrics
$all_metrics = [
    'total_score' => 'Total Score',
    'm1_score' => 'M1 Score',
    'm2_score' => 'M2 Score',
    'total_10X' => 'Total 10+X',
    'total_109' => 'Total 10/9'
];

// If no metrics are selected, select all by default
if (empty($selected_metrics)) {
    $selected_metrics = array_keys($all_metrics);
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Compare Performance Across Competitions</h3>
        </div>
    </div>

    <!-- Selection Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="competitionForm" action="" method="post">
                <div id="competitionSelectionGroup">
                    <?php foreach ($selected_competitions as $index => $competition_id): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="competition<?php echo $index; ?>" class="form-label">Select Competition</label>
                                <select id="competition<?php echo $index; ?>" name="competitions[]" class="form-control" required>
                                    <option value="">Select a competition</option>
                                    <?php foreach ($competitions as $competition): ?>
                                        <option value="<?php echo htmlspecialchars($competition['competition_id']); ?>" <?php echo ($competition['competition_id'] == $competition_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($competition['event_name']) . ' (' . date('d/m/Y', strtotime($competition['created_at'])) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if ($index > 0): ?>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-btn">
                                        <i class="bi bi-dash-lg"></i> Remove
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($selected_competitions)): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstCompetition" class="form-label">Select Competition</label>
                                <select id="firstCompetition" name="competitions[]" class="form-control" required>
                                    <option value="">Select a competition</option>
                                    <?php foreach ($competitions as $competition): ?>
                                        <option value="<?php echo htmlspecialchars($competition['competition_id']); ?>">
                                            <?php echo htmlspecialchars($competition['event_name']) . ' (' . date('d/m/Y', strtotime($competition['created_at'])) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success add-more-btn">
                            <i class="bi bi-plus-lg"></i> Add Competition
                        </button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Select Metrics to Display</label>
                        <?php foreach ($all_metrics as $metric_key => $metric_name): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="metrics[]" value="<?php echo $metric_key; ?>" id="metric_<?php echo $metric_key; ?>" <?php echo in_array($metric_key, $selected_metrics) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="metric_<?php echo $metric_key; ?>">
                                    <?php echo $metric_name; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Compare</button>
            </form>
        </div>
    </div>

    <!-- Comparison Results -->
    <?php if (!empty($comparison_data)): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="text-primary">Comparison Results</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Competition Name</th>
                                <th>Event Distance</th>
                                <?php foreach ($selected_metrics as $metric): ?>
                                    <th><?php echo $all_metrics[$metric]; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comparison_data as $data): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($data['event_name']); ?></td>
                                    <td><?php echo htmlspecialchars($data['event_distance']); ?></td>
                                    <?php foreach ($selected_metrics as $metric): ?>
                                        <td><?php echo htmlspecialchars($data[$metric]); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Performance Comparison Chart -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="text-primary">Performance Comparison Chart</h4>
                <div style="position: relative; height: 60vh; width: 100%;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Average Per Arrow Chart -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="text-primary">Average Per Arrow Comparison</h4>
                <div style="position: relative; height: 60vh; width: 100%;">
                    <canvas id="avgPerArrowChart"></canvas>
                </div>
            </div>
        </div>

        <script>
            const ctx = document.getElementById('performanceChart').getContext('2d');
            const avgCtx = document.getElementById('avgPerArrowChart').getContext('2d');

            const labels = <?php echo json_encode(array_map(function($metric) use ($all_metrics) { return $all_metrics[$metric]; }, $selected_metrics)); ?>;

            const datasets = [
                <?php 
                $colors = [
                    'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 
                    'rgba(54, 162, 235, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
                ];
                foreach ($comparison_data as $index => $data): 
                $color = $colors[$index % count($colors)];
                ?>
                {
                    label: '<?php echo htmlspecialchars($data['event_name']); ?>',
                    data: [
                        <?php 
                        foreach ($selected_metrics as $metric) {
                            echo htmlspecialchars($data[$metric]) . ',';
                        }
                        ?>
                    ],
                    backgroundColor: '<?php echo str_replace('1)', '0.2)', $color); ?>',
                    borderColor: '<?php echo $color; ?>',
                    borderWidth: 2,
                    tension: 0.3
                },
                <?php endforeach; ?>
            ];

            const performanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Score',
                                font: {
                                    size: 14
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Metrics',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });

            // Average per arrow calculation
            const avgDatasets = [
                <?php foreach ($comparison_data as $index => $data): 
                $color = $colors[$index % count($colors)];
                ?>
                {
                    label: '<?php echo htmlspecialchars($data['event_name']); ?>',
                    data: [
                        <?php 
                        foreach ($selected_metrics as $metric) {
                            if ($metric === 'total_score' || $metric === 'total_10X' || $metric === 'total_109') {
                                echo '(' . $data[$metric] . ' / 72).toFixed(2),';
                            } elseif ($metric === 'm1_score' || $metric === 'm2_score') {
                                echo '(' . $data[$metric] . ' / 36).toFixed(2),';
                            } else {
                                echo $data[$metric] . ',';
                            }
                        }
                        ?>
                    ],
                    backgroundColor: '<?php echo str_replace('1)', '0.2)', $color); ?>',
                    borderColor: '<?php echo $color; ?>',
                    borderWidth: 2,
                    tension: 0.3
                },
                <?php endforeach; ?>
            ];

            const avgPerArrowChart = new Chart(avgCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: avgDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Average Score Per Arrow',
                                font: {
                                    size: 14
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Metrics',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>

<script>
    document.querySelector('.add-more-btn').addEventListener('click', function () {
        const competitionGroup = document.getElementById('competitionSelectionGroup');
        const newSelection = document.createElement('div');
        newSelection.classList.add('row', 'mb-3');
        newSelection.innerHTML = `
            <div class="col-md-6">
                <label for="competition" class="form-label">Select Another Competition</label>
                <select name="competitions[]" class="form-control" required>
                    <option value="">Select a competition</option>
                    <?php foreach ($competitions as $competition): ?>
                        <option value="<?php echo htmlspecialchars($competition['competition_id']); ?>">
                            <?php echo htmlspecialchars($competition['event_name']) . ' (' . date('d/m/Y', strtotime($competition['created_at'])) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-btn">
                    <i class="bi bi-dash-lg"></i> Remove
                </button>
            </div>
        `;
        competitionGroup.appendChild(newSelection);

        // Remove competition functionality
        newSelection.querySelector('.remove-btn').addEventListener('click', function () {
            newSelection.remove();
        });
    });

    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function () {
            this.closest('.row').remove();
        });
    });
</script>

<?php
function getAllCompetitions($pdo, $mareos_id) {
    $query = "SELECT competition_id, event_name, event_distance, created_at FROM local_comp_scores WHERE mareos_id = ? UNION SELECT competition_id, event_name, event_distance, created_at FROM international_comp_scores WHERE mareos_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$mareos_id, $mareos_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCompetitionComparison($pdo, $mareos_id, $selected_competitions) {
    if (empty($selected_competitions) || !is_array($selected_competitions)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($selected_competitions), '?'));
    $query = "
        SELECT competition_id, event_name, event_distance, m1_score, m2_score, total_score, total_10X, total_109
        FROM (
            SELECT competition_id, event_name, event_distance, m1_score, m2_score, total_score, total_10X, total_109
            FROM local_comp_scores
            WHERE mareos_id = ? AND competition_id IN ($placeholders)
            UNION ALL
            SELECT competition_id, event_name, event_distance, m1_score, m2_score, total_score, total_10X, total_109
            FROM international_comp_scores
            WHERE mareos_id = ? AND competition_id IN ($placeholders)
        ) AS combined_scores
        ORDER BY competition_id
    ";

    $params = array_merge([$mareos_id], $selected_competitions, [$mareos_id], $selected_competitions);
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
