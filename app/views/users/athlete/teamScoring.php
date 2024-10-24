<?php
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch mareos_id of the logged-in athlete
try {
    $stmt = $pdo->prepare('SELECT mareos_id FROM athlete_details WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $athlete = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($athlete && !empty($athlete['mareos_id'])) {
        $currentMareosId = $athlete['mareos_id'];
    } else {
        echo json_encode(['error' => 'No mareos_id found']);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}

// Fetch all local training scores for the logged-in athlete
try {
    $stmt = $pdo->prepare('SELECT * FROM team_training_scores WHERE mareos_id = ? ORDER BY created_at DESC');
    $stmt->execute([$currentMareosId]);
    $teamScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
} 
?>

<div class="row">
    <div class="col-12 mb-3">
        <!-- Button to Open Modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#fetchTrainingModal">
            Fetch New Training Scores
        </button>
        <button id="refreshPage" class="btn btn-primary">Refresh Page</button>
    </div>
</div>

<div class="row">
    <!-- Modal for Fetching Training Scores -->
    <div class="modal fade" id="fetchTrainingModal" tabindex="-1" aria-labelledby="fetchTrainingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fetchTrainingModalLabel">Fetch New Training Scores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Training Session Dropdown -->
                    <div class="mb-4">
                        <select id="training-select" class="form-select p-3">
                            <option value="">Select a training session</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <!-- Input Field for Training Session ID -->
                        <input type="text" id="training-id-input" class="form-control" placeholder="Or enter training session ID">
                    </div>

                    <!-- Training Scores Table -->
                    <div class="table-responsive" id="trainingScoresTableContainer">
                        <table class="table table-striped table-bordered" id="training-scores">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Training ID</th>
                                    <th>Session Name</th>
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

                    <!-- No Athlete Found Message -->
                    <div id="noAthleteFoundMessage" class="alert alert-danger d-none">
                        No athlete found for the selected training session ID: <span id="noAthleteTrainingId"></span> 
                        <br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="fetchModal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Training Scores Table -->
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="team-training-scores-table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Training ID</th>
                        <th>Session Name</th>
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
                        <th>Date Saved</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teamScores)): ?>
                        <?php foreach ($teamScores as $index => $score): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($score['training_id']); ?></td>
                                <td><?php echo htmlspecialchars($score['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($score['event_distance']); ?>m</td>
                                <td class="table-primary"><?php echo htmlspecialchars($score['m1_score']); ?></td>
                                <td class="table-primary"><?php echo htmlspecialchars($score['m1_10X']); ?></td>
                                <td class="table-primary"><?php echo htmlspecialchars($score['m1_109']); ?></td>
                                <td class="table-info"><?php echo htmlspecialchars($score['m2_score']); ?></td>
                                <td class="table-info"><?php echo htmlspecialchars($score['m2_10X']); ?></td>
                                <td class="table-info"><?php echo htmlspecialchars($score['m2_109']); ?></td>
                                <td><?php echo htmlspecialchars($score['total_score']); ?></td>
                                <td><?php echo htmlspecialchars($score['total_10X']); ?></td>
                                <td><?php echo htmlspecialchars($score['total_109']); ?></td>
                                <td><?php echo htmlspecialchars($score['created_at']); ?></td>
                                <td class="d-flex justify-content-center align-items-center">
                                    <!-- Delete Button -->
                                    <button class="btn btn-danger delete-btn" data-score-id="<?php echo $score['score_id']; ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="15" class="text-center">No local training scores found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reusable Confirmation -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmButton" class="btn btn-primary">Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
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

<script>
// Refresh page    
const fetchModal = document.getElementById('fetchTrainingModal'); 

fetchModal.addEventListener('hidden.bs.modal', function () {
    location.reload();
});

document.getElementById('refreshPage').addEventListener('click', function() {
    location.reload();
});

// Modal score fetch
const currentMareosId = '<?php echo $currentMareosId; ?>';

// Fetch the list of training sessions
fetch('<?php echo TRAIN_LIST_URL; ?>')
    .then(response => response.json())
    .then(data => {
        const trainingSelect = document.getElementById('training-select');
        data.forEach(session => {
            const option = document.createElement('option');
            option.value = session.ToCode;
            option.text = `[${session.ToCode}] ${session.ToName} —— ${session.ToWhenFrom} to ${session.ToWhenTo}`;
            trainingSelect.appendChild(option);
        });
    })
    .catch(error => console.error('Error fetching training sessions:', error));

// Fetch scores based on the training session ID or selected session
function fetchScores() {
    const inputField = document.getElementById('training-id-input').value.trim();
    const dropdownValue = document.getElementById('training-select').value;
    const trainingId = inputField || dropdownValue;

    if (trainingId) {
        fetch(`<?php echo TRAIN_SCORE_URL; ?>?tournament_code=${trainingId}`)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('#training-scores tbody');
                const noAthleteFoundMessage = document.getElementById('noAthleteFoundMessage');
                const scoresTableContainer = document.getElementById('trainingScoresTableContainer');

                tableBody.innerHTML = ''; 
                let position = 1;
                let athleteFound = false;
                
                fetchSavedScores(trainingId).then(savedScores => {
                    data.forEach(row => {
                        if (row.athlete_id === currentMareosId) {
                            athleteFound = true;
                            const isSaved = savedScores.some(saved => saved.mareos_id === row.athlete_id);
                            const tableRow = `
                                <tr>
                                    <td class="align-middle">${position}</td>
                                    <td class="align-middle">${trainingId}</td>
                                    <td class="align-middle">${row.event_name}</td>
                                    <td class="align-middle">${row.event_distance}m</td>
                                    <td class="align-middle table-primary">${row.m1_score}</td>
                                    <td class="align-middle table-primary">${row['m1_10X']}</td>
                                    <td class="align-middle table-primary">${row['m1_109']}</td>
                                    <td class="align-middle table-info">${row.m2_score}</td>
                                    <td class="align-middle table-info">${row['m2_10X']}</td>
                                    <td class="align-middle table-info">${row['m2_109']}</td>
                                    <td class="align-middle">${row.total_score}</td>
                                    <td class="align-middle">${row.total_10X}</td>
                                    <td class="align-middle">${row.total_109}</td>
                                    <td class="d-flex justify-content-center align-items-center">
                                        <button class="btn btn-primary save-btn" data-score='${JSON.stringify(row)}' ${isSaved ? 'disabled' : ''}>${isSaved ? 'Saved' : 'Save Score'}</button>
                                    </td>
                                </tr>`;
                            tableBody.innerHTML += tableRow;
                            position++;
                        }
                    });

                    if (!athleteFound) {
                        scoresTableContainer.classList.add('d-none');
                        noAthleteFoundMessage.classList.remove('d-none');
                        document.getElementById('noAthleteTrainingId').textContent = trainingId;
                    } else {
                        scoresTableContainer.classList.remove('d-none');
                        noAthleteFoundMessage.classList.add('d-none');
                    }
                    // Add event listeners to the save buttons
                    document.querySelectorAll('.save-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const scoreData = JSON.parse(this.getAttribute('data-score'));
                            const message = `Do you want to save the score for training session: ${trainingId}, total score: ${scoreData.total_score}?`;

                            openConfirmModal(message, function () {
                                saveTrainingScore(scoreData); 
                            });
                        });
                    });
                });
            })
            .catch(error => console.error('Error fetching training scores:', error));
    }
}

// Fetch saved training scores for the logged-in athlete
function fetchSavedScores(trainingId) {
    return fetch(`<?php echo BASE_URL; ?>app/handlers/TrainTeamSavedScoreHandler.php?training_id=${trainingId}`)
        .then(response => response.json())
        .catch(error => {
            console.error('Error fetching saved training scores:', error);
            return [];
        });
}

// Debounce function to prevent multiple requests while typing
function debounce(func, delay) {
    let debounceTimer;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.apply(context, args), delay);
    }
}

// Convert input to uppercase
document.getElementById('training-id-input').addEventListener('input', function() {
    this.value = this.value.toUpperCase();  
});

// Training session dropdown
document.getElementById('training-select').addEventListener('change', function () {
    const trainingId = this.value;
    document.getElementById('training-id-input').value = '';
    fetchScores();  
});

// Training session ID 
document.getElementById('training-id-input').addEventListener('keyup', debounce(function () {
    const trainingId = this.value.trim();
    if (trainingId) {
        document.getElementById('training-select').value = ''; 
    }
    fetchScores();  
}, 500));  

// Save training score to the server
function saveTrainingScore(scoreData) {
    let trainingId = document.getElementById('training-id-input').value.trim() || document.getElementById('training-select').value;
    
    if (!trainingId) {
        alert('No training session selected or entered.');
        return;
    }
    trainingId = trainingId.toUpperCase();
    scoreData.training_id = trainingId;

    fetch('<?php echo BASE_URL; ?>app/handlers/TrainTeamScoringHandler.php', {
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();
        } else if (data.status === 'error' && data.message === 'You have already saved a score for this training session.') {
            updateSavedScoreUI(scoreData.athlete_id);
            showMessage('Information', 'The score has already been saved.');
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();  
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        showMessage('Error', 'An error occurred while saving the training score.');
        console.error('Error saving training score:', error);
    });
}

// Function to delete a training score
function deleteTrainingScore(scoreId) {
    fetch(`<?php echo BASE_URL; ?>app/handlers/TrainTeamScoringHandler.php?score_id=${scoreId}`, {
        method: 'DELETE',
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.querySelector(`button[data-score-id="${scoreId}"]`).closest('tr').remove();
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();  
            showMessage('Success', 'Training score deleted successfully'); 
        } else {
            throw new Error(data.message || 'Failed to delete the training score');
        }
    })
    .catch(error => showMessage('Error', error.message));
}

// Update UI for saved training scores (for athletes)
function updateSavedScoreUI(athleteId) {
    const rows = document.querySelectorAll('#training-scores tbody tr');
    rows.forEach(row => {
        const saveButton = row.querySelector('.save-btn');
        if (saveButton && saveButton.dataset.score) {
            const scoreData = JSON.parse(saveButton.dataset.score);
            if (scoreData.athlete_id === athleteId) {
                row.classList.add('table-success');  // Mark the row as saved
                saveButton.disabled = true;  // Disable the Save button
                saveButton.textContent = 'Saved';  // Change the button text to "Saved"
            }
        }
    });
}

// Function to open confirmation modal
function openConfirmModal(message, callback) {
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    document.getElementById('confirmMessage').textContent = message;
    const confirmButton = document.getElementById('confirmButton');

    // Clear previous listeners
    confirmButton.removeEventListener('click', confirmButton.listener);

    // Add the new listener and store it
    confirmButton.listener = () => callback();
    confirmButton.addEventListener('click', confirmButton.listener);

    confirmModal.show();
}

// Reusable function to display messages in error modal
function showMessage(title, message) {
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    document.getElementById('errorModalLabel').textContent = title;
    document.getElementById('errorMessage').textContent = message;
    modal.show();

    const errorModal = document.getElementById('errorModal');
    errorModal.addEventListener('hidden.bs.modal', function () {
        location.reload();
    });
}

// Handle delete button click
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function () {
        const scoreId = this.getAttribute('data-score-id');
        openConfirmModal('Are you sure you want to delete this training score?', function () {
            deleteTrainingScore(scoreId);
        });
    });
});
</script>