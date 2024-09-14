<?php
function getAthleteData($pdo, $user_id) {
    $stmt = $pdo->prepare('SELECT athlete_id FROM athlete_details WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCompetitionScores($pdo, $athlete_id) {
    $stmt = $pdo->prepare('
        SELECT 
            s.competition_id, s.score_50m_1, s.score_50m_2, s.total_score, s.tens_and_x, s.x_count, s.win_status, s.rank, 
            c.competition_name, c.start_date, c.end_date
        FROM scores s
        JOIN competitions c ON s.competition_id = c.competition_id
        WHERE s.athlete_id = ? 
        ORDER BY c.start_date DESC
    ');
    $stmt->execute([$athlete_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAverageStats($pdo, $athlete_id) {
    $stmt = $pdo->prepare('
        SELECT 
            AVG(score_50m_1) AS avg_50m_1, 
            AVG(score_50m_2) AS avg_50m_2, 
            AVG(total_score) AS avg_total_score, 
            AVG(tens_and_x) AS avg_tens_and_x, 
            AVG(x_count) AS avg_x_count
        FROM scores
        WHERE athlete_id = ?
    ');
    $stmt->execute([$athlete_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBestAndLowestStats($pdo, $athlete_id) {
    $stmt = $pdo->prepare('
        SELECT 
            MAX(total_score) AS best_total_score, 
            MAX(tens_and_x) AS best_tens_and_x, 
            MAX(x_count) AS best_x_count,
            MIN(total_score) AS lowest_total_score,
            MIN(tens_and_x) AS lowest_tens_and_x,
            MIN(x_count) AS lowest_x_count
        FROM scores
        WHERE athlete_id = ?
    ');
    $stmt->execute([$athlete_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getWinLossStats($pdo, $athlete_id) {
    $stmt = $pdo->prepare('
        SELECT 
            SUM(CASE WHEN win_status = "w" THEN 1 ELSE 0 END) AS wins,
            SUM(CASE WHEN win_status = "l" THEN 1 ELSE 0 END) AS losses,
            COUNT(competition_id) AS total_competitions
        FROM scores
        WHERE athlete_id = ?
    ');
    $stmt->execute([$athlete_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getMonthlyCompetitionData($pdo, $athlete_id) {
    $stmt = $pdo->prepare('
        SELECT 
            MONTH(c.start_date) AS competition_month, 
            COUNT(s.competition_id) AS competition_count
        FROM scores s
        JOIN competitions c ON s.competition_id = c.competition_id
        WHERE s.athlete_id = ?
        GROUP BY competition_month
        ORDER BY competition_month
    ');
    $stmt->execute([$athlete_id]);
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
