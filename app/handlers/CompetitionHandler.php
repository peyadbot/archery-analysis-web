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

// Fetch event levels for the dropdown
$levelsStmt = $pdo->query('SELECT * FROM event_levels');
$levels = $levelsStmt->fetchAll();

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_name = $_POST['competition_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $registration_deadline = $_POST['registration_deadline'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $level_id = $_POST['level_id'];
    $added_by = $_SESSION['user_id'];

    // Bow type booleans
    $bow_type_recurve = isset($_POST['bow_type_recurve']) ? 1 : 0;
    $bow_type_compound = isset($_POST['bow_type_compound']) ? 1 : 0;
    $bow_type_barebow = isset($_POST['bow_type_barebow']) ? 1 : 0;

    // Event type booleans
    $event_type_individual = isset($_POST['event_type_individual']) ? 1 : 0;
    $event_type_team = isset($_POST['event_type_team']) ? 1 : 0;
    $event_type_mixed_team = isset($_POST['event_type_mixed_team']) ? 1 : 0;

    if (isset($_POST['competition_id'])) {
        // Update existing competition
        $competition_id = $_POST['competition_id'];
        try {
            $stmt = $pdo->prepare('
                UPDATE competitions 
                SET competition_name = ?, start_date = ?, end_date = ?, registration_deadline = ?, location = ?, description = ?, level_id = ?, 
                bow_type_recurve = ?, bow_type_compound = ?, bow_type_barebow = ?, 
                event_type_individual = ?, event_type_team = ?, event_type_mixed_team = ?, added_by = ? 
                WHERE competition_id = ?');
            $stmt->execute([$competition_name, $start_date, $end_date, $registration_deadline, $location, $description, $level_id,
                $bow_type_recurve, $bow_type_compound, $bow_type_barebow, 
                $event_type_individual, $event_type_team, $event_type_mixed_team, $added_by, $competition_id]);
            $_SESSION['success'] = 'Competition updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update competition: ' . $e->getMessage();
        }
    } else {
        // Insert new competition
        try {
            $stmt = $pdo->prepare('
                INSERT INTO competitions 
                (competition_name, start_date, end_date, registration_deadline, location, description, level_id, 
                bow_type_recurve, bow_type_compound, bow_type_barebow, 
                event_type_individual, event_type_team, event_type_mixed_team, added_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$competition_name, $start_date, $end_date, $registration_deadline, $location, $description, $level_id,
                $bow_type_recurve, $bow_type_compound, $bow_type_barebow, 
                $event_type_individual, $event_type_team, $event_type_mixed_team, $added_by]);
            $_SESSION['success'] = 'Competition added successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to add competition: ' . $e->getMessage();
        }
    }

    // Redirect to avoid form resubmission
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

    // SELECT competitions.*, users.username
    // FROM competitions
    // JOIN users ON competitions.added_by = users.user_id
// Fetch all competitions with user info
$stmt = $pdo->prepare('
    SELECT competitions.*, users.username
    FROM competitions
    JOIN users ON competitions.added_by = users.user_id
');
$stmt->execute();
$competitions = $stmt->fetchAll();

$isLoggedIn = isset($_SESSION['user_id']);
$isAdminOrCoach = $isLoggedIn && (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'coach'));

// Function to format bow types
function formatCompetitionBowTypes($competition) {
    $bowTypes = [];
    if ($competition['bow_type_recurve']) $bowTypes[] = 'R';
    if ($competition['bow_type_compound']) $bowTypes[] = 'C';
    if ($competition['bow_type_barebow']) $bowTypes[] = 'BB';
    return implode(', ', $bowTypes);
}

// Function to format event types
function formatCompetitionEventTypes($competition) {
    $eventTypes = [];
    if ($competition['event_type_individual']) $eventTypes[] = 'Ind';
    if ($competition['event_type_team']) $eventTypes[] = 'T';
    if ($competition['event_type_mixed_team']) $eventTypes[] = 'MT';
    return implode(', ', $eventTypes);
}