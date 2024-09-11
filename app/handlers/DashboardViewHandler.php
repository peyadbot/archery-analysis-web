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

// Check user roles
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

    // Initialize the data array
    $data = [
        'athleteCount' => 0,
        'coachCount' => 0,
        'competitionCount' => 0,
        'trainingCount' => 0,    
        'bestScore' => 0,               // Best score achieved by athlete
        'recentCompetitions' => [],  // Recent competitions for athlete or coach
        'monthlyScores' => []
    ];

    try {
        switch ($role) {
            case 'admin':
                // Admin-specific data: number of athletes, coaches, competitions, and trainings
                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM users WHERE role = "athlete"');
                $data['athleteCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM users WHERE role = "coach"');
                $data['coachCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM competitions');
                $data['competitionCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->query('SELECT COUNT(*) AS count FROM trainings');
                $data['trainingCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                break;

            case 'coach':
                // Coach-specific data: Competitions and trainings added by the coach
                $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM competitions WHERE added_by = ?');
                $stmt->execute([$user_id]);
                $data['competitionCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM trainings WHERE added_by = ?');
                $stmt->execute([$user_id]);
                $data['trainingCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                // Fetch recent competitions added by the coach
                $stmt = $pdo->prepare('SELECT competition_name, start_date FROM competitions WHERE added_by = ? ORDER BY start_date DESC LIMIT 5');
                $stmt->execute([$user_id]);
                $data['recentCompetitions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'athlete':
                // Fetch the total number of competitions the athlete has participated in
                $stmt = $pdo->prepare('
                    SELECT COUNT(DISTINCT competition_id) AS total_competitions
                    FROM scores
                    WHERE athlete_id = (SELECT athlete_id FROM athlete_details WHERE user_id = ?)
                ');
                $stmt->execute([$user_id]);
                $data['competitionCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_competitions'];

                // Fetch the best score
                $stmt = $pdo->prepare('
                    SELECT  COALESCE(MAX(total_score), 0) AS best_score
                    FROM scores
                    WHERE athlete_id = (SELECT athlete_id FROM athlete_details WHERE user_id = ?)
                ');
                $stmt->execute([$user_id]);
                $data['bestScore'] = $stmt->fetch(PDO::FETCH_ASSOC)['best_score'];

                // Fetch the latest 3 competitions with their scores
                $stmt = $pdo->prepare('
                    SELECT c.competition_name, s.total_score, c.start_date 
                    FROM scores s 
                    JOIN competitions c ON s.competition_id = c.competition_id 
                    WHERE s.athlete_id = (SELECT athlete_id FROM athlete_details WHERE user_id = ?)
                    ORDER BY c.start_date DESC LIMIT 4
                ');
                $stmt->execute([$user_id]);
                $data['recentCompetitions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Monthly total scores
                $stmt = $pdo->prepare('
                SELECT 
                    DATE_FORMAT(c.start_date, "%Y-%m") AS month, 
                    SUM(s.total_score) AS monthly_total
                FROM scores s
                JOIN competitions c ON s.competition_id = c.competition_id
                WHERE s.athlete_id = (SELECT athlete_id FROM athlete_details WHERE user_id = ?)
                AND YEAR(c.start_date) = YEAR(CURDATE())  -- This ensures only scores from the current year are fetched
                GROUP BY month
                ORDER BY month
                ');
                $stmt->execute([$user_id]);
                $monthlyScores = $stmt->fetchAll(PDO::FETCH_ASSOC);


                // Prepare monthly scores for Chart.js (e.g., Jan, Feb, etc.)
                $data['monthlyScores'] = [];
                $months = [
                    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun',
                    '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                ];

                foreach ($months as $month => $monthName) {
                    $monthKey = date('Y') . '-' . $month;
                    $monthlyTotal = 0;

                    // Check if data exists for this month
                    foreach ($monthlyScores as $score) {
                        if ($score['month'] === $monthKey) {
                            $monthlyTotal = $score['monthly_total'];
                            break;
                        }
                    }

                    $data['monthlyScores'][] = [
                        'month' => $monthName,
                        'total' => $monthlyTotal
                    ];
                }

                break;
        }

    } catch (PDOException $e) {
        throw new Exception('Database error: ' . $e->getMessage());
    }

    return $data;
}

// Function to retrieve user profile
function getProfile($user_id) {
    global $pdo;

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

        return $profile;

    } catch (PDOException $e) {
        throw new Exception('Database error: ' . $e->getMessage());
    }
}