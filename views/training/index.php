<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Check if the user is logged in and has an appropriate role
$isLoggedIn = isset($_SESSION['user_id']);
$isAdminOrCoach = $isLoggedIn && (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'coach'));

// Fetch all training sessions
$stmt = $pdo->prepare('SELECT trainings.*, users.username FROM trainings JOIN users ON trainings.added_by = users.user_id');
$stmt->execute();
$trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Sessions - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<style>
    body {
        background-color: #f8f9fa;
    }
    .container.table {
        background-color: #f8f9fa; /* Lighter gray for the main content area */
        height: 100vh;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Adding a subtle shadow for depth */
    }
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .header-container h2 {
        margin: 0;
    }
</style>

<body>
    <!-- Navbar -->
    <?php include __DIR__ . '/../template/header.php'; ?>

    <div class="container table mt-5">
        <div class="header-container">
            <h2 class="text-center mb-0">Training Sessions</h2>
            <?php if ($isAdminOrCoach): ?>
                <a href="create.php" class="btn btn-primary">Add New Training Session</a>
            <?php endif; ?>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Session Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Added By</th>
                    <?php if ($isAdminOrCoach): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainings as $training) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($training['training_name']); ?></td>
                        <td><?php echo htmlspecialchars($training['date']); ?></td>
                        <td><?php echo htmlspecialchars($training['location']); ?></td>
                        <td><?php echo htmlspecialchars($training['description']); ?></td>
                        <td><?php echo htmlspecialchars($training['username']); ?></td>
                        <?php if ($isAdminOrCoach): ?>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit.php?id=<?php echo $training['training_id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?php echo $training['training_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this training?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
