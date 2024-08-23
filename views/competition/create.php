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
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $added_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO competitions (competition_name, date, location, description, added_by) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$competition_name, $date, $location, $description, $added_by]);

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
            <button type="submit" class="btn btn-primary w-100">Add Competition</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
