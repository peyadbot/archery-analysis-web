<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../handlers/SessionExpiryHandler.php';
checkSessionTimeout();

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

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_id = $_POST['competition_id'];
    $score_50m_1 = $_POST['score_50m_1'];
    $score_50m_2 = $_POST['score_50m_2'];
    $total_score = $_POST['total_score'];
    $tens_and_x = $_POST['tens_and_x'];
    $x_count = $_POST['x_count'];

    // Insert the scores into the database
    try {
        $stmt = $pdo->prepare('INSERT INTO scores (athlete_id, competition_id, score_50m_1, score_50m_2, total_score, tens_and_x, x_count) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$athlete_id, $competition_id, $score_50m_1, $score_50m_2, $total_score, $tens_and_x, $x_count]);
        
        $_SESSION['success'] = 'Score added successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to add score: ' . $e->getMessage();
    }

    // Refresh the page to avoid form resubmission
    header('Location: ' . BASE_URL . 'app/views/users/athlete/inputScoreComp.php');
    exit();
}

// Fetch competitions for the dropdown
$stmt = $pdo->prepare('SELECT competition_id, competition_name FROM competitions');
$stmt->execute();
$competitions = $stmt->fetchAll();
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Input Competition Scores</h3>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Form to input scores -->
    <div class="card shadow-sm p-4 mb-5">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="competition_id" class="form-label">Competition</label>
                        <select class="form-control" id="competition_id" name="competition_id" required>
                            <option value="">Select Competition</option>
                            <?php foreach ($competitions as $competition): ?>
                                <option value="<?php echo $competition['competition_id']; ?>"><?php echo htmlspecialchars($competition['competition_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="score_50m_1" class="form-label">50m-1 Score</label>
                        <input type="number" class="form-control" id="score_50m_1" name="score_50m_1" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="score_50m_2" class="form-label">50m-2 Score</label>
                        <input type="number" class="form-control" id="score_50m_2" name="score_50m_2" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="total_score" class="form-label">Total Score</label>
                        <input type="number" class="form-control" id="total_score" name="total_score" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tens_and_x" class="form-label">10+X Count</label>
                        <input type="number" class="form-control" id="tens_and_x" name="tens_and_x" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="x_count" class="form-label">X Count</label>
                        <input type="number" class="form-control" id="x_count" name="x_count" required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Submit Scores</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>
