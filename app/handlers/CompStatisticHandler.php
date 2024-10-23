<?php
function getAthleteData($pdo, $user_id) {
    $stmt = $pdo->prepare('SELECT mareos_id FROM athlete_details WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCompetitionScores($pdo, $mareos_id, $competition_type, $start_date = null, $end_date = null) {
    $table = $competition_type == 'international' ? 'international_comp_scores' : 'local_comp_scores';

    $query = "
        SELECT 
            competition_id, m1_score, m1_10X, m1_109, m2_score, m2_10X, m2_109, 
            total_score, total_10X, total_109, event_name, event_distance, created_at
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

function getAverageStats($pdo, $mareos_id, $competition_type, $start_date = null, $end_date = null) {
    $table = $competition_type == 'international' ? 'international_comp_scores' : 'local_comp_scores';
    
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
    
    // Apply date range filtering
    if ($start_date && $end_date) {
        $query .= " AND created_at BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBestAndLowestStats($pdo, $mareos_id, $competition_type, $start_date = null, $end_date = null) {
    $table = $competition_type == 'international' ? 'international_comp_scores' : 'local_comp_scores';

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
    
    // Apply date range filtering
    if ($start_date && $end_date) {
        $query .= " AND created_at BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getMonthlyCompetitionData($pdo, $mareos_id, $competition_type) {
    $table = $competition_type == 'international' ? 'international_comp_scores' : 'local_comp_scores';
    
    $stmt = $pdo->prepare("
        SELECT 
            MONTH(created_at) AS competition_month, 
            COUNT(competition_id) AS competition_count
        FROM $table
        WHERE mareos_id = ?
        GROUP BY competition_month
        ORDER BY competition_month ASC
    ");
    $stmt->execute([$mareos_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function prepareMonthlyCompetitions($monthly_competition_data) {
    $monthly_competitions = array_fill(1, 12, 0); // 12 months, starting from January

    foreach ($monthly_competition_data as $data) {
        $monthly_competitions[$data['competition_month']] = $data['competition_count'];
    }

    return $monthly_competitions;
}

