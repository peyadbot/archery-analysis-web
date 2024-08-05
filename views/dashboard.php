<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Archery Stats</title>
    <link rel="stylesheet" href="/path/to/your/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
