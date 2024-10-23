<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SessionExpiryHandler.php';
checkSessionTimeout();

// Ensure the user is logged in and is a coach
if ($_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$coach_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mareos_id = $_POST['mareos_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // Handle adding a new athlete
        try {
            $stmt = $pdo->prepare('SELECT user_id FROM athlete_details WHERE mareos_id = :mareos_id');
            $stmt->bindParam(':mareos_id', $mareos_id);
            $stmt->execute();
            $athlete = $stmt->fetch();

            if ($athlete) {
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
    } elseif ($action === 'remove') {
        // Handle removing an athlete
        $athlete_user_id = $_POST['athlete_user_id'] ?? null;

        if ($athlete_user_id) {
            try {
                $stmt = $pdo->prepare('DELETE FROM coach_athlete WHERE coach_user_id = :coach_id AND athlete_user_id = :athlete_user_id');
                $stmt->bindParam(':coach_id', $coach_id);
                $stmt->bindParam(':athlete_user_id', $athlete_user_id);
                $stmt->execute();

                $_SESSION['success'] = 'Athlete successfully removed from your list.';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Failed to remove athlete: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Invalid athlete ID.';
        }
    }

    header('Location: ' . BASE_URL . 'app/views/users/coach/manageAthletes.php');
    exit();
}