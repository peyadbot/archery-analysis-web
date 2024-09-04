<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit;
}

$success = $error = '';
$editMode = false;
$editCompetition = null;

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_name = $_POST['competition_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $added_by = $_SESSION['user_id'];

    if (isset($_POST['competition_id'])) {
        // Update existing competition
        $competition_id = $_POST['competition_id'];
        try {
            $stmt = $pdo->prepare('UPDATE competitions SET competition_name = ?, start_date = ?, end_date = ?, location = ?, description = ?, added_by = ? WHERE competition_id = ?');
            $stmt->execute([$competition_name, $start_date, $end_date, $location, $description, $added_by, $competition_id]);
            $_SESSION['success'] = 'Competition updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update competition: ' . $e->getMessage();
        }
    } else {
        // Insert new competition
        try {
            $stmt = $pdo->prepare('INSERT INTO competitions (competition_name, start_date, end_date, location, description, added_by) VALUES (?, ?, ?, ?, ?,?)');
            $stmt->execute([$competition_name, $start_date, $end_date, $location, $description, $added_by]);
            $_SESSION['success'] = 'Competition added successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to add competition: ' . $e->getMessage();
        }
    }

    // Redirect to avoid form resubmission
    header('Location: ' . BASE_URL . 'app/views/competition/competition.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $competition_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare('DELETE FROM competitions WHERE competition_id = ?');
        $stmt->execute([$competition_id]);
        $_SESSION['success'] = 'Competition deleted successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete competition: ' . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'app/views/competition/competition.php');
    exit;
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $competition_id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM competitions WHERE competition_id = ?');
    $stmt->execute([$competition_id]);
    $editCompetition = $stmt->fetch();
    if ($editCompetition) {
        $editMode = true;
    }
}

// Fetch all competitions with username
$stmt = $pdo->prepare('
    SELECT competitions.*, users.username
    FROM competitions
    JOIN users ON competitions.added_by = users.user_id
');
$stmt->execute();
$competitions = $stmt->fetchAll();

$isLoggedIn = isset($_SESSION['user_id']);
$isAdminOrCoach = $isLoggedIn && (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'coach'));
