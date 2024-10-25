<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// First determine if viewing competition, training or compare
$isViewingCompetition = !isset($_GET['type']) || $_GET['type'] === 'competition';
$isViewingTraining = isset($_GET['type']) && $_GET['type'] === 'training';
$isViewingCompare = isset($_GET['type']) && $_GET['type'] === 'compare';

// Then determine which specific view within competition/training
$isViewingLocalStats = isset($_GET['view']) && $_GET['view'] === 'local';
$isViewingInternationalStats = isset($_GET['view']) && $_GET['view'] === 'international';
$isViewingTeamStats = isset($_GET['view']) && $_GET['view'] === 'team';
$isViewingPersonalStats = isset($_GET['view']) && $_GET['view'] === 'personal';

// Set default views if none selected
if ($isViewingCompetition && !isset($_GET['view'])) {
    $isViewingLocalStats = true;
    $_SESSION['view'] = 'local';
}

if ($isViewingTraining && !isset($_GET['view'])) {
    $isViewingTeamStats = true;
    $_SESSION['view'] = 'team'; 
}

// Switch view type if provided in GET request
if (isset($_GET['view'])) {
    $_SESSION['view'] = $_GET['view'];
}
$view = $_SESSION['view'] ?? 'local'; // Set default view to 'local' if not set

// Set title based on current view
if ($isViewingCompetition) {
    if ($isViewingLocalStats) {
        $title = 'Local Competition Statistics';
    } else {
        $title = 'International Competition Statistics';
    }
} elseif ($isViewingTraining) {
    if ($isViewingTeamStats) {
        $title = 'Team Training Statistics';
    } else {
        $title = 'Personal Training Statistics';
    }
} else {
    $title = 'Compare Statistics';
}
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0"><?php echo $title; ?></h3>
        </div>
    </div>

    <!-- View Switch -->
    <?php if ($isViewingCompetition): ?>
        <div class="d-flex justify-content-center mb-5">
            <div class="btn-group btn-group-responsive">
                <a href="?type=competition&view=local" class="btn <?php echo ($view === 'local') ? 'btn-primary' : 'btn-outline-primary'; ?>">Local</a>
                <a href="?type=competition&view=international" class="btn <?php echo ($view === 'international') ? 'btn-primary' : 'btn-outline-primary'; ?>">International</a>
            </div>
        </div>
    <?php elseif ($isViewingTraining): ?>
        <div class="d-flex justify-content-center mb-5">
            <div class="btn-group btn-group-responsive">
                <a href="?type=training&view=team" class="btn <?php echo ($view === 'team') ? 'btn-primary' : 'btn-outline-primary'; ?>">Team</a>
                <a href="?type=training&view=personal" class="btn <?php echo ($view === 'personal') ? 'btn-primary' : 'btn-outline-primary'; ?>">Personal</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Content -->
    <?php if ($isViewingCompetition): ?>
        <?php include 'statisticView.php'; ?>
    <?php elseif ($isViewingTraining): ?>
        <?php include 'statisticView.php'; ?>
    <?php else: ?>
        <?php include 'statisticView.php'; ?>
    <?php endif; ?>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>
