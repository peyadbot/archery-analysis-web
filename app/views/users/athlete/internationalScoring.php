<?php
require_once __DIR__ . '/../../../handlers/InternationalScoringHandler.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id']; 

// Fetch international competition
$stmt = $pdo->prepare('
    SELECT s.score_id, s.m1_score, s.m1_10X, s.m1_109, s.m2_score, s.m2_10X, s.m2_109, 
        s.total_score, s.total_10X, s.total_109, s.event_name, s.event_distance, 
        s.competition_id, s.competition_name, s.created_at
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
                            <label for="m1_score" class="form-label">M-1 Score</label>
                            <input type="number" class="form-control" id="m1_score" name="m1_score" max="360" value="<?php echo $editMode ? htmlspecialchars($editScore['m1_score']) : ''; ?>" required>
                        </div>

                        <!-- First Round 10+X -->
                        <div class="col-md-4 mb-3">
                            <label for="m1_10X" class="form-label">M-1 10+X</label>
                            <input type="number" class="form-control" id="m1_10X" name="m1_10X" max="36" value="<?php echo $editMode ? htmlspecialchars($editScore['m1_10X']) : ''; ?>" required>
                        </div>

                        <!-- First Round 10/9 -->
                        <div class="col-md-4 mb-3">
                            <label for="m1_109" class="form-label">M-1 10/9</label>
                            <input type="number" class="form-control" id="m1_109" name="m1_109" max="36" value="<?php echo $editMode ? htmlspecialchars($editScore['m1_109']) : ''; ?>" required>
                        </div>

                        <!-- Second Round Score -->
                        <div class="col-md-4 mb-3">
                            <label for="m2_score" class="form-label">M-2 Score</label>
                            <input type="number" class="form-control" id="m2_score" name="m2_score" max="360" value="<?php echo $editMode ? htmlspecialchars($editScore['m2_score']) : ''; ?>" required>
                        </div>

                        <!-- Second Round 10+X -->
                        <div class="col-md-4 mb-3">
                            <label for="m2_10X" class="form-label">M-2 10+X</label>
                            <input type="number" class="form-control" id="m2_10X" name="m2_10X" max="36" value="<?php echo $editMode ? htmlspecialchars($editScore['m2_10X']) : ''; ?>" required>
                        </div>

                        <!-- Second Round 10/9 -->
                        <div class="col-md-4 mb-3">
                            <label for="m2_109" class="form-label">M-2 10/9</label>
                            <input type="number" class="form-control" id="m2_109" name="m2_109" max="36" value="<?php echo $editMode ? htmlspecialchars($editScore['m2_109']) : ''; ?>" required>
                        </div>

                        <!-- Total Score -->
                        <div class="col-md-4 mb-3">
                            <label for="total_score" class="form-label">Total Score</label>
                            <input type="number" class="form-control" id="total_score" name="total_score" max="720" value="<?php echo $editMode ? htmlspecialchars($editScore['total_score']) : ''; ?>" readonly required>
                        </div>

                        <!-- Total 10+X -->
                        <div class="col-md-4 mb-3">
                            <label for="total_10X" class="form-label">Total 10+X</label>
                            <input type="number" class="form-control" id="total_10X" name="total_10X" max="72" value="<?php echo $editMode ? htmlspecialchars($editScore['total_10X']) : ''; ?>" readonly required>
                        </div>

                        <!-- Total 10/9 -->
                        <div class="col-md-4 mb-3">
                            <label for="total_109" class="form-label">Total 10/9</label>
                            <input type="number" class="form-control" id="total_109" name="total_109" max="72" value="<?php echo $editMode ? htmlspecialchars($editScore['total_109']) : ''; ?>" readonly required>
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
    <table class="table table-bordered table-striped table-hover shadow-sm">
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
                        <td class="table-primary"><?php echo htmlspecialchars($score['m1_score']); ?></td>
                        <td class="table-primary"><?php echo htmlspecialchars($score['m1_10X']); ?></td>
                        <td class="table-primary"><?php echo htmlspecialchars($score['m1_109']); ?></td>
                        <td class="table-info"><?php echo htmlspecialchars($score['m2_score']); ?></td>
                        <td class="table-info"><?php echo htmlspecialchars($score['m2_10X']); ?></td>
                        <td class="table-info"><?php echo htmlspecialchars($score['m2_109']); ?></td>
                        <td><?php echo htmlspecialchars($score['total_score']); ?></td>
                        <td><?php echo htmlspecialchars($score['total_10X']); ?></td>
                        <td><?php echo htmlspecialchars($score['total_109']); ?></td>
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
    // Automatically calculate the total score, total 10+X, and total 10/9
    document.addEventListener("DOMContentLoaded", function() {
        const m1ScoreInput = document.getElementById('m1_score');
        const m2ScoreInput = document.getElementById('m2_score');
        const m1_10XInput = document.getElementById('m1_10X');
        const m2_10XInput = document.getElementById('m2_10X');
        const m1_109Input = document.getElementById('m1_109');
        const m2_109Input = document.getElementById('m2_109');
        const totalScoreInput = document.getElementById('total_score');
        const total10XInput = document.getElementById('total_10X');
        const total109Input = document.getElementById('total_109');

        function updateTotals() {
            const m1Score = parseFloat(m1ScoreInput.value) || 0;
            const m2Score = parseFloat(m2ScoreInput.value) || 0;
            const m1_10X = parseInt(m1_10XInput.value) || 0;
            const m2_10X = parseInt(m2_10XInput.value) || 0;
            const m1_109 = parseInt(m1_109Input.value) || 0;
            const m2_109 = parseInt(m2_109Input.value) || 0;

            // Calculate totals
            const totalScore = m1Score + m2Score;
            const total10X = m1_10X + m2_10X;
            const total109 = m1_109 + m2_109;

            // Update the total fields
            totalScoreInput.value = totalScore;
            total10XInput.value = total10X;
            total109Input.value = total109;
        }

        // Add event listeners to all input fields that affect the totals
        [m1ScoreInput, m2ScoreInput, m1_10XInput, m2_10XInput, m1_109Input, m2_109Input].forEach(function(input) {
            input.addEventListener('input', updateTotals);
        });
    });
</script>


