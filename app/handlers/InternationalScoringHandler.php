<?php
require_once __DIR__ . '/../../config/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update score logic
    $competition_id = $_POST['competition_id'];
    $competition_name = $_POST['competition_name'];
    $event_name = $_POST['event_name'];
    $event_distance = $_POST['event_distance'];
    $m_1_score = $_POST['m_1_score'];
    $m_2_score = $_POST['m_2_score'];
    $total_10 = $_POST['total_10'];
    $total_9 = $_POST['total_9'];
    $total_score = $_POST['total_score'];

    // Check if updating an existing score
    if (isset($_POST['score_id']) && !empty($_POST['score_id'])) {
        $score_id = $_POST['score_id'];

        try {
            $stmt = $pdo->prepare('
                UPDATE international_comp_scores 
                SET competition_id = ?, competition_name = ?, event_name = ?, event_distance = ?, m_1_score = ?, m_2_score = ?, total_10 = ?, total_9 = ?, total_score = ?, updated_at = NOW()
                WHERE score_id = ? AND user_id = ?
            ');
            $stmt->execute([$competition_id, $competition_name, $event_name, $event_distance, $m_1_score, $m_2_score, $total_10, $total_9, $total_score, $score_id, $user_id]);

            $_SESSION['success'] = 'Score updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update score: ' . $e->getMessage();
        }
    } else {
        // Insert new score
        try {
            $stmt = $pdo->prepare('
                INSERT INTO international_comp_scores (user_id, competition_id, competition_name, event_name, event_distance, m_1_score, m_2_score, total_10, total_9, total_score, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([$user_id, $competition_id, $competition_name, $event_name, $event_distance, $m_1_score, $m_2_score, $total_10, $total_9, $total_score]);

            $_SESSION['success'] = 'Score added successfully!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to add score: ' . $e->getMessage();
        }
    }

    header('Location: ' . BASE_URL . 'app/views/users/athlete/compScoring.php?view=international');
    exit();
}

// Handle DELETE request
if (isset($_GET['delete'])) {
    $score_id = $_GET['delete'];

    try {
        $stmt = $pdo->prepare('DELETE FROM international_comp_scores WHERE score_id = ? AND user_id = ?');
        $stmt->execute([$score_id, $user_id]);

        $_SESSION['success'] = 'Score deleted successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete score: ' . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'app/views/users/athlete/compScoring.php?view=international');
    exit();
}
