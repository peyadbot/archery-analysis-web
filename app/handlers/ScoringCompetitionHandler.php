<?php
ob_start();
// session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SessionExpiryHandler.php';

if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$athlete_id = $user_id;
$editMode = false;

// Handle DELETE request
if (isset($_GET['delete'])) {
    $score_id = $_GET['delete'];

    try {
        $stmt = $pdo->prepare('DELETE FROM scores WHERE score_id = ? AND athlete_id = ?');
        $stmt->execute([$score_id, $athlete_id]);

        $_SESSION['success'] = 'Score deleted successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete score: ' . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'app/views/users/athlete/inputScoreComp.php');
    exit();
}

// Fetch scores to edit
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT s.*, c.generated_code FROM scores s JOIN competitions c ON s.competition_id = c.competition_id WHERE s.score_id = ? AND s.athlete_id = ?');
    $stmt->execute([$_GET['edit'], $athlete_id]);
    $editScore = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($editScore) {
        $editMode = true;
    }
}

// Add & update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_code = $_POST['competition_code'];
    $score_50m_1 = $_POST['score_50m_1'];
    $score_50m_2 = $_POST['score_50m_2'];
    $total_score = $_POST['total_score'];
    $tens_and_x = $_POST['tens_and_x'];
    $x_count = $_POST['x_count'];
    $win_status = $_POST['win_status'];
    $rank = $_POST['rank'];

    // Validate competition code and get competition_id
    $stmt = $pdo->prepare('SELECT competition_id FROM competitions WHERE generated_code = ?');
    $stmt->execute([$competition_code]);
    $competition = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$competition) {
        // Invalid competition code
        $_SESSION['error'] = 'Invalid competition code. Please enter the correct code provided by the judge.';
        header('Location: ' . BASE_URL . 'app/views/users/athlete/inputScoreComp.php');
        exit();
    }

    $competition_id = $competition['competition_id'];

    // Check if the athlete already has a score for this competition
    if (!isset($_POST['score_id'])) {
        // Only check for existing scores if it's not an update
        $stmt = $pdo->prepare('SELECT * FROM scores WHERE athlete_id = ? AND competition_id = ?');
        $stmt->execute([$athlete_id, $competition_id]);
        $existingScore = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingScore) {
            $_SESSION['error'] = 'You have already submitted a score for this competition.';
            header('Location: ' . BASE_URL . 'app/views/users/athlete/inputScoreComp.php');
            exit();
        }
    }

    // Check if we are updating an existing score
    if (isset($_POST['score_id']) && !empty($_POST['score_id'])) {
        $score_id = $_POST['score_id'];

        try {
            // Update the score, including win_status and rank
            $stmt = $pdo->prepare('
                UPDATE scores 
                SET score_50m_1 = ?, score_50m_2 = ?, total_score = ?, tens_and_x = ?, x_count = ?, win_status = ?, rank = ?, competition_id = ? 
                WHERE score_id = ? AND athlete_id = ?
            ');
            $stmt->execute([$score_50m_1, $score_50m_2, $total_score, $tens_and_x, $x_count, $win_status, $rank, $competition_id, $score_id, $athlete_id]);

            $_SESSION['success'] = 'Score updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update score: ' . $e->getMessage();
        }
    } else {
        try {
            // Insert new score with win_status and rank
            $stmt = $pdo->prepare('
                INSERT INTO scores (athlete_id, competition_id, score_50m_1, score_50m_2, total_score, tens_and_x, x_count, win_status, rank, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([$athlete_id, $competition_id, $score_50m_1, $score_50m_2, $total_score, $tens_and_x, $x_count, $win_status, $rank]);

            $_SESSION['success'] = 'Score added successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to add score: ' . $e->getMessage();
        }
    }

    header('Location: ' . BASE_URL . 'app/views/users/athlete/inputScoreComp.php');
    exit();
}
