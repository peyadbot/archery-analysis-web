<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$coach_id = $_SESSION['user_id'];
$role = $_SESSION['role']; 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle DELETE Request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['score_id'])) {
    $score_id = $_GET['score_id'];

    try {
        // Check if the score exists
        $stmt = $pdo->prepare('SELECT mareos_id FROM local_comp_scores WHERE score_id = ?');
        $stmt->execute([$score_id]);
        $score = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$score) {
            echo json_encode(['status' => 'error', 'message' => 'Score not found']);
            exit();
        }

        // Now check if the user (athlete or coach) has permission to delete the score
        $stmt = $pdo->prepare('SELECT user_id FROM profiles WHERE mareos_id = ?');
        $stmt->execute([$score['mareos_id']]);
        $athlete = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$athlete || ($role === 'coach' && !$athlete)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
            exit();
        }

        // Delete the score
        $stmt = $pdo->prepare('DELETE FROM local_comp_scores WHERE score_id = ?');
        $stmt->execute([$score_id]);

        if ($stmt->rowCount()) {
            echo json_encode(['status' => 'success', 'message' => 'Score deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete the score']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// Handle POST Request (Saving a new score)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the incoming data
    if (!isset($data['athlete_id'], $data['event_name'], $data['event_distance'], $data['m_1_score'], $data['1_10'], $data['1_9'], $data['m_2_score'], $data['2_10'], $data['2_9'],
        $data['m_2_score'], $data['total_score'], $data['total_10'], $data['total_9'], $data['competition_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    }

    try {
        // Check if a score already exists for this athlete and competition
        $stmt = $pdo->prepare('SELECT score_id FROM local_comp_scores WHERE mareos_id = ? AND competition_id = ?');
        $stmt->execute([$data['athlete_id'], $data['competition_id']]);
        $existingScore = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingScore) {
            echo json_encode(['status' => 'error', 'message' => 'You have already saved a score for this competition.']);
        } else {
            // Fetch athlete's user_id from mareos_id
            $stmt = $pdo->prepare('SELECT user_id FROM profiles WHERE mareos_id = ?');
            $stmt->execute([$data['athlete_id']]);
            $athlete = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$athlete) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid athlete ID']);
                exit();
            }

            // Insert new score for the athlete, saving the athlete's user_id
            $stmt = $pdo->prepare('
                INSERT INTO local_comp_scores (user_id, mareos_id, total_score, 1_10, 1_9, m_1_score, 2_10, 2_9, m_2_score, total_10, total_9, event_name, event_distance, competition_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $athlete['user_id'], $data['athlete_id'], $data['m_1_score'], $data['1_10'], $data['1_9'], $data['m_2_score'], $data['2_10'], $data['2_9'], $data['total_score'],
                $data['total_10'], $data['total_9'], $data['event_name'], $data['event_distance'], $data['competition_id']
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Score saved successfully']);
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// If the request is neither POST nor DELETE
echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
exit();
