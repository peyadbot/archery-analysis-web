<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$isViewingLocal = isset($_GET['view']) && $_GET['view'] === 'local';
$isViewingInternational = isset($_GET['view']) && $_GET['view'] === 'international';
$isViewingStats = isset($_GET['view']) && $_GET['view'] === 'stats';
$isViewingCompare = isset($_GET['view']) && $_GET['view'] === 'compare';

if (!$isViewingLocal && !$isViewingInternational && !$isViewingStats && !$isViewingCompare) {
    $isViewingLocal = true;
}

$view = isset($_GET['view']) ? $_GET['view'] : 'local';
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <?php if ($isViewingLocal): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">Local Scoring</h3>
            </div>
        </div>
    <?php elseif ($isViewingInternational): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">International Scoring</h3>
            </div>
        </div>
    <?php elseif ($isViewingStats): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">Competition Statistics</h3>
            </div>
        </div>
    <?php elseif ($isViewingCompare): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">Compare Competitions</h3>
            </div>
        </div>
    <?php endif; ?>

    <!-- Competition Type Switch -->
    <div class="d-flex justify-content-center mb-5">
        <div class="btn-group">
            <a href="?view=local" class="btn <?php echo ($view === 'local') ? 'btn-primary' : 'btn-outline-primary'; ?>">Local Competitions</a>
            <a href="?view=international" class="btn <?php echo ($view === 'international') ? 'btn-primary' : 'btn-outline-primary'; ?>">International Competitions</a>
            <a href="?view=stats" class="btn <?php echo ($view === 'stats') ? 'btn-primary' : 'btn-outline-primary'; ?>">Competition Statistics</a>
            <a href="?view=compare" class="btn <?php echo ($view === 'compare') ? 'btn-primary' : 'btn-outline-primary'; ?>">Compare Competitions</a>
        </div>
    </div>

    <?php if ($isViewingLocal): ?>
        <?php include 'localScoring.php'; ?>
    <?php elseif ($isViewingInternational): ?>
        <?php include 'internationalScoring.php'; ?>
    <?php elseif ($isViewingStats): ?>
        <?php include 'statCompetition.php'; ?>
    <?php elseif ($isViewingCompare): ?>
        <?php include 'compCompare.php'; ?>
    <?php endif; ?>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>
