<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

// Ensure the user is a coach
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

$coach_id = $_SESSION['user_id']; // Get the logged-in coach's ID

// Fetch athletes associated with the coach from coach_athlete table
try {
    $stmt = $pdo->prepare("
        SELECT ad.mareos_id
        FROM athlete_details ad
        JOIN coach_athlete ca ON ad.mareos_id = ca.mareos_id
        WHERE ca.coach_id = ?
    ");
    $stmt->execute([$coach_id]);
    $athletes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error fetching athletes: ' . $e->getMessage();
    exit();
}
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Create a New Training Session</h1>

    <form action="create_training_handler.php" method="POST" class="p-4 shadow bg-light rounded">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="session_name" class="form-label">Session Name</label>
                <input type="text" id="session_name" name="session_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="location" name="location" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="session_date" class="form-label">Date</label>
                <input type="date" id="session_date" name="session_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="num_ends" class="form-label">Number of Ends</label>
                <input type="number" id="num_ends" name="num_ends" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="session_type" class="form-label">Type</label>
                <select id="session_type" name="session_type" class="form-select" required>
                    <option value="Recurve">Recurve</option>
                    <option value="Compound">Compound</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="distance" class="form-label">Distance</label>
                <input type="text" id="distance" name="distance" class="form-control" placeholder="e.g., 50m, 70m" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="game_type" class="form-label">Game Type</label>
                <select id="game_type" name="game_type" class="form-select" required>
                    <option value="Individual">Individual</option>
                    <option value="Team">Team</option>
                    <option value="Mixed">Mixed</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="athletes" class="form-label">Select Athletes (Hold Ctrl to select multiple)</label>
                <select name="athletes[]" id="athletes" class="form-select" multiple required>
                    <?php foreach ($athletes as $athlete): ?>
                        <option value="<?php echo htmlspecialchars($athlete['mareos_id']); ?>">
                            <?php echo htmlspecialchars($athlete['first_name'] . ' ' . $athlete['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Create Training Session</button>
        </div>
    </form>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>
