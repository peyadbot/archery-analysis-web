<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/LogoutHandler.php';


if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['athlete', 'coach', 'admin'])) {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

function getDashboardData($user_id, $role) {
    global $pdo; // Ensure you are using the global $pdo object

    // Initialize the data array
    $data = [
        'athleteCount' => 0,
        'coachCount' => 0,
        'competitionCount' => 0,
        'trainingCount' => 0,
    ];

    try {
        switch ($role) {
            case 'admin': // Admin-specific data
                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM users WHERE role = "athlete"');
                $data['athleteCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM users WHERE role = "coach"');
                $data['coachCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM competitions');
                $data['competitionCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM trainings');
                $data['trainingCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                break;

            case 'coach': // Coach-specific data
                // Get the number of competitions added by this coach
                $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM competitions WHERE added_by = ?');
                $stmt->execute([$user_id]);
                $data['competitionCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                // Get the number of training sessions added by this coach
                $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM trainings WHERE added_by = ?');
                $stmt->execute([$user_id]);
                $data['trainingCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                break;

            case 'athlete':
                // Athlete-specific data

                break;
        }

    } catch (PDOException $e) {
        throw new Exception('Database error: ' . $e->getMessage());
    }

    return $data;
}

function getProfile($user_id) {
    global $pdo; // Ensure you are using the global $pdo object

    try {
        // Prepare and execute the SQL query to fetch user profile
        $stmt = $pdo->prepare('SELECT * FROM profiles WHERE user_id = ?');
        $stmt->execute([$user_id]);

        // Fetch profile data
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        // If profile is not found, return a default structure
        if (!$profile) {
            return [
                'incomplete' => true,
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone_number' => '',
                'profile_picture' => '',
                'ic_number' => '',
                'passport_number' => '',
                'state' => ''
            ];
        }

        // Check if the profile is incomplete (if any field is missing)
        $profile['incomplete'] = empty($profile['first_name']) || empty($profile['last_name']) || empty($profile['email']);
        return $profile;

    } catch (PDOException $e) {
        throw new Exception('Database error: ' . $e->getMessage());
    }
}

