<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch mareos_id of the logged-in athlete
try {
    $stmt = $pdo->prepare('SELECT mareos_id FROM profiles WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $athlete = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($athlete && !empty($athlete['mareos_id'])) {
        $mareos_id = $athlete['mareos_id'];
    } else {
        $_SESSION['error'] = 'No mareos_id found for the athlete.';
        header('Location: ' . BASE_URL . 'app/views/users/athlete/trainScoring.php?view=personal');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to retrieve mareos_id: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'app/views/users/athlete/trainScoring.php?view=personal');
    exit();
}

// Save new score or update existing score
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_id = $_POST['training_id'];
    $training_name = $_POST['training_name'];
    $event_name = $_POST['event_name'];
    $event_distance = $_POST['event_distance'];
    $m1_score = $_POST['m1_score'];
    $m1_10X = $_POST['m1_10X'];
    $m1_109 = $_POST['m1_109'];
    $m2_score = $_POST['m2_score'];
    $m2_10X = $_POST['m2_10X'];
    $m2_109 = $_POST['m2_109'];
    $total_10X = $_POST['total_10X'];
    $total_109 = $_POST['total_109'];
    $total_score = $_POST['total_score'];

    // Check if updating an existing score
    if (isset($_POST['score_id']) && !empty($_POST['score_id'])) {
        $score_id = $_POST['score_id'];

        try {
            $stmt = $pdo->prepare('
                UPDATE personal_training_scores 
                SET training_id = ?, training_name = ?, event_name = ?, event_distance = ?, 
                    m1_score = ?, m1_10X = ?, m1_109 = ?, m2_score = ?, m2_10X = ?, m2_109 = ?, 
                    total_10X = ?, total_109 = ?, total_score = ?, updated_at = NOW()
                WHERE score_id = ? AND user_id = ?
            ');
            $stmt->execute([
                $training_id, $training_name, $event_name, $event_distance,
                $m1_score, $m1_10X, $m1_109, $m2_score, $m2_10X, $m2_109,
                $total_10X, $total_109, $total_score, $score_id, $user_id
            ]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = 'Score updated successfully!';
            } else {
                $_SESSION['error'] = 'No changes were made or score not found.';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update score: ' . $e->getMessage();
        }
    } else {
        // Insert new score
        try {
            $stmt = $pdo->prepare('
                INSERT INTO personal_training_scores 
                (user_id, mareos_id, training_id, training_name, event_name, event_distance, 
                m1_score, m1_10X, m1_109, m2_score, m2_10X, m2_109, total_10X, total_109, total_score, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $user_id, $mareos_id, $training_id, $training_name, $event_name, $event_distance, 
                $m1_score, $m1_10X, $m1_109, $m2_score, $m2_10X, $m2_109, $total_10X, $total_109, $total_score
            ]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = 'Score added successfully!';
            } else {
                $_SESSION['error'] = 'Failed to add score. No rows affected.';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to add score: ' . $e->getMessage();
        }
    }

    header('Location: ' . BASE_URL . 'app/views/users/athlete/trainScoring.php?view=personal');
    exit();
}

// Handle DELETE request
if (isset($_GET['delete'])) {
    $score_id = $_GET['delete'];

    try {
        $stmt = $pdo->prepare('DELETE FROM personal_training_scores WHERE score_id = ? AND user_id = ?');
        $stmt->execute([$score_id, $user_id]);

        $_SESSION['success'] = 'Score deleted successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete score: ' . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'app/views/users/athlete/trainScoring.php?view=personal');
    exit();
}