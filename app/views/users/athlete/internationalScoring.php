<?php
require_once __DIR__ . '/../../../handlers/InternationalScoringHandler.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id']; 

// Fetch international competition
$stmt = $pdo->prepare('
    SELECT s.score_id, s.m_1_score, s.1_10, s.1_9, s.m_2_score, s.2_10, s.2_9, s.total_10, s.total_9, s.total_score, s.event_name, s.event_distance, s.competition_id, s.competition_name, s.created_at
    FROM international_comp_scores s
    WHERE s.user_id = ? 
    ORDER BY s.created_at DESC
');
$stmt->execute([$user_id]);
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$editMode = false;
$editScore = null;

if (isset($_GET['edit'])) {
    $editMode = true;
    $score_id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM international_comp_scores WHERE score_id = ? AND user_id = ?');
    $stmt->execute([$score_id, $user_id]);
    $editScore = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$editScore) {
        $_SESSION['error'] = 'Score not found or permission denied.';
        header('Location: ' . BASE_URL . 'app/views/users/athlete/compScoring.php.php');
        exit();
    }
}
?>

<!-- Success and Error Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success'];
                                                                            unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error'];
                                                            unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Accordion for Add/Edit Score Form -->
<div class="accordion mb-3" style="max-width: 600px;">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button <?php echo $editMode ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="true" aria-controls="collapseForm">
                <?php echo $editMode ? 'Edit Score' : 'Add New Score'; ?>
            </button>
        </h2>
        <div id="collapseForm" class="accordion-collapse collapse <?php echo $editMode ? 'show' : ''; ?>">
            <div class="accordion-body">
                <form action="" method="POST">
                    <div class="row">
                        <!-- Hidden input for score_id in edit mode -->
                        <?php if ($editMode): ?>
                            <input type="hidden" name="score_id" value="<?php echo htmlspecialchars($editScore['score_id']); ?>">
                        <?php endif; ?>

                        <!-- Competition Name Input -->
                        <div class="col-md-12 mb-3">
                            <label for="competition_name" class="form-label">Competition Name</label>
                            <input type="text" class="form-control" id="competition_name" name="competition_name" value="<?php echo $editMode ? htmlspecialchars($editScore['competition_name']) : ''; ?>" required>
                        </div>

                        <!-- Competition ID Input -->
                        <div class="col-md-4 mb-3">
                            <label for="competition_id" class="form-label">Competition ID</label>
                            <input type="text" class="form-control" id="competition_id" name="competition_id" value="<?php echo $editMode ? htmlspecialchars($editScore['competition_id']) : ''; ?>" required>
                        </div>

                        <!-- Event Name Input -->
                        <div class="col-md-4 mb-3">
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" placeholder="exp:Recurve U12"value="<?php echo $editMode ? htmlspecialchars($editScore['event_name']) : ''; ?>" required>
                        </div>

                        <!-- Event Distance Input -->
                        <div class="col-md-4 mb-3">
                            <label for="event_distance" class="form-label">Distance (M)</label>
                            <input type="number" class="form-control" id="event_distance" name="event_distance" placeholder="exp:50" value="<?php echo $editMode ? htmlspecialchars($editScore['event_distance']) : ''; ?>" required>
                        </div>

                        <!-- First Round Score -->
                        <div class="col-md-4 mb-3">
                            <label for="m_1_score" class="form-label">M-1 Score</label>
                            <input type="number" class="form-control" id="m_1_score" name="m_1_score" max="500" value="<?php echo $editMode ? htmlspecialchars($editScore['m_1_score']) : ''; ?>" required>
                        </div>

                        <!-- First Round 10s -->
                        <div class="col-md-4 mb-3">
                            <label for="1_10" class="form-label">M-1 10+X</label>
                            <input type="number" class="form-control" id="1_10" name="1_10" max="50" value="<?php echo $editMode ? htmlspecialchars($editScore['1_10']) : ''; ?>" required>
                        </div>

                        <!-- First Round 9s -->
                        <div class="col-md-4 mb-3">
                            <label for="1_9" class="form-label">M-1 10/9</label>
                            <input type="number" class="form-control" id="1_9" name="1_9" max="50" value="<?php echo $editMode ? htmlspecialchars($editScore['1_9']) : ''; ?>" required>
                        </div>

                        <!-- Second Round Score -->
                        <div class="col-md-4 mb-3">
                            <label for="m_2_score" class="form-label">M-2 Score</label>
                            <input type="number" class="form-control" id="m_2_score" name="m_2_score" max="500" value="<?php echo $editMode ? htmlspecialchars($editScore['m_2_score']) : ''; ?>" required>
                        </div>

                        <!-- Second Round 10s -->
                        <div class="col-md-4 mb-3">
                            <label for="2_10" class="form-label">M-2 10+X</label>
                            <input type="number" class="form-control" id="2_10" name="2_10" max="50" value="<?php echo $editMode ? htmlspecialchars($editScore['2_10']) : ''; ?>" required>
                        </div>

                        <!-- Second Round 9s -->
                        <div class="col-md-4 mb-3">
                            <label for="2_9" class="form-label">M-2 10/9</label>
                            <input type="number" class="form-control" id="2_9" name="2_9" max="50" value="<?php echo $editMode ? htmlspecialchars($editScore['2_9']) : ''; ?>" required>
                        </div>

                        <!-- Total Score -->
                        <div class="col-md-4 mb-3">
                            <label for="total_score" class="form-label">Total Score</label>
                            <input type="number" class="form-control" id="total_score" name="total_score" value="<?php echo $editMode ? htmlspecialchars($editScore['total_score']) : ''; ?>" readonly required>
                        </div>

                        <!-- Total 10s -->
                        <div class="col-md-4 mb-3">
                            <label for="total_10" class="form-label">Total 10+X</label>
                            <input type="number" class="form-control" id="total_10" name="total_10" max="100" value="<?php echo $editMode ? htmlspecialchars($editScore['total_10']) : ''; ?>" readonly required>
                        </div>

                        <!-- Total 9s -->
                        <div class="col-md-4 mb-3">
                            <label for="total_9" class="form-label">Total 10/9</label>
                            <input type="number" class="form-control" id="total_9" name="total_9" max="100" value="<?php echo $editMode ? htmlspecialchars($editScore['total_9']) : ''; ?>" readonly required>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary mb-2 mb-md-0">
                                <i class="bi bi-save me-2"></i><?php echo $editMode ? 'Update Score' : 'Submit Score'; ?>
                            </button>
                            <?php if ($editMode): ?>
                                <a href="compScoring.php?view=international" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Table displaying competition scores -->
<div class="table-responsive mb-2">
    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>Competition ID</th>
                <th>Competition Name</th>
                <th>Event Name</th>
                <th>Event Distance</th>
                <th>M-1 Score</th>
                <th>M-1 10+X</th>
                <th>M-1 10/9</th>
                <th>M-2 Score</th>
                <th>M-2 10+X</th>
                <th>M-2 10/9</th>
                <th>Total Score</th>
                <th>Total 10+X</th>
                <th>Total 10/9</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($scores)): ?>
                <?php foreach ($scores as $index => $score): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($score['competition_id']); ?></td>
                        <td><?php echo htmlspecialchars($score['competition_name']); ?></td>
                        <td><?php echo htmlspecialchars($score['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($score['event_distance']); ?>m</td>
                        <td class="table-primary"><?php echo htmlspecialchars($score['m_1_score']); ?></td>
                        <td class="table-primary"><?php echo htmlspecialchars($score['1_10']); ?></td>
                        <td class="table-primary"><?php echo htmlspecialchars($score['1_9']); ?></td>
                        <td class="table-info"><?php echo htmlspecialchars($score['m_2_score']); ?></td>
                        <td class="table-info"><?php echo htmlspecialchars($score['2_10']); ?></td>
                        <td class="table-info"><?php echo htmlspecialchars($score['2_9']); ?></td>
                        <td><?php echo htmlspecialchars($score['total_score']); ?></td>
                        <td><?php echo htmlspecialchars($score['total_10']); ?></td>
                        <td><?php echo htmlspecialchars($score['total_9']); ?></td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?view=international&edit=<?php echo htmlspecialchars($score['score_id']); ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="?view=international&delete=<?php echo htmlspecialchars($score['score_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this score?');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No competition scores found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Automatically calculate the total score, total 10, and total 9
    document.addEventListener("DOMContentLoaded", function() {
        var m1ScoreInput = document.getElementById('m_1_score');
        var m2ScoreInput = document.getElementById('m_2_score');
        var m1_10Input = document.getElementById('1_10');
        var m2_10Input = document.getElementById('2_10');
        var m1_9Input = document.getElementById('1_9');
        var m2_9Input = document.getElementById('2_9');
        var totalScoreInput = document.getElementById('total_score');
        var total10Input = document.getElementById('total_10');
        var total9Input = document.getElementById('total_9');

        function updateTotals() {
            var m1Score = parseFloat(m1ScoreInput.value) || 0;
            var m2Score = parseFloat(m2ScoreInput.value) || 0;
            var m1_10 = parseInt(m1_10Input.value) || 0;
            var m2_10 = parseInt(m2_10Input.value) || 0;
            var m1_9 = parseInt(m1_9Input.value) || 0;
            var m2_9 = parseInt(m2_9Input.value) || 0;

            totalScoreInput.value = m1Score + m2Score;
            total10Input.value = m1_10 + m2_10;
            total9Input.value = m1_9 + m2_9;
        }

        [m1ScoreInput, m2ScoreInput, m1_10Input, m2_10Input, m1_9Input, m2_9Input].forEach(input => {
            input.addEventListener('input', updateTotals);
        });
    });
</script>


