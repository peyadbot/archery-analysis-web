<?php

function getAthleteData($pdo, $user_id) {
    // Assuming athlete_id corresponds to user_id in the local_comp_scores/international_comp_scores table
    $stmt = $pdo->prepare('SELECT mareos_id FROM athlete_details WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCompetitionScores($pdo, $mareos_id, $competition_type) {
    $table = $competition_type == 'international' ? 'international_comp_scores' : 'local_comp_scores';
    
    $stmt = $pdo->prepare("
        SELECT 
            competition_id, m_1_score, m_2_score, total_score, total_10, total_9, 
            event_name, event_distance, created_at
        FROM $table
        WHERE mareos_id = ? 
        ORDER BY created_at ASC
    ");
    $stmt->execute([$mareos_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAverageStats($pdo, $mareos_id, $competition_type) {
    $table = $competition_type == 'international' ? 'international_comp_scores' : 'local_comp_scores';
    
    $stmt = $pdo->prepare("
        SELECT 
            AVG(m_1_score) AS avg_m1, 
            AVG(m_2_score) AS avg_m2, 
            AVG(total_score) AS avg_total_score, 
            AVG(total_10) AS avg_total_10, 
            AVG(total_9) AS avg_total_9
        FROM $table
        WHERE mareos_id = ?
    ");
    $stmt->execute([$mareos_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBestAndLowestStats($pdo, $mareos_id, $competition_type) {
    $table = $competition_type == 'international' ? 'international_comp_scores' : 'local_comp_scores';

    $stmt = $pdo->prepare("
        SELECT 
            MAX(total_score) AS best_total_score, 
            MAX(total_10) AS best_total_10, 
            MAX(total_9) AS best_total_9,
            MIN(total_score) AS lowest_total_score,
            MIN(total_10) AS lowest_total_10,
            MIN(total_9) AS lowest_total_9
        FROM $table
        WHERE mareos_id = ?
    ");
    $stmt->execute([$mareos_id]);
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

    // Populate the array with actual data
    foreach ($monthly_competition_data as $data) {
        $monthly_competitions[$data['competition_month']] = $data['competition_count'];
    }

    return $monthly_competitions;
}

