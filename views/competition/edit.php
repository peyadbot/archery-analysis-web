<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and has the role of coach or admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'coach' && $_SESSION['role'] !== 'admin')) {
    header('Location: ../login.php');
    exit();
}

$competition_id = $_GET['id'] ?? null;

// Validate competition_id
if (!$competition_id || !is_numeric($competition_id)) {
    echo '<div class="alert alert-danger">Invalid competition ID: ' . htmlspecialchars($competition_id) . '</div>';
    exit();
}

// Fetch competition details
$stmt = $pdo->prepare('SELECT * FROM competitions WHERE competition_id = ?');
$stmt->execute([$competition_id]);
$competition = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$competition) {
    echo '<div class="alert alert-danger">Competition not found.</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_name = $_POST['competition_name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // Update competition details
    $stmt = $pdo->prepare('UPDATE competitions SET competition_name = ?, date = ?, location = ?, description = ? WHERE competition_id = ?');
    $stmt->execute([$competition_name, $date, $location, $description, $competition_id]);

    $success = 'Competition updated successfully!';
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Competition - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Competition</h2>
        <?php if (isset($success)) : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="competition_name" class="form-label">Competition Name</label>
                <input type="text" class="form-control" id="competition_name" name="competition_name" value="<?php echo htmlspecialchars($competition['competition_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($competition['date']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($competition['location']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($competition['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Competition</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
