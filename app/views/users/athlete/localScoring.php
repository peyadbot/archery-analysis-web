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

// Fetch all local scores for the logged-in athlete
try {
    $stmt = $pdo->prepare('SELECT * FROM local_comp_scores WHERE mareos_id = ? ORDER BY created_at DESC');
    $stmt->execute([$currentMareosId]);
    $localScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}
?>

<div class="row">
    <div class="col-12 mb-3">
        <!-- Button to Open Modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#fetchScoresModal">
            Fetch New Scores
        </button>
        <button id="refreshPage" class="btn btn-primary">Refresh Page</button>
    </div>
</div>

<div class="row">
    <!-- Modal for Fetching Scores -->
    <div class="modal fade" id="fetchScoresModal" tabindex="-1" aria-labelledby="fetchScoresModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fetchScoresModalLabel">Fetch New Scores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tournament Dropdown -->
                    <div class="mb-4">
                        <select id="tournament-select" class="form-select p-3">
                            <option value="">Select a tournament</option>
                        </select>
                    </div>

                    <!-- Scores Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="ianseo-scores">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="fetchModal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Local Tournament Scores Table -->
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="local-scores-table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Competition ID</th>
                        <th>Event Name</th>
                        <th>Distance</th>
                        <th>M-1 Score</th>
                        <th>M-2 Score</th>
                        <th>10</th>
                        <th>9</th>
                        <th>Total Score</th>
                        <th>Date Saved</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($localScores)): ?>
                        <?php foreach ($localScores as $index => $score): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($score['competition_id']); ?></td>
                                <td><?php echo htmlspecialchars($score['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($score['event_distance']); ?>m</td>
                                <td><?php echo htmlspecialchars($score['m_1_score']); ?></td>
                                <td><?php echo htmlspecialchars($score['m_2_score']); ?></td>
                                <td><?php echo htmlspecialchars($score['total_10']); ?></td>
                                <td><?php echo htmlspecialchars($score['total_9']); ?></td>
                                <td><?php echo htmlspecialchars($score['total_score']); ?></td>
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
                            <td colspan="11" class="text-center">No local tournament scores found.</td>
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
const currentMareosId = '<?php echo $currentMareosId; ?>'; 
const fetchModal = document.getElementById('fetchScoresModal');

// Refresh page when modal close
fetchModal.addEventListener('hidden.bs.modal', function () {
    location.reload();
});

// Refresh page button
document.getElementById('refreshPage').addEventListener('click', function() {
        location.reload(); // Refresh the page
    });

// Fetch the list of tournaments and populate the dropdown
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
    .catch(error => console.error('Error fetching tournaments:', error));

// Fetch scores for the athlete based on the selected tournament
document.getElementById('tournament-select').addEventListener('change', function () {
    const tournamentId = this.value;
    if (tournamentId) {
        fetch(`https://ianseo.sukanfc.com/fetch_ianseo.php?tournament_code=${tournamentId}`)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('#ianseo-scores tbody');
                tableBody.innerHTML = ''; 
                let position = 1;

                // Fetch saved scores for the athlete
                fetchSavedScores(tournamentId).then(savedScores => {
                    data.forEach(row => {
                        if (row.athlete_id === currentMareosId) {
                            const isSaved = savedScores.some(saved => saved.mareos_id === row.athlete_id);
                            const tableRow = `
                                <tr>
                                    <td class="align-middle">${position}</td>
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

                            position++;
                        }
                    });

                    // Add event listeners to the save buttons
                    document.querySelectorAll('.save-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const scoreData = JSON.parse(this.getAttribute('data-score'));
                            const message = `Do you want to save the score for event: ${scoreData.event_name}, total score: ${scoreData.total_score}?`;

                            openConfirmModal(message, function () {
                                saveScore(scoreData); 
                            });
                        });
                    });
                });
            })
            .catch(error => console.error('Error fetching scores:', error));
    }
});

// Fetch saved scores for the logged-in athlete
function fetchSavedScores(tournamentId) {
    return fetch(`<?php echo BASE_URL; ?>app/handlers/LocalSavedScoreHandler.php?competition_id=${tournamentId}`)
        .then(response => response.json())
        .catch(error => {
            console.error('Error fetching saved scores:', error);
            return [];
        });
}

// Save score to the server
function saveScore(scoreData) {
    scoreData.competition_id = document.getElementById('tournament-select').value;

    fetch('<?php echo BASE_URL; ?>app/handlers/LocalScoringHandler.php', {
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
        } else if (data.status === 'error' && data.message === 'You have already saved a score for this competition.') {
            updateSavedScoreUI(scoreData.athlete_id);
            showMessage('Information', 'The score has already been saved.');
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();  
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        showMessage('Error', 'An error occurred while saving the score.');
        console.error('Error saving score:', error);
    });
}

// Function to delete a score
function deleteScore(scoreId) {
    fetch(`<?php echo BASE_URL; ?>app/handlers/LocalScoringHandler.php?score_id=${scoreId}`, {
        method: 'DELETE',
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.querySelector(`button[data-score-id="${scoreId}"]`).closest('tr').remove();
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();  
            showMessage('Success', 'Score deleted successfully'); 
        } else {
            throw new Error(data.message || 'Failed to delete the score');
        }
    })
    .catch(error => showMessage('Error', error.message));
}

// Update UI for saved scores (for athletes)
function updateSavedScoreUI(athleteId) {
    const rows = document.querySelectorAll('#ianseo-scores tbody tr');
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
        openConfirmModal('Are you sure you want to delete this score?', function () {
            deleteScore(scoreId);
        });
    });
});
</script>