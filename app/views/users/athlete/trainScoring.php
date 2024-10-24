<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$view = $_GET['view'] ?? 'team';
$isViewingTeam = $view === 'team';
$isViewingPersonal = $view === 'personal';

if ($isViewingTeam) {
    $title = 'Team Training Scoring';
} else {
    $title = 'Personal Training Scoring';
}
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <?php if ($isViewingTeam): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">Team Scoring</h3>
            </div>
        </div>
    <?php elseif ($isViewingPersonal): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">Personal Scoring</h3>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-center mb-4">
        <div class="btn-group">
            <!-- View Team Trainings Button -->
            <a href="?view=team" class="btn <?php echo ($view === 'team') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                Team Trainings
            </a>

            <!-- View Personal Trainings Button -->
            <a href="?view=personal" class="btn <?php echo ($view === 'personal') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                Personal Trainings
            </a>
        </div>
    </div>

    <?php if ($isViewingTeam): ?>
        <?php include 'teamScoring.php'; ?>
    <?php elseif ($isViewingPersonal): ?>
        <?php include 'personalScoring.php'; ?>
    <?php endif; ?>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>