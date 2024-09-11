<?php
// session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SessionExpiryHandler.php';
checkSessionTimeout();

// Ensure the user is an athlete
if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $athlete_id = $_SESSION['user_id'];  // Athlete's ID from the session
    $competition_id = $_POST['competition_id'];
    $score_50m_1 = $_POST['score_50m_1'];
    $score_50m_2 = $_POST['score_50m_2'];
    $total_score = $_POST['total_score'];
    $tens_and_x = $_POST['tens_and_x'];
    $x_count = $_POST['x_count'];

    // Insert the scores into the database
    try {
        $stmt = $pdo->prepare('INSERT INTO scores (athlete_id, competition_id, score_50m_1, score_50m_2, total_score, tens_and_x, x_count) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$athlete_id, $competition_id, $score_50m_1, $score_50m_2, $total_score, $tens_and_x, $x_count]);
        
        $_SESSION['success'] = 'Score added successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to add score: ' . $e->getMessage();
    }

    // Redirect back to the input form
    header('Location: ' . BASE_URL . 'app/views/users/athlete/statisticComp.php');
    exit();
}
