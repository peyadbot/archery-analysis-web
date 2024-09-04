<?php
require_once __DIR__ . '/../../../app/handlers/TrainingHandler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training - Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<style>
    /* Custom Styles */
    body {
        background-color: #f8f9fa;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .table-responsive {
        margin-top: 20px;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
</style>
<body>
    <div class="container table mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h3>All Competitions</h3>
            <form class="d-flex search-bar">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success me-2" type="submit">Search</button>
                <a class="btn btn-primary w-50" href="training-form.php" role="button">Add New</a>
            </form>
        </div>
        
        <!-- Display Success and Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
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
                    <?php if (!empty($trainings)): ?>
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
                                            <a href="training-form.php?edit=<?php echo htmlspecialchars($training['training_id']); ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="training.php?delete=<?php echo htmlspecialchars($training['training_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this training?');">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No training found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/index.php'; ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hide success and error messages
        setTimeout(function() {
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 2000);
    </script>
</body>

</html>