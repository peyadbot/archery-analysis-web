<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

// Check if the user is logged in and has the coach role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

// Fetch dashboard data based on user role
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$profile_incomplete = empty($profile['name']) || empty($profile['ic_number']) || empty($profile['email']) || empty($profile['phone_number']);

// Get data
try {
    $dashboardData = getDashboardData($userId, $role);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
$latestCompetitions = $dashboardData['latestCompetitions'];  
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h3 class="m-0">Coach Dashboard</h3>
            <div id="clock-container" class="text-end">
                <p id="clock-time" class="mb-0"></p>
                <p id="clock-date" class="mb-0"></p>
            </div>
        </div>
        <?php if (isset($_SESSION['impersonating']) && $_SESSION['impersonating'] === true): ?>
            <form method="POST" action="../../../handlers/ImpersonateStopHandler.php" class="pt-4">
                <button type="submit" class="btn btn-warning w-100">
                    <i class="bi bi-arrow-left-circle"></i> Return to Account
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Profile Incomplete Warning -->
    <?php if ($profile_incomplete): ?>
        <div class="alert alert-warning">
            <h4 class="text-danger">Profile Incomplete</h4>
            <p>Your profile is incomplete. Please complete your profile to access the dashboard features.</p>
            <a href="<?php echo BASE_URL . 'app/views/profiles/profile.php'; ?>" class="btn btn-primary">Complete Profile</a>
        </div>
    <?php else: ?>

        <!-- Dashboard Overview -->
        <div class="row gy-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg text-center">
                    <div class="card-body">
                        <i class="bi bi-graph-up-arrow text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title">Competitions</h5>
                        <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['competitionCount']); ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="#" class="btn btn-outline-primary">View Competitions</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg text-center">
                    <div class="card-body">
                        <i class="bi bi-person-check text-success mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title">Trainings</h5>
                        <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['trainingCount']); ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="#" class="btn btn-outline-success">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg text-center">
                    <div class="card-body">
                        <i class="bi bi-people-fill text-info mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title">Athletes</h5>
                        <h2 class="display-4"><?php echo htmlspecialchars($dashboardData['athleteCount']); ?></h2>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="<?php echo BASE_URL . 'app/views/users/coach/manage-athletes.php'; ?>" class="btn btn-outline-info">Manage Athletes</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <!-- Profile Section -->
            <div class="col-lg-4 d-flex flex-column">
                <div class="card mb-5 flex-grow-1 shadow-lg d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-header bg-dark text-white">
                            <h4 class="mb-0">Profile</h4>
                        </div>
                        <div class="card-body flex-grow-1">
                            <div class="profile-info">
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['name']); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone_number']); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>IC Number:</strong> <?php echo htmlspecialchars($profile['ic_number']); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Passport:</strong> <?php echo htmlspecialchars($profile['passport_number']); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Address:</strong> <?php echo htmlspecialchars($profile['home_address']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Edit Profile Button -->
                    <div class="text-end mt-4 p-3">
                        <a href="<?php echo BASE_URL . 'app/handlers/AthleteReportHandler.php'; ?>" class="btn btn-sm btn-outline-success">Download Report</a>
                        <a href="<?php echo BASE_URL . 'app/views/profiles/profile.php'; ?>" class="btn btn-sm btn-outline-primary w-20">Edit Profile</a>
                    </div>
                </div>
            </div>

            <!-- Latest Competitions Section -->
            <div class="col-lg-8 d-flex flex-column">
                <div class="card shadow-lg mb-5">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Latest Competitions</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($latestCompetitions)): ?>
                                <?php foreach ($latestCompetitions as $competition): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <h6 class="fw-bold"><?php echo htmlspecialchars($competition['ToName']); ?></h6>
                                            <small class="text-muted">Code: <?php echo htmlspecialchars($competition['ToCode']); ?></small><br>
                                            <small class="text-muted">Location: <?php echo htmlspecialchars($competition['ToWhere']); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary rounded-pill">
                                                From: <?php echo htmlspecialchars($competition['ToWhenFrom']); ?>
                                            </span><br>
                                            <span class="badge bg-secondary rounded-pill">
                                                To: <?php echo htmlspecialchars($competition['ToWhenTo']); ?>
                                            </span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">No competitions available at the moment.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Clock
    function updateClock() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const formattedTime = `${hours}:${minutes}:${seconds} ${ampm}`;
        const formattedDate = now.toLocaleDateString('en-US', {
            year: 'numeric', 
            month: 'long', 
            day: 'numeric'
        });

        document.getElementById('clock-time').textContent = formattedTime;
        document.getElementById('clock-date').textContent = formattedDate;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>
