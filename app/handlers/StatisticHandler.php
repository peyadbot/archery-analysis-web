<?php
function getAthleteData($pdo, $user_id) {
    $stmt = $pdo->prepare('SELECT mareos_id FROM profiles WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getScores($pdo, $mareos_id, $type, $view, $start_date = null, $end_date = null) {
    if (!$mareos_id || !$type || !$view) {
        return [];
    }

    $table = ($type === 'competition')
        ? ($view === 'international' ? 'international_comp_scores' : 'local_comp_scores')
        : ($view === 'team' ? 'team_training_scores' : 'personal_training_scores');
    
    $id_column = ($type === 'competition') ? 'competition_id' : 'training_id';

    $query = "
        SELECT 
            $id_column, event_name, event_distance, 
            m1_score, m1_10X, m1_109, m2_score, m2_10X, m2_109, 
            total_score, total_10X, total_109, created_at
        FROM $table
        WHERE mareos_id = ?
    ";
    $params = [$mareos_id];
    
    if ($start_date && $end_date) {
        $end_date .= ' 23:59:59';
        $query .= " AND created_at BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }
    $query .= " ORDER BY created_at ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAverageStats($pdo, $mareos_id, $type, $view, $start_date = null, $end_date = null) {
    if (!$mareos_id || !$type || !$view) {
        return [];
    }

    $table = ($type === 'competition')
        ? ($view === 'local' ? 'local_comp_scores' : 'international_comp_scores')
        : ($view === 'team' ? 'team_training_scores' : 'personal_training_scores');

    $query = "
        SELECT 
            AVG(total_score / 72) AS avg_total_score, 
            AVG(total_10X / 72) AS avg_total_10X, 
            AVG(total_109 / 72) AS avg_total_109,
            AVG(m1_score / 36) AS avg_m1_per_arrow, 
            AVG(m2_score / 36) AS avg_m2_per_arrow, 
            AVG(total_score / 72) AS avg_total_per_arrow, 
            AVG(total_10X / 72) AS avg_10X_per_arrow, 
            AVG(total_109 / 72) AS avg_109_per_arrow,
            AVG(m1_10X / 36) AS avg_m1_10X_per_arrow,
            AVG(m1_109 / 36) AS avg_m1_109_per_arrow,
            AVG(m2_10X / 36) AS avg_m2_10X_per_arrow,
            AVG(m2_109 / 36) AS avg_m2_109_per_arrow
        FROM $table
        WHERE mareos_id = ?
    ";
    
    $params = [$mareos_id];

    if ($start_date && $end_date) {
        $query .= " AND created_at BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBestAndLowestStats($pdo, $mareos_id, $type, $view, $start_date = null, $end_date = null) {
    if (!$mareos_id || !$type || !$view) {
        return [];
    }

    $table = ($type === 'competition')
        ? ($view === 'local' ? 'local_comp_scores' : 'international_comp_scores')
        : ($view === 'team' ? 'team_training_scores' : 'personal_training_scores');

    $query = "
        SELECT 
            MAX(total_score) AS best_total_score, 
            MAX(total_10X) AS best_total_10X, 
            MAX(total_109) AS best_total_109,
            MIN(total_score) AS lowest_total_score,
            MIN(total_10X) AS lowest_total_10X,
            MIN(total_109) AS lowest_total_109,

            MAX(m1_score) AS best_m1_score,
            MIN(m1_score) AS lowest_m1_score,
            MAX(m1_10X) AS max_m1_10X,
            MIN(m1_10X) AS min_m1_10X,
            MAX(m1_109) AS max_m1_109,
            MIN(m1_109) AS min_m1_109,
            
            MAX(m2_score) AS best_m2_score,
            MIN(m2_score) AS lowest_m2_score,
            MAX(m2_10X) AS max_m2_10X,
            MIN(m2_10X) AS min_m2_10X,
            MAX(m2_109) AS max_m2_109,
            MIN(m2_109) AS min_m2_109
        FROM $table
        WHERE mareos_id = ?
    ";

    $params = [$mareos_id];
    
    if ($start_date && $end_date) {
        $query .= " AND created_at BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getMonthlyMatchData($pdo, $mareos_id, $type, $view) {
    if (!$mareos_id || !$type || !$view) {
        return [];
    }

    $table = ($type === 'competition')
        ? ($view === 'local' ? 'local_comp_scores' : 'international_comp_scores')
        : ($view === 'team' ? 'team_training_scores' : 'personal_training_scores');

    $stmt = $pdo->prepare("
        SELECT 
            MONTH(created_at) AS month, 
            COUNT(*) AS count
        FROM $table
        WHERE mareos_id = ?
        GROUP BY month
        ORDER BY month ASC
    ");
    $stmt->execute([$mareos_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function prepareMonthlyMatchs($monthly_match_data) {
    $monthly_matchs = array_fill(1, 12, 0); // Initialize an array for 12 months

    foreach ($monthly_match_data as $data) {
        $monthly_matchs[$data['month']] = $data['count']; 
    }

    return $monthly_matchs;
}
