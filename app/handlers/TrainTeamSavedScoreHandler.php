<?php
require_once __DIR__ . '/../../config/config.php';  

// Use to fetch saved score
if (isset($_GET['training_id'])) {
    $training_id = $_GET['training_id'];

    try {
        // Fetch saved scores for the given training_id
        $stmt = $pdo->prepare('SELECT mareos_id FROM team_training_scores WHERE training_id = ?');
        $stmt->execute([$training_id]);
        $savedScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Set header and output JSON without any other content before it
        header('Content-Type: application/json');
        echo json_encode($savedScores, JSON_PRETTY_PRINT); 
    } catch (PDOException $e) {
        // Handle database error and return as JSON
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Handle missing training_id error
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid training_id']);
}