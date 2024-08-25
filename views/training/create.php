<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and is a coach or admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'coach' && $_SESSION['role'] !== 'admin')) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_name = $_POST['training_name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $added_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO trainings (training_name, date, location, description, added_by) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$training_name, $date, $location, $description, $added_by]);

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Training Session - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add Training Session</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="training_name" class="form-label">Session Name</label>
                <input type="text" class="form-control" id="training_name" name="training_name" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Training Session</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
