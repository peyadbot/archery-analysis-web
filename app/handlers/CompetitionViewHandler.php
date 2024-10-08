<?php
// Function to format bow types
function formatCompetitionBowTypes($competition) {
    $bowTypes = [];
    if ($competition['bow_type_recurve']) $bowTypes[] = 'R';
    if ($competition['bow_type_compound']) $bowTypes[] = 'C';
    if ($competition['bow_type_barebow']) $bowTypes[] = 'BB';
    return implode(', ', $bowTypes);
}

// Function to format event types
function formatCompetitionEventTypes($competition) {
    $eventTypes = [];
    if ($competition['event_type_individual']) $eventTypes[] = 'Ind';
    if ($competition['event_type_team']) $eventTypes[] = 'T';
    if ($competition['event_type_mixed_team']) $eventTypes[] = 'MT';
    return implode(', ', $eventTypes);
}

$user_id = isset($_SESSION['user_id']);

// Retrieve form inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_criteria = isset($_GET['search_criteria']) ? $_GET['search_criteria'] : 'name';
$filter_bow_type = isset($_GET['filter_bow_type']) ? $_GET['filter_bow_type'] : '';
$filter_event_type = isset($_GET['filter_event_type']) ? $_GET['filter_event_type'] : '';
$filter_level = isset($_GET['filter_level']) ? $_GET['filter_level'] : '';
$isViewingMyCompetitions = isset($_GET['view']) && $_GET['view'] === 'my';

// Base query
$query = '
    SELECT competitions.*, users.username, event_levels.level_name 
    FROM competitions
    JOIN users ON competitions.added_by = users.user_id
    JOIN event_levels ON competitions.level_id = event_levels.level_id
    WHERE 1=1'; 


if ($isViewingMyCompetitions) {
    $query .= ' AND competitions.added_by = :user_id';
}

// Add search filtering
if (!empty($search)) {
    if ($search_criteria === 'name') {
        $query .= ' AND competitions.competition_name LIKE :search';
    } elseif ($search_criteria === 'location') {
        $query .= ' AND competitions.location LIKE :search';
    } elseif ($search_criteria === 'id') {
        $query .= ' AND competitions.competition_id = :search';
    }
}

// Add bow type filtering
if (!empty($filter_bow_type)) {
    if ($filter_bow_type === 'recurve') {
        $query .= ' AND competitions.bow_type_recurve = 1';
    } elseif ($filter_bow_type === 'compound') {
        $query .= ' AND competitions.bow_type_compound = 1';
    } elseif ($filter_bow_type === 'barebow') {
        $query .= ' AND competitions.bow_type_barebow = 1';
    }
}

// Add event type filtering
if (!empty($filter_event_type)) {
    if ($filter_event_type === 'individual') {
        $query .= ' AND competitions.event_type_individual = 1';
    } elseif ($filter_event_type === 'team') {
        $query .= ' AND competitions.event_type_team = 1';
    } elseif ($filter_event_type === 'mixed_team') {
        $query .= ' AND competitions.event_type_mixed_team = 1';
    }
}

// Add level filtering
if (!empty($filter_level)) {
    $query .= ' AND competitions.level_id = :filter_level';
}

// Prepare and execute the query
$stmt = $pdo->prepare($query);

// Bind parameters
if (!empty($search)) {
    $stmt->bindValue(':search', $search_criteria === 'id' ? $search : "%$search%");
}

if (!empty($filter_level)) {
    $stmt->bindValue(':filter_level', $filter_level, PDO::PARAM_INT);
}

if ($isViewingMyCompetitions) {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

$stmt->execute();
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);

