<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SessionExpiryHandler.php';
require_once __DIR__ . '/LogoutHandler.php';
checkSessionTimeout();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['athlete', 'coach', 'admin'])) {
    header('Location: ' . BASE_URL . 'app/views/auth/login.php');
    exit();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';
$isCoach = $isLoggedIn && $_SESSION['role'] === 'coach';
$isAthlete = $isLoggedIn && $_SESSION['role'] === 'athlete';
$isAdminOrCoach = $isAdmin || $isCoach;

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Function to retrieve dashboard statistics based on user role
function getDashboardData($user_id, $role) {
    global $pdo;
    $data = [
        'athleteCount' => 0,
        'coachCount' => 0,
        'competitionCount' => 0,
        'trainingCount' => 0,    
        'bestScore' => 0,              
        'monthlyScores' => [],
        'allCompetition'=> [],
        'recentCompetitions' => [], 
        'latestCompetitions' => []
    ];

    try {
        switch ($role) {
            case 'admin':
                // Admin-specific data: number of athletes, coaches, competitions, and trainings
                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM users WHERE role = "athlete"');
                $data['athleteCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM users WHERE role = "coach"');
                $data['coachCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM trainings');
                $data['trainingCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                // Fetch the recent user registrations
                $stmt = $pdo->prepare('SELECT user_id, username, role, created_at FROM users ORDER BY created_at DESC LIMIT 4');
                $stmt->execute();
                $data['recentUsers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Fetch the latest competition
                try {
                    $json = file_get_contents('https://ianseo.sukanfc.com/fetch_tournaments.php');
                    $competitions = json_decode($json, true);

                    if (!empty($competitions)) {
                        $data['latestCompetitions'] = array_slice($competitions, 0, 4); // Show only 5 recent competitions
                    } else {
                        $data['latestCompetitions'] = [];
                    }
                } catch (Exception $e) {
                    $data['latestCompetitions'] = [];
                }

                // Fetch all competition
                try {
                    // Replace with the correct API endpoint
                    $json = file_get_contents('https://ianseo.sukanfc.com/fetch_tournaments.php');
                    $competitions = json_decode($json, true);
                
                    $data['allCompetitions'] = $competitions; 
                } catch (Exception $e) {
                    $data['allCompetitions'] = []; 
                }
                $data['competitionCount'] =  count($data['allCompetitions']); 
                break;

            case 'coach':
                $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM trainings WHERE added_by = ?');
                $stmt->execute([$user_id]);
                $data['trainingCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                // Fetch the number of athletes managed by the coach
                $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM coach_athlete WHERE coach_user_id = ?');
                $stmt->execute([$user_id]);
                $data['athleteCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                // Fetch the list of tournaments from the Ianseo API
                try {
                    $json = file_get_contents('https://ianseo.sukanfc.com/fetch_tournaments.php');
                    $competitions = json_decode($json, true);

                    if (!empty($competitions)) {
                        $data['latestCompetitions'] = array_slice($competitions, 0, 5); // Show only 5 recent competitions
                    } else {
                        $data['latestCompetitions'] = [];
                    }
                } catch (Exception $e) {
                    // Handle the case where the API fetch fails
                    $data['latestCompetitions'] = [];
                }

                // Fetch all competition
                try {
                    // Replace with the correct API endpoint
                    $json = file_get_contents('https://ianseo.sukanfc.com/fetch_tournaments.php');
                    $competitions = json_decode($json, true);
                
                    $data['allCompetitions'] = $competitions; 
                } catch (Exception $e) {
                    $data['allCompetitions'] = []; 
                }
                $data['competitionCount'] =  count($data['allCompetitions']); 
                break;

            case 'athlete':
                // Fetch the total number of competitions the athlete has participated in (local + international)
                $stmt = $pdo->prepare('
                    SELECT COUNT(DISTINCT competition_id) AS total_competitions
                    FROM (
                        SELECT competition_id FROM local_comp_scores WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                        UNION ALL
                        SELECT competition_id FROM international_comp_scores WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                    ) AS combined_competitions
                ');
                $stmt->execute([$user_id, $user_id]);
                $data['competitionCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_competitions'];

                // Fetch the best score for Local Competitions
                $stmt = $pdo->prepare('
                    SELECT MAX(total_score) AS best_local_score
                    FROM local_comp_scores
                    WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                ');
                $stmt->execute([$user_id]);
                $data['bestLocalScore'] = $stmt->fetch(PDO::FETCH_ASSOC)['best_local_score'];

                // Fetch the best score for International Competitions
                $stmt = $pdo->prepare('
                    SELECT MAX(total_score) AS best_international_score
                    FROM international_comp_scores
                    WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                ');
                $stmt->execute([$user_id]);
                $data['bestInternationalScore'] = $stmt->fetch(PDO::FETCH_ASSOC)['best_international_score'];

                // Fetch the latest 4 competitions with their scores (local + international)
                $stmt = $pdo->prepare('
                    SELECT competition_id, total_score, created_at FROM (
                        SELECT competition_id, total_score, created_at 
                        FROM local_comp_scores 
                        WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                        
                        UNION ALL
                        
                        SELECT competition_id, total_score, created_at 
                        FROM international_comp_scores 
                        WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                    ) AS combined_competitions
                    ORDER BY created_at DESC LIMIT 4
                ');
                $stmt->execute([$user_id, $user_id]);
                $data['recentCompetitions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Fetch the monthly total scores for both local and international competitions
                $stmt = $pdo->prepare('
                    SELECT month, SUM(local_total) AS local_total, SUM(international_total) AS international_total FROM (
                        SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, SUM(total_score) AS local_total, 0 AS international_total
                        FROM local_comp_scores
                        WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                        GROUP BY month
                        UNION ALL
                        SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, 0 AS local_total, SUM(total_score) AS international_total
                        FROM international_comp_scores
                        WHERE mareos_id = (SELECT mareos_id FROM athlete_details WHERE user_id = ?)
                        GROUP BY month
                    ) AS combined_scores
                    GROUP BY month
                    ORDER BY month
                ');
                $stmt->execute([$user_id, $user_id]);
                $monthlyScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Prepare monthly scores 
                $data['monthlyScores'] = [];
                $months = [
                    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun',
                    '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                ];

                foreach ($months as $month => $monthName) {
                    $monthKey = date('Y') . '-' . $month;
                    $localTotal = 0;
                    $internationalTotal = 0;

                    // Check if data exists for this month
                    foreach ($monthlyScores as $score) {
                        if ($score['month'] === $monthKey) {
                            $localTotal = $score['local_total'];
                            $internationalTotal = $score['international_total'];
                            break;
                        }
                    }

                    $data['monthlyScores'][] = [
                        'month' => $monthName,
                        'local_total' => $localTotal,
                        'international_total' => $internationalTotal
                    ];
                }
                break;
        }
    } catch (PDOException $e) {
        throw new Exception('Database error: ' . $e->getMessage());
    }
    return $data;
}