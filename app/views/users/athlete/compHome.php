<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$isViewingLocalStats = isset($_GET['view']) && $_GET['view'] === 'local';
$isViewingInternationalStats = isset($_GET['view']) && $_GET['view'] === 'international';
$isViewingCompare = isset($_GET['view']) && $_GET['view'] === 'compare';

if (!$isViewingLocalStats && !$isViewingInternationalStats && !$isViewingCompare) {
    $isViewingLocalStats = true;
}

// Set default competition type if not set
if (!isset($_SESSION['view'])) {
    $_SESSION['view'] = 'local';
}

// Switch competition type if provided in GET request
if (isset($_GET['view'])) {
    $_SESSION['view'] = $_GET['view'];
}

$view = $_SESSION['view'];
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <?php if ($isViewingLocalStats): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">Local Competition Statistics</h3>
            </div>
        </div>
    <?php elseif ($isViewingInternationalStats): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">International Competition Statistics</h3>
            </div>
        </div>
    <?php elseif ($isViewingCompare): ?>
        <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
            <div class="col">
                <h3 class="m-0">Compare Competitions</h3>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main View Switch -->
    <div class="d-flex justify-content-center mb-5">
        <div class="btn-group btn-group-responsive">
            <a href="?view=local" class="btn <?php echo ($_SESSION['view'] == 'local') ? 'btn-primary' : 'btn-outline-primary'; ?>">Local</a>
            <a href="?view=international" class="btn <?php echo ($_SESSION['view'] == 'international') ? 'btn-primary' : 'btn-outline-primary'; ?>">International</a>
            <a href="?view=compare" class="btn <?php echo ($view === 'compare') ? 'btn-primary' : 'btn-outline-primary'; ?>">Compare</a>
        </div>
    </div>

    <?php if ($isViewingLocalStats): ?>
        <?php include 'compStatistic.php'; ?>
    <?php elseif ($isViewingInternationalStats): ?>
        <?php include 'statStatistic.php'; ?>
    <?php elseif ($isViewingCompare): ?>
        <?php include 'compCompare.php'; ?>
    <?php endif; ?>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>
