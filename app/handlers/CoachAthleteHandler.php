<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SessionExpiryHandler.php';
checkSessionTimeout();

// Ensure the user is logged in and is a coach
if ($_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$coach_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mareos_id = $_POST['mareos_id'];

    // Check if the athlete with the provided mareos_id exists and has completed their profile
    try {
        $stmt = $pdo->prepare('
            SELECT user_id FROM athlete_details WHERE mareos_id = :mareos_id AND mareos_id IS NOT NULL
        ');
        $stmt->bindParam(':mareos_id', $mareos_id);
        $stmt->execute();
        $athlete = $stmt->fetch();

        if ($athlete) {
            // Athlete exists and has a valid mareos_id, proceed to add to coach_athlete
            $athlete_user_id = $athlete['user_id'];

            $stmt = $pdo->prepare('
                INSERT INTO coach_athlete (coach_user_id, athlete_user_id, start_date)
                VALUES (:coach_id, :athlete_user_id, NOW())
                ON DUPLICATE KEY UPDATE updated_at = NOW()
            ');
            $stmt->bindParam(':coach_id', $coach_id);
            $stmt->bindParam(':athlete_user_id', $athlete_user_id);
            $stmt->execute();

            $_SESSION['success'] = 'Athlete successfully added to your list.';
        } else {
            $_SESSION['error'] = 'Athlete not found or does not have a valid Mareos ID.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to add athlete: ' . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'app\views\users\coach\manage-athletes.php');
    exit();
}
