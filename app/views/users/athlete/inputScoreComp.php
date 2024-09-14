<?php
ob_start();
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/ScoringCompetitionHandler.php';

if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch athlete details
$stmt = $pdo->prepare('SELECT athlete_id FROM athlete_details WHERE user_id = ?');
$stmt->execute([$user_id]);
$athlete = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$athlete) {
    $_SESSION['error'] = 'Athlete profile not found. Please complete your profile.';
    header('Location: ' . BASE_URL . 'app/views/profiles/profile.php');
    exit();
}

$athlete_id = $athlete['athlete_id'];

// Fetch athlete info (gender and bow type)
$stmt = $pdo->prepare('SELECT p.gender, a.bow_type FROM profiles p JOIN athlete_details a ON p.user_id = a.user_id WHERE p.user_id = ?');
$stmt->execute([$user_id]);
$athlete_info = $stmt->fetch(PDO::FETCH_ASSOC);

$gender = $athlete_info['gender'];
$bow_type = $athlete_info['bow_type'];

// Fetch all competition scores for the athlete
$stmt = $pdo->prepare('
    SELECT 
        s.score_id, s.competition_id, s.score_50m_1, s.score_50m_2, s.total_score, s.tens_and_x, s.x_count, s.win_status, s.rank, 
        c.competition_name, c.start_date, c.end_date 
    FROM scores s
    JOIN competitions c ON s.competition_id = c.competition_id
    WHERE s.athlete_id = ? 
    ORDER BY c.start_date DESC
');
$stmt->execute([$athlete_id]);
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0"><?php echo $editMode ? 'Edit Score' : 'Add New Score'; ?></h3>
        </div>
    </div>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success'];
                                                                                unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error'];
                                                                unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Form to input/edit scores -->
    <div class="accordion mb-4">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button <?php echo $editMode ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <?php echo $editMode ? 'Edit Scoring' : 'Add New Score'; ?>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse <?php echo $editMode ? 'show' : ''; ?>">
                <div class="accordion-body">
                    <form action="" method="POST">
                        <div class="row">
                            <!-- Competition Code Input -->
                            <div class="col-md-6 mb-3">
                                <label for="competition_code" class="form-label">Competition Code</label>
                                <input type="text" class="form-control" id="competition_code" name="competition_code" required 
                                    value="<?php echo $editMode ? htmlspecialchars($editScore['generated_code']) : ''; ?>" 
                                    <?php echo $editMode ? 'readonly' : ''; ?>>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="score_50m_1" class="form-label">50m-1 Score</label>
                                <input type="number" class="form-control" id="score_50m_1" name="score_50m_1" max="500" required value="<?php echo $editMode ? htmlspecialchars($editScore['score_50m_1']) : ''; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="score_50m_2" class="form-label">50m-2 Score</label>
                                <input type="number" class="form-control" id="score_50m_2" name="score_50m_2" max="500" required value="<?php echo $editMode ? htmlspecialchars($editScore['score_50m_2']) : ''; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="total_score" class="form-label">Total Score</label>
                                <input type="number" class="form-control" id="total_score" name="total_score" required readonly value="<?php echo $editMode ? htmlspecialchars($editScore['total_score']) : ''; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tens_and_x" class="form-label">10+X Count</label>
                                <input type="number" class="form-control" id="tens_and_x" name="tens_and_x" max="100" required value="<?php echo $editMode ? htmlspecialchars($editScore['tens_and_x']) : ''; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="x_count" class="form-label">X Count</label>
                                <input type="number" class="form-control" id="x_count" name="x_count" max="100" required value="<?php echo $editMode ? htmlspecialchars($editScore['x_count']) : ''; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="win_status" class="form-label">Win/Loss (Win: Rank 1-4, Loss: Rank 5 and above)</label>
                                <select class="form-control" id="win_status" name="win_status" required>
                                    <option value="w" <?php echo ($editMode && $editScore['win_status'] === 'w') ? 'selected' : ''; ?>>Win</option>
                                    <option value="l" <?php echo ($editMode && $editScore['win_status'] === 'l') ? 'selected' : ''; ?>>Lose</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="rank" class="form-label">Rank</label>
                                <input type="number" class="form-control" id="rank" name="rank" required min="1" value="<?php echo $editMode ? htmlspecialchars($editScore['rank']) : ''; ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary mb-2 mb-md-0">
                                <i class="bi bi-save me-2"></i><?php echo $editMode ? 'Update Score' : 'Submit Score'; ?>
                            </button>
                            <a href="inputScoreComp.php" class="btn btn-secondary">Cancel</a>
                        </div>

                        <!-- Hidden field to store score_id during edit mode -->
                        <?php if ($editMode): ?>
                            <input type="hidden" name="score_id" value="<?php echo htmlspecialchars($editScore['score_id']); ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table displaying competition scores -->
    <div class="table-responsive mb-2">
        <table class="table table-bordered table-hover shadow-sm">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($scores)): ?>
                    <?php foreach ($scores as $score): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($score['score_id']); ?></td>
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
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="?edit=<?php echo htmlspecialchars($score['score_id']); ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo htmlspecialchars($score['score_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this score?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No competition scores found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Hide success and error messages after 2 seconds
    setTimeout(function() {
        const alertMessage = document.getElementById('alertMessage');
        if (alertMessage) {
            alertMessage.style.display = 'none';
        }
    }, 2000);

    // Calculate the total score based on input for score_50m_1 and score_50m_2
    document.addEventListener("DOMContentLoaded", function() {
        var score50m1 = document.getElementById('score_50m_1');
        var score50m2 = document.getElementById('score_50m_2');
        var totalScore = document.getElementById('total_score');

        function updateTotalScore() {
            var score1 = parseFloat(score50m1.value) || 0;
            var score2 = parseFloat(score50m2.value) || 0;
            totalScore.value = score1 + score2;
        }

        score50m1.addEventListener('input', updateTotalScore);
        score50m2.addEventListener('input', updateTotalScore);
    });

    // Win & Rank validation
    document.addEventListener('DOMContentLoaded', function () {
        const winStatusSelect = document.getElementById('win_status');
        const rankInput = document.getElementById('rank');

        // Function to update rank limits based on win/lose selection
        function updateRankLimits() {
            if (winStatusSelect.value === 'w') {
                rankInput.min = 1;
                rankInput.max = 4;
                rankInput.value = Math.min(rankInput.value, 4); // Adjust value if higher than 4
            } else if (winStatusSelect.value === 'l') {
                rankInput.min = 5;
                rankInput.max = 100; // Or any appropriate higher value
                rankInput.value = Math.max(rankInput.value, 5); // Adjust value if lower than 5
            }
        }

        // Initial update on page load
        updateRankLimits();

        // Update the rank limits whenever win/lose is changed
        winStatusSelect.addEventListener('change', updateRankLimits);
    });
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>