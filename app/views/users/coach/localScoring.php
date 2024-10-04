<?php
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/CoachAthleteHandler.php';

// Ensure the user is logged in and is a coach
if ($_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$coach_id = $_SESSION['user_id'];

// Fetch athletes under the coach from the database
try {
    $stmt = $pdo->prepare('
        SELECT u.user_id, u.username, p.name, ad.mareos_id, ca.start_date, ca.end_date, ca.updated_at
        FROM coach_athlete ca
        JOIN users u ON ca.athlete_user_id = u.user_id
        JOIN profiles p ON p.user_id = u.user_id
        JOIN athlete_details ad ON ad.user_id = u.user_id
        WHERE ca.coach_user_id = :coach_id
    ');
    $stmt->bindParam(':coach_id', $coach_id);
    $stmt->execute();
    $athletes = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to retrieve athletes: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Header -->
    <div class="row bg-dark text-white py-4 mb-5" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Coach Scoring Dashboard</h3>
        </div>
    </div>

    <!-- Bulk Fetch Button -->
    <button id="fetchSelectedScores" class="btn btn-success mb-4">Save Selected Scores</button>
    <button id="refreshPage" class="btn btn-primary mb-4">Refresh Page</button>

    <!-- Tournament Dropdown -->
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <label for="tournament-select" class="form-label">Choose a tournament:</label>
                <select id="tournament-select" class="form-select">
                    <option value="">Select a tournament</option>
                </select>
            </div>

            <!-- Scores Table -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="ianseo-scores">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="select-all"> All</th>
                            <th>No</th>
                            <th>Mareos ID</th>
                            <th>Athlete Name</th>
                            <th>Competition ID</th>
                            <th>Event Name</th>
                            <th>Distance</th>
                            <th>M 1 Score</th>
                            <th>M 2 Score</th>
                            <th>Total Score</th>
                            <th>10</th>
                            <th>9</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Reusable Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmActionButton" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reusable Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let athletes = <?php echo json_encode($athletes); ?>;
let selectedScores = [];

// Refresh page
document.getElementById('refreshPage').addEventListener('click', function() {
    location.reload(); // Refresh the page
});

// Fetch the list of competitions and populate the dropdown
function fetchTournaments() {
    fetch('https://ianseo.sukanfc.com/fetch_tournaments.php')
        .then(response => response.json())
        .then(data => {
            const tournamentSelect = document.getElementById('tournament-select');
            data.forEach(tournament => {
                const option = document.createElement('option');
                option.value = tournament.ToCode;
                option.text = `[${tournament.ToCode}] ${tournament.ToName}`;
                tournamentSelect.appendChild(option);
            });
        })
        .catch(error => displayError('Error fetching tournaments', error.message));
}

// Uncheck the "Select All" checkbox
document.getElementById('tournament-select').addEventListener('change', function () {
    const tournamentId = this.value;
    
    // Reset the "Select All" checkbox when switching competitions
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
    }

    if (tournamentId) {
        fetchTournamentScores(tournamentId).then(data => {
            fetchSavedScores(tournamentId).then(savedScores => {
                populateTable(tournamentId, data, savedScores);
            });
        });
    }
});

// Fetch scores based on the selected tournament
function fetchTournamentScores(tournamentId) {
    return fetch(`https://ianseo.sukanfc.com/fetch_ianseo.php?tournament_code=${tournamentId}`)
        .then(response => response.json())
        .catch(error => displayError('Error fetching scores', error.message));
}

// Fetch saved scores from the backend
function fetchSavedScores(tournamentId) {
    return fetch(`<?php echo BASE_URL; ?>app/handlers/LocalSavedScoreHandler.php?competition_id=${tournamentId}`)
        .then(response => response.json())
        .catch(error => {
            console.error('Error fetching saved scores:', error);
            return [];
        });
}

// Populate the table with athlete data
function populateTable(tournamentId, data, savedScores) {
    const tableBody = document.querySelector('#ianseo-scores tbody');
    tableBody.innerHTML = '';  // Clear previous data

    let position = 1;
    data.forEach(row => {
        const matchingAthlete = athletes.find(athlete => athlete.mareos_id == row.athlete_id);
        if (matchingAthlete) {
            const isSaved = savedScores.some(saved => saved.mareos_id === row.athlete_id);
            const tableRow = `
                <tr data-athlete-id="${row.athlete_id}">
                    <td><input type="checkbox" class="select-score" data-score='${JSON.stringify(row)}' ${isSaved ? 'disabled' : ''}></td>
                    <td class="align-middle">${position}</td>
                    <td class="align-middle">${row.athlete_id}</td>
                    <td class="align-middle">${matchingAthlete.name}</td>
                    <td class="align-middle">${tournamentId}</td>
                    <td class="align-middle">${row.event_name}</td>
                    <td class="align-middle">${row.event_distance}m</td>
                    <td class="align-middle">${row.m_1_score}</td>
                    <td class="align-middle">${row.m_2_score}</td>
                    <td class="align-middle">${row.total_score}</td>
                    <td class="align-middle">${row.total_10}</td>
                    <td class="align-middle">${row.total_9}</td>
                    <td class="d-flex justify-content-center align-items-center">
                        <button class="btn btn-primary save-btn" data-score='${JSON.stringify(row)}' ${isSaved ? 'disabled' : ''}>${isSaved ? 'Saved' : 'Save Score'}</button>
                    </td>
                </tr>`;
            tableBody.innerHTML += tableRow;

            if (isSaved) updateSavedScoreUI(row.athlete_id);

            position++;
        }
    });

    addEventListeners();
}

// Update UI for saved scores
function updateSavedScoreUI(athleteId) {
    const row = document.querySelector(`tr[data-athlete-id="${athleteId}"]`);
    if (row) {
        row.classList.add('table-success');
        const saveButton = row.querySelector('.save-btn');
        if (saveButton) {
            saveButton.disabled = true;
            saveButton.textContent = 'Saved';
        }
        const checkbox = row.querySelector('.select-score');
        if (checkbox) {
            checkbox.disabled = true;
        }
    }
}

// Event listener functions
function addEventListeners() {
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function () {
            const scoreData = JSON.parse(this.getAttribute('data-score'));
            openConfirmationModal(`Are you sure you want to save this score for ${scoreData.athlete_name}?`, function() {
                saveScore(scoreData).catch(error => displayError('Error saving score', error.message));
            });
        });
    });

    // Avoid saving duplicates
    document.querySelectorAll('.select-score').forEach(checkbox => {
        checkbox.removeEventListener('change', checkboxChangeHandler);
        checkbox.addEventListener('change', checkboxChangeHandler);
    });

    // Exclude checkboxes (saved scores)
    const selectAllCheckbox = document.getElementById('select-all');
    selectAllCheckbox.removeEventListener('change', selectAllHandler);
    selectAllCheckbox.addEventListener('change', selectAllHandler);
}

// Handler for individual checkbox change
function checkboxChangeHandler() {
    const scoreData = JSON.parse(this.getAttribute('data-score'));

    if (this.checked && !this.disabled) {
        selectedScores = selectedScores.filter(score => score.athlete_id !== scoreData.athlete_id); // Ensure no duplicates
        selectedScores.push(scoreData);
    } else {
        selectedScores = selectedScores.filter(score => score.athlete_id !== scoreData.athlete_id);
    }

    console.log(`${selectedScores.length} athletes selected.`);
}

// Handler for "Select All" checkbox
function selectAllHandler() {
    const checkboxes = document.querySelectorAll('.select-score');
    const isChecked = this.checked;
    selectedScores = [];  // Reset selectedScores when selecting all or deselecting all

    checkboxes.forEach(checkbox => {
        if (!checkbox.disabled) {  // Only select checkboxes that aren't disabled (i.e., unsaved scores)
            checkbox.checked = isChecked;
            checkbox.dispatchEvent(new Event('change'));
        }
    });

    console.log(`${selectedScores.length} athletes selected.`);
}

// Save Selected Scores (Batch Save)
document.getElementById('fetchSelectedScores').addEventListener('click', function() {
    if (selectedScores.length > 0) {
        openConfirmationModal(`Are you sure you want to save scores for ${selectedScores.length} athlete(s)?`, function() {
            saveScores(selectedScores);
        });
    } else {
        displayError('Error', 'No scores selected. Please select at least one score to save.');
    }
});

// Save score to the server
function saveScore(scoreData) {
    scoreData.competition_id = document.getElementById('tournament-select').value;

    return fetch('<?php echo BASE_URL; ?>app/handlers/LocalScoreHandler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(scoreData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateSavedScoreUI(scoreData.athlete_id);
        } else if (data.status === 'error' && data.message === 'You have already saved a score for this competition.') {
            updateSavedScoreUI(scoreData.athlete_id);
            displayError('Information', 'The score has already been saved for this athlete.');
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    });
}

// Save multiple scores (batch save)
function saveScores(scores) {
    const promises = scores.map(score => saveScore(score));

    Promise.all(promises)
        .then(() => {
            scores.forEach(score => updateSavedScoreUI(score.athlete_id));
        })
        .catch(error => displayError('Error saving scores', error.message));
}

// Reusable confirmation modal
function openConfirmationModal(message, confirmAction) {
    document.getElementById('confirmationMessage').textContent = message;

    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    modal.show();

    const confirmButton = document.getElementById('confirmActionButton');
    confirmButton.onclick = function() {
        confirmAction();
        modal.hide();
    };
}

// Reusable error modal
function displayError(title, message) {
    document.getElementById('errorMessage').textContent = message;
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
}

// Initialization: Fetch tournaments when the page loads
fetchTournaments();

</script>

<?php include '../../layouts/dashboard/footer.php'; ?>
