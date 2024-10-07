<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/CoachAthleteHandler.php';

// Ensure the user is logged in and is a coach
if ($_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$coach_id = $_SESSION['user_id'];

// Fetch athletes under the coach from the database
try {
    $stmt = $pdo->prepare('
        SELECT u.user_id, u.username, p.name, ad.mareos_id, ca.start_date, ca.end_date, ca.updated_at
        FROM coach_athlete ca
        JOIN users u ON ca.athlete_user_id = u.user_id
        JOIN profiles p ON p.user_id = u.user_id
        JOIN athlete_details ad ON ad.user_id = u.user_id
        WHERE ca.coach_user_id = :coach_id
    ');
    $stmt->bindParam(':coach_id', $coach_id);
    $stmt->execute();
    $athletes = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to retrieve athletes: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Manage Your Athletes</h3>
        </div>
    </div>

    <!-- Success and Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success'];
                                                                                unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error'];
                                                                unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="accordion mb-4">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    Add New Athlete by Mareos ID
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <!-- Add Athlete Form -->
                    <form method="POST" action="../../../handlers/CoachAthleteHandler.php" id="addAthleteForm">
                        <div class="mb-3">
                            <label for="mareos_id" class="form-label">Mareos ID</label>
                            <input type="text" name="mareos_id" class="form-control" id="mareos_id" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Athlete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Athletes Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>Mareos ID</th>
                <th>Name</th>
                <th>Start Date</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($athletes)): ?>
                <?php foreach ($athletes as $index => $athlete): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($athlete['mareos_id']); ?></td>
                        <td><?php echo htmlspecialchars($athlete['name']); ?></td>
                        <td><?php echo htmlspecialchars($athlete['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($athlete['updated_at']); ?></td>
                        <td>
                            <!-- Impersonate Athlete -->
                            <form method="POST" action="../../../handlers/ImpersonateAthleteHandler.php" style="display:inline;">
                                <input type="hidden" name="athlete_user_id" value="<?php echo htmlspecialchars($athlete['user_id']); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Access Athlete Dashboard</button>
                            </form>
                            <!-- Download Report -->
                            <a href="<?php echo BASE_URL . 'app/handlers/AthleteReportHandler.php?athlete_user_id=' . $athlete['user_id']; ?>" class="btn btn-success btn-sm">
                                Download Report
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No athletes found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>
