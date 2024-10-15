<?php
require_once __DIR__ . '/../../config/config.php';

// Check that the user is a coach
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

// Get form data
$session_name = $_POST['session_name'];
$location = $_POST['location'];
$session_date = $_POST['session_date'];
$description = $_POST['description'];
$session_type = $_POST['session_type'];
$distance = $_POST['distance'];
$num_ends = $_POST['num_ends'];
$game_type = $_POST['game_type'];
$athletes = $_POST['athletes']; // Array of selected mareos_ids
$created_by = $_SESSION['user_id']; // Coach's user_id from session

try {
    // Insert new session into training_sessions table
    $stmt = $pdo->prepare("
        INSERT INTO training_sessions 
        (session_name, location, session_date, description, session_type, distance, num_ends, game_type, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$session_name, $location, $session_date, $description, $session_type, $distance, $num_ends, $game_type, $created_by]);

    // Get the ID of the newly created session
    $session_id = $pdo->lastInsertId();

    // Insert each selected athlete into session_athletes table
    $stmt = $pdo->prepare("INSERT INTO session_athletes (session_id, mareos_id, status) VALUES (?, ?, 'pending')");
    foreach ($athletes as $mareos_id) {
        $stmt->execute([$session_id, $mareos_id]);
    }

    // Redirect to a success page or show a success message
    $_SESSION['success'] = 'Training session created successfully!';
    header('Location: ' . BASE_URL . 'app/views/training/training.php');
    exit();
} catch (PDOException $e) {
    // Handle errors
    $_SESSION['error'] = 'Error creating training session: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'app/views/training/add_training_session.php');
    exit();
}
