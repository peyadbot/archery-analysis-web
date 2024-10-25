<?php
$title = 'Athletes Training Scoring';
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/CoachAthleteHandler.php';

if ($_SESSION['role'] !== 'coach') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$coach_id = $_SESSION['user_id'];

// Fetch athletes under the coach from the database
try {
    $stmt = $pdo->prepare('
        SELECT u.user_id, u.username, p.name, p.mareos_id, ca.start_date, ca.end_date, ca.updated_at
        FROM coach_athlete ca
        JOIN users u ON ca.athlete_user_id = u.user_id
        JOIN profiles p ON p.user_id = u.user_id
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
            <h3 class="m-0">Athletes Training Scoring Dashboard</h3>
        </div>
    </div>

    <!-- Bulk Fetch Button -->
    <button id="fetchSelectedScores" class="btn btn-success mb-4">Save Selected Scores</button>
    <button id="refreshPage" class="btn btn-primary mb-4">Refresh Page</button>

    <div class="row">
        <div class="col-12">
            <!-- Dropdown -->
            <div class="mb-4">
                <label for="training-select" class="form-label">Choose a training session:</label>
                <select id="training-select" class="form-select">
                    <option value="">Select a training session</option>
                </select>
            </div>

            <!-- ID search -->
            <div class="mb-4">
                <label for="training-id-input" class="form-label">Or enter training ID:</label>
                <input type="text" id="training-id-input" class="form-control" placeholder="Enter training ID">
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
                            <th>Training ID</th>
                            <th>Event Name</th>
                            <th>Distance</th>
                            <th>M1 Score</th>
                            <th>M1 10+X</th>
                            <th>M1 10/9</th>
                            <th>M2 Score</th>
                            <th>M2 10+X</th>
                            <th>M2 10/9</th>
                            <th>Total Score</th>
                            <th>Total 10+X</th>
                            <th>Total 10/9</th>
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
    location.reload();
});

// Fetch the list of training sessions and populate the dropdown
function fetchTrainings() {
    fetch('<?php echo TRAIN_LIST_URL; ?>')
        .then(response => response.json())
        .then(data => {
            const trainingSelect = document.getElementById('training-select');
            data.forEach(training => {
                const option = document.createElement('option');
                option.value = training.ToCode;
                option.text = `[${training.ToCode}] ${training.ToName}`;
                trainingSelect.appendChild(option);
            });
        })
        .catch(error => displayError('Error fetching training sessions', error.message));
}

// Fetch scores based on the selected training session
function fetchTrainingScores(trainingId) {
    return fetch(`<?php echo TRAIN_SCORE_URL; ?>?tournament_code=${trainingId}`)
        .then(response => response.json())
        .then(data => {
            fetchSavedScores(trainingId).then(savedScores => {
                populateTable(trainingId, data, savedScores);
            });
        })
        .catch(error => displayError('Error fetching scores', error.message));
}

// Fetch saved scores from the backend
function fetchSavedScores(trainingId) {
    return fetch(`<?php echo BASE_URL; ?>app/handlers/TrainTeamSavedScoreHandler.php?training_id=${trainingId}`)
        .then(response => response.json())
        .catch(error => {
            console.error('Error fetching saved scores:', error);
            return [];
        });
}

// Populate the table with athlete data
function populateTable(trainingId, data, savedScores) {
    const tableBody = document.querySelector('#ianseo-scores tbody');
    tableBody.innerHTML = ''; 

    let position = 1;
    let athletesFound = false;  

    data.forEach(row => {
        const matchingAthlete = athletes.find(athlete => athlete.mareos_id == row.athlete_id);
        if (matchingAthlete) {
            athletesFound = true;  
            const isSaved = savedScores.some(saved => saved.mareos_id === row.athlete_id);
            const tableRow = `
                <tr data-athlete-id="${row.athlete_id}">
                    <td><input type="checkbox" class="select-score" data-score='${JSON.stringify(row)}' ${isSaved ? 'disabled' : ''}></td>
                    <td class="align-middle">${position}</td>
                    <td class="align-middle">${row.athlete_id}</td>
                    <td class="align-middle">${matchingAthlete.name}</td>
                    <td class="align-middle">${trainingId}</td>
                    <td class="align-middle">${row.event_name}</td>
                    <td class="align-middle">${row.event_distance}m</td>
                    <td class="table-primary align-middle">${row.m1_score}</td>
                    <td class="table-primary align-middle">${row.m1_10X}</td>
                    <td class="table-primary align-middle">${row.m1_109}</td>
                    <td class="table-info align-middle">${row.m2_score}</td>
                    <td class="table-info align-middle">${row.m2_10X}</td>
                    <td class="table-info align-middle">${row.m2_109}</td>
                    <td class="align-middle">${row.total_score}</td>
                    <td class="align-middle">${row.total_10X}</td>
                    <td class="align-middle">${row.total_109}</td>
                    <td class="d-flex justify-content-center align-items-center">
                        <button class="btn btn-primary save-btn" data-score='${JSON.stringify({...row, athlete_name: matchingAthlete.name})}' ${isSaved ? 'disabled' : ''}>${isSaved ? 'Saved' : 'Save Score'}</button>
                    </td>
                </tr>`;
            tableBody.innerHTML += tableRow;
            if (isSaved) updateSavedScoreUI(row.athlete_id);
            position++;
        }
    });

    if (!athletesFound) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="13" class="text-center">No athletes found for this training session.</td>
            </tr>`;
    }
    addEventListeners();  
}

// Event listener functions
function addEventListeners() {
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function () {
            const scoreData = JSON.parse(this.getAttribute('data-score'));
            openConfirmationModal(`Are you sure you want to save this for ${scoreData.athlete_name}, Score: ${scoreData.total_score}?`, function() {
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

// Event listener for training ID input
document.getElementById('training-id-input').addEventListener('input', function() {
    this.value = this.value.toUpperCase();  
    const trainingId = this.value.trim();
    if (trainingId) {
        document.getElementById('training-select').value = '';
        fetchTrainingScores(trainingId);
    }
});

// Handler for "Select All" checkbox
function selectAllHandler() {
    const checkboxes = document.querySelectorAll('.select-score');
    const isChecked = this.checked;
    selectedScores = [];  

    checkboxes.forEach(checkbox => {
        if (!checkbox.disabled) {  
            checkbox.checked = isChecked;
            checkbox.dispatchEvent(new Event('change'));
        }
    });

    console.log(`${selectedScores.length} athletes selected.`);
}

// Handler individual checkbox 
function checkboxChangeHandler() {
    const scoreData = JSON.parse(this.getAttribute('data-score'));

    if (this.checked && !this.disabled) {
        selectedScores = selectedScores.filter(score => score.athlete_id !== scoreData.athlete_id); 
        selectedScores.push(scoreData);
    } else {
        selectedScores = selectedScores.filter(score => score.athlete_id !== scoreData.athlete_id);
    }

    console.log(`${selectedScores.length} athletes selected.`);
}

// Uncheck checkbox
document.getElementById('training-select').addEventListener('change', function () {
    const trainingId = this.value;
    const selectAllCheckbox = document.getElementById('select-all');

    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
    }

    if (trainingId) {
        document.getElementById('training-id-input').value = '';  
        fetchTrainingScores(trainingId);
    }
});

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

// Save multiple scores (batch save)
function saveScores(scores) {
    const promises = scores.map(score => saveScore(score));

    Promise.all(promises)
        .then(() => {
            scores.forEach(score => updateSavedScoreUI(score.athlete_id));
        })
        .catch(error => displayError('Error saving scores', error.message));
}

// Save score to the server
function saveScore(scoreData) {
    scoreData.training_id = document.getElementById('training-select').value || document.getElementById('training-id-input').value;

    return fetch('<?php echo BASE_URL; ?>app/handlers/TrainTeamScoringHandler.php', {
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
        } else if (data.status === 'error' && data.message === 'You have already saved a score for this training session.') {
            updateSavedScoreUI(scoreData.athlete_id);
            displayError('Information', 'The score has already been saved for this athlete.');
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    });
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

fetchTrainings();
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>