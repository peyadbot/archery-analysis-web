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
    $location = $_POST['location'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $added_by = $_SESSION['user_id'];

    // Bow type booleans
    $bow_type_recurve = isset($_POST['bow_type_recurve']) ? 1 : 0;
    $bow_type_compound = isset($_POST['bow_type_compound']) ? 1 : 0;
    $bow_type_barebow = isset($_POST['bow_type_barebow']) ? 1 : 0;

    // Event type booleans
    $event_type_individual = isset($_POST['event_type_individual']) ? 1 : 0;
    $event_type_team = isset($_POST['event_type_team']) ? 1 : 0;
    $event_type_mixed_team = isset($_POST['event_type_mixed_team']) ? 1 : 0;

    if (isset($_POST['training_id'])) {
        // Update existing training
        $training_id = $_POST['training_id'];
        try {
            $stmt = $pdo->prepare('
                UPDATE trainings 
                SET training_name = ?, location = ?, description = ?, start_date = ?, end_date = ?, 
                bow_type_recurve = ?, bow_type_compound = ?, bow_type_barebow = ?, 
                event_type_individual = ?, event_type_team = ?, event_type_mixed_team = ?
                WHERE training_id = ?');
            $stmt->execute([$training_name, $location, $description, $start_date, $end_date,
                $bow_type_recurve, $bow_type_compound, $bow_type_barebow, $event_type_individual, $event_type_team,
                $event_type_mixed_team, $training_id]);
            $_SESSION['success'] = 'Training updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update training: ' . $e->getMessage();
        }
    } else {
        // Insert new training
        try {
            $stmt = $pdo->prepare('
                INSERT INTO trainings (training_name, location, description, 
                    start_date, end_date, bow_type_recurve, bow_type_compound, bow_type_barebow, 
                    event_type_individual, event_type_team, event_type_mixed_team, added_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$training_name, $location, $description, $start_date, $end_date,
                $bow_type_recurve, $bow_type_compound, $bow_type_barebow, $event_type_individual, $event_type_team,
                $event_type_mixed_team, $added_by]);
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

function formatTrainingBowTypes($training) {
    $bowTypes = [];
    if ($training['bow_type_recurve']) $bowTypes[] = 'R';
    if ($training['bow_type_compound']) $bowTypes[] = 'C';
    if ($training['bow_type_barebow']) $bowTypes[] = 'BB';
    return implode(', ', $bowTypes);
}

// Function to format event types
function formatTrainingEventTypes($training) {
    $eventTypes = [];
    if ($training['event_type_individual']) $eventTypes[] = 'Ind';
    if ($training['event_type_team']) $eventTypes[] = 'T';
    if ($training['event_type_mixed_team']) $eventTypes[] = 'MT';
    return implode(', ', $eventTypes);
}