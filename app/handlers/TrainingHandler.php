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
$editTraining = null;

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_name = $_POST['training_name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $added_by = $_SESSION['user_id'];

    if (isset($_POST['training_id'])) {
        // Update existing training
        $training_id = $_POST['training_id'];
        try {
            $stmt = $pdo->prepare('UPDATE trainings SET training_name = ?, date = ?, location = ?, description = ? WHERE training_id = ?');
            $stmt->execute([$training_name, $date, $location, $description, $training_id]);
            $_SESSION['success'] = 'Training updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update training: ' . $e->getMessage();
        }
    } else {
        // Insert new training
        try {
            $stmt = $pdo->prepare('INSERT INTO trainings (training_name, date, location, description, added_by) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$training_name, $date, $location, $description, $added_by]);
            $_SESSION['success'] = 'Training added successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to add training: ' . $e->getMessage();
        }
    }

    // Redirect to avoid form resubmission
    header('Location: ' . BASE_URL . 'app/views/training/training.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $training_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare('DELETE FROM trainings WHERE training_id = ?');
        $stmt->execute([$training_id]);
        $_SESSION['success'] = 'Training deleted successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete training: ' . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'app/views/training/training.php');
    exit;
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $training_id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM trainings WHERE training_id = ?');
    $stmt->execute([$training_id]);
    $editTraining = $stmt->fetch();
    if ($editTraining) {
        $editMode = true;
    }
}

// Fetch all training sessions
$stmt = $pdo->prepare('
    SELECT trainings.*, users.username 
    FROM trainings 
    JOIN users ON trainings.added_by = users.user_id
');
$stmt->execute();
$trainings = $stmt->fetchAll();

$isLoggedIn = isset($_SESSION['user_id']);
$isAdminOrCoach = $isLoggedIn && (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'coach'));
