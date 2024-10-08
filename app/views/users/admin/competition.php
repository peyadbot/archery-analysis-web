<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../../config/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

$competitions = [];
try {
    $json = file_get_contents('https://ianseo.sukanfc.com/fetch_tournaments.php'); 
    $competitions = json_decode($json, true);
} catch (Exception $e) {
    $error = 'Failed to fetch competitions: ' . $e->getMessage();
}

?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">All Competitions</h3>
        </div>
    </div>

    <!-- Show error message if fetching failed -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Competitions Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Competition Code</th>
                    <th>Competition Name</th>
                    <th>Location</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($competitions)): ?>
                    <?php foreach ($competitions as $index => $competition): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($competition['ToCode']); ?></td>
                            <td><?php echo htmlspecialchars($competition['ToName']); ?></td>
                            <td><?php echo htmlspecialchars($competition['ToVenue'] . ', ' . $competition['ToWhere']); ?></td>
                            <td><?php echo htmlspecialchars($competition['ToWhenFrom']); ?></td>
                            <td><?php echo htmlspecialchars($competition['ToWhenTo']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No competitions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../layouts/dashboard/footer.php'; ?>
