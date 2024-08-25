<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Default SQL query
$sql = '
    SELECT competitions.*, users.username 
    FROM competitions 
    JOIN users ON competitions.added_by = users.user_id
    WHERE 1=1
';

$params = []; // Array to store parameters for the query
$orderBy = 'start_date DESC'; // Default: latest first

// Filter
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'oldest') {
        $orderBy = 'start_date ASC';
    } else if ($_GET['sort'] === 'latest') {
        $orderBy = 'start_date DESC';
    }
}

$sql .= " ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params); // Pass the parameters to execute()

$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user is logged in and has an appropriate role
$isLoggedIn = isset($_SESSION['user_id']);
$isAdminOrCoach = $isLoggedIn && (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'coach'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competitions - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .container.main {
            display: flex;
        }
        .filter-sidebar {
            position: sticky;
            top: 50px; 
            width: 350px; 
            height: 200px;
            background-color: #fff; 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }
        .filter-sidebar label {
            font-weight: bold;
        }
        .container.table {
            background-color: #fff; 
            height: 100vh;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); 
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
</head>
<body>
    <!-- Navbar -->
    <?php include __DIR__ . '/../template/header.php'; ?>
    <div class="container main">
        <!-- Filter Form -->
        <div class="filter-sidebar">
            <!-- Filter Form -->
            <form method="GET" action="">
                <div class="mb-3">
                    <label for="sort" class="form-label">Sort by Date</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="latest" <?= (isset($_GET['sort']) && $_GET['sort'] === 'latest') ? 'selected' : '' ?>>Latest</option>
                        <option value="oldest" <?= (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-75">Filter</button>
                    <a href="index.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="container table mt-5">
            <div class="header-container">
                <h2 class="text-center mb-0">Competitions</h2>
                <?php if ($isAdminOrCoach): ?>
                    <a href="create.php" class="btn btn-primary">Add New Competition</a>
                <?php endif; ?>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Competition Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Added By</th>
                        <th>Approved</th>
                        <?php if ($isAdminOrCoach): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($competitions as $competition) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($competition['competition_name']); ?></td>
                            <td><?php echo htmlspecialchars($competition['start_date']) . ' ~ ' . htmlspecialchars($competition['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($competition['location']); ?></td>
                            <td><?php echo htmlspecialchars($competition['description']); ?></td>
                            <td><?php echo htmlspecialchars($competition['username']); ?></td>
                            <td><?php echo $competition['approved'] ? 'Yes' : 'No'; ?></td>
                            <?php if ($isAdminOrCoach): ?>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="edit.php?id=<?php echo htmlspecialchars($competition['competition_id']); ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="delete.php?id=<?php echo htmlspecialchars($competition['competition_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this competition?');">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
