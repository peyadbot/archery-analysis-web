<?php 

// Functions to format training details
function formatTrainingBowTypes($training) {
    $bowTypes = [];
    if ($training['bow_type_recurve']) $bowTypes[] = 'R';
    if ($training['bow_type_compound']) $bowTypes[] = 'C';
    if ($training['bow_type_barebow']) $bowTypes[] = 'BB';
    return implode(', ', $bowTypes);
}

function formatTrainingEventTypes($training) {
    $eventTypes = [];
    if ($training['event_type_individual']) $eventTypes[] = 'Ind';
    if ($training['event_type_team']) $eventTypes[] = 'T';
    if ($training['event_type_mixed_team']) $eventTypes[] = 'MT';
    return implode(', ', $eventTypes);
}


// Retrieve form inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_criteria = isset($_GET['search_criteria']) ? $_GET['search_criteria'] : 'name';
$filter_bow_type = isset($_GET['filter_bow_type']) ? $_GET['filter_bow_type'] : '';
$filter_event_type = isset($_GET['filter_event_type']) ? $_GET['filter_event_type'] : '';

// Base query
$query = '
    SELECT trainings.*, users.username 
    FROM trainings
    JOIN users ON trainings.added_by = users.user_id'; 

// Add search filtering
if (!empty($search)) {
    if ($search_criteria === 'name') {
        $query .= ' AND trainings.training_name LIKE :search';
    } elseif ($search_criteria === 'location') {
        $query .= ' AND trainings.location LIKE :search';
    } elseif ($search_criteria === 'id') {
        $query .= ' AND trainings.training_id = :search';
    }
}

// Add bow type filtering
if (!empty($filter_bow_type)) {
    if ($filter_bow_type === 'recurve') {
        $query .= ' AND trainings.bow_type_recurve = 1';
    } elseif ($filter_bow_type === 'compound') {
        $query .= ' AND trainings.bow_type_compound = 1';
    } elseif ($filter_bow_type === 'barebow') {
        $query .= ' AND trainings.bow_type_barebow = 1';
    }
}

// Add event type filtering
if (!empty($filter_event_type)) {
    if ($filter_event_type === 'individual') {
        $query .= ' AND trainings.event_type_individual = 1';
    } elseif ($filter_event_type === 'team') {
        $query .= ' AND trainings.event_type_team = 1';
    } elseif ($filter_event_type === 'mixed_team') {
        $query .= ' AND trainings.event_type_mixed_team = 1';
    }
}

// Prepare and execute the query
$stmt = $pdo->prepare($query);

// Bind parameters
if (!empty($search)) {
    $stmt->bindValue(':search', $search_criteria === 'id' ? $search : "%$search%");
}

$stmt->execute();
$trainings = $stmt->fetchAll();