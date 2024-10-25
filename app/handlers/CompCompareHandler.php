<?php
function handleCompCompare($pdo, $user_id)
{
    $athlete = getAthleteData($pdo, $user_id);

    if (!$athlete) {
        return [
            'error' => 'Athlete profile not found. Please complete your profile.',
            'redirect' => BASE_URL . 'app/views/profiles/profile.php'
        ];
    }

    $mareos_id = $athlete['mareos_id'];
    $competitions = getAllCompetitions($pdo, $mareos_id);

    $comparison_data = [];
    $selected_competitions = [];
    $selected_metrics = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['competitions'])) {
            $selected_competitions = $_POST['competitions'];
            $comparison_data = getCompetitionComparison($pdo, $mareos_id, $selected_competitions);
        }
        if (!empty($_POST['metrics'])) {
            $selected_metrics = $_POST['metrics'];
        }
    }

    $all_metrics = [
        'm1_score' => 'M1 Score',
        'm2_score' => 'M2 Score',
        'total_score' => 'Total Score',
        'm1_10X' => 'M1 10+X',
        'm1_109' => 'M1 10/9',
        'm2_10X' => 'M2 10+X',
        'm2_109' => 'M2 10/9',
        'total_10X' => 'Total 10+X',
        'total_109' => 'Total 10/9'
    ];


    if (empty($selected_metrics)) {
        $selected_metrics = array_keys($all_metrics);
    }

    return [
        'competitions' => $competitions,
        'comparison_data' => $comparison_data,
        'selected_competitions' => $selected_competitions,
        'selected_metrics' => $selected_metrics,
        'all_metrics' => $all_metrics,
        'mareos_id' => $athlete['mareos_id']
    ];
}

function getAllCompetitions($pdo, $mareos_id)
{
    $query = "SELECT competition_id, event_name, event_distance, created_at FROM local_comp_scores WHERE mareos_id = ? UNION SELECT competition_id, event_name, event_distance, created_at FROM international_comp_scores WHERE mareos_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$mareos_id, $mareos_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCompetitionComparison($pdo, $mareos_id, $selected_competitions)
{
    if (empty($selected_competitions) || !is_array($selected_competitions)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($selected_competitions), '?'));
    $query = "
        SELECT competition_id, event_name, event_distance, m1_score, m2_score, 
            m1_10X as m1_10X, m1_109, m2_10X as m2_10X, m2_109, 
            total_score, total_10X, total_109
        FROM (
            SELECT competition_id, event_name, event_distance, m1_score, m2_score, 
                m1_10X, m1_109, m2_10X, m2_109, 
                total_score, total_10X, total_109
            FROM local_comp_scores
            WHERE mareos_id = ? AND competition_id IN ($placeholders)
            UNION ALL
            SELECT competition_id, event_name, event_distance, m1_score, m2_score, 
                m1_10X, m1_109, m2_10X, m2_109, 
                total_score, total_10X, total_109
            FROM international_comp_scores
            WHERE mareos_id = ? AND competition_id IN ($placeholders)
        ) AS combined_scores
        ORDER BY competition_id
    ";

    $params = array_merge([$mareos_id], $selected_competitions, [$mareos_id], $selected_competitions);
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
