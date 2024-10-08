<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Check user roles
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';
$isCoach = $isLoggedIn && $_SESSION['role'] === 'coach';
$isAthlete = $isLoggedIn && $_SESSION['role'] === 'athlete';
$isAdminOrCoach = $isAdmin || $isCoach;

if ($isAdminOrCoach) {
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

        // Generate random 8-character code for the competition
        $generated_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

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
                        event_type_individual, event_type_team, event_type_mixed_team, added_by, generated_code) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$competition_name, $start_date, $end_date, $registration_deadline, $location, $description, $level_id,
                    $bow_type_recurve, $bow_type_compound, $bow_type_barebow, 
                    $event_type_individual, $event_type_team, $event_type_mixed_team, $added_by, $generated_code]);
                $_SESSION['success'] = 'Competition added successfully!';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Failed to add competition: ' . $e->getMessage();
            }
        }

        header('Location: ' . BASE_URL . 'app/views/competition/competition.php');
        exit;
    }

    // Handle Delete
    if (isset($_GET['delete'])) {
        if (!$isAdmin) {
            $_SESSION['error'] = 'You do not have permission to delete this.';
            header('Location: ' . BASE_URL . 'app/views/training/training.php');
            exit;
        }

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
}