<?php
require_once __DIR__ . '/../../config/config.php';  

// Use to fetch saved score
// Check if competition_id is set
if (isset($_GET['competition_id'])) {
    $competition_id = $_GET['competition_id'];

    try {
        // Fetch saved scores for the given competition_id
        $stmt = $pdo->prepare('SELECT mareos_id FROM local_comp_scores WHERE competition_id = ?');
        $stmt->execute([$competition_id]);
        $savedScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Set header and output JSON without any other content before it
        header('Content-Type: application/json');
        echo json_encode($savedScores, JSON_PRETTY_PRINT);  // Use pretty print for better readability (optional)
    } catch (PDOException $e) {
        // Handle database error and return as JSON
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Handle missing competition_id error
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid competition_id']);
}
