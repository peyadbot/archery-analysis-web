<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and has the role of coach or admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'coach' && $_SESSION['role'] !== 'admin')) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_name = $_POST['competition_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $added_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO competitions (competition_name, start_date, end_date, location, description, added_by) VALUES (?, ?, ?, ?, ?,?)');
    $stmt->execute([$competition_name, $start_date, $end_date, $location, $description, $added_by]);

    if ($_SESSION['role'] === 'coach') {
        $competition_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO competition_approval_requests (competition_id, coach_id) VALUES (?, ?)');
        $stmt->execute([$competition_id, $added_by]);
    }

    $success = 'Competition added successfully!';
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Competition - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add New Competition</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="competition_name" class="form-label">Competition Name</label>
                <input type="text" class="form-control" id="competition_name" name="competition_name" required>
            </div>
            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Competition</button>
        </form>
    </div>

    <script>
        // Start & End date form selector configuration
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // Set min date for start_date to today
            const today = new Date().toISOString().split('T')[0];
            startDateInput.setAttribute('min', today);

            // Update the end_date min value based on start_date
            startDateInput.addEventListener('change', function() {
                const startDate = new Date(startDateInput.value).toISOString().split('T')[0];
                endDateInput.setAttribute('min', startDate);
                // Clear end_date if it's less than the new start_date
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = '';
                }
            });

            // Validate end_date
            endDateInput.addEventListener('change', function() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (endDate < startDate) {
                    alert('End date must be after the start date.');
                    endDateInput.value = '';
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
