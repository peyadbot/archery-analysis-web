<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';

if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch athlete details
$stmt = $pdo->prepare('SELECT athlete_id FROM athlete_details WHERE user_id = ?');
$stmt->execute([$user_id]);
$athlete = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$athlete) {
    $_SESSION['error'] = 'Athlete profile not found. Please complete your profile.';
    header('Location: ' . BASE_URL . 'app/views/profiles/profile.php');
    exit();
}

$athlete_id = $athlete['athlete_id'];
?>

<?php include '../../layouts/dashboard/header.php'; ?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        text-align: center;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }
    h2 {
        font-size: 24px;
        color: #333;
        margin-top: 20px;
    }
    .score-table {
        width: 80%;
        max-width: 500px;
        margin: 20px auto;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .score-table th {
        background-color: #54afd8;
        color: white;
        padding: 12px;
        text-transform: uppercase;
    }
    .score-table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
    }
    .score-table input {
        width: 50px;
        height: 30px;
        text-align: center;
        border: none;
        background-color: #f9f9f9;
        font-size: 16px;
    }
    #targetCanvas {
        border: 1px solid #000;
        display: block;
        margin: 20px auto;
        background-color: white;
        border-radius: 50%;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    }
    .label {
        text-align: center;
        font-size: 18px;
        margin-top: 10px;
        color: #333;
    }
    .reset-buttons button {
        background-color: #54afd8;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 15px;
    }
    .reset-buttons button:hover {
        background-color: #45a1c9;
    }
</style>

<div class="main-content" id="mainContent">

<h2>Archery Score Entry</h2>

<!-- Score Table -->
<table class="score-table">
    <thead>
        <tr>
            <th>#</th>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>End</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody id="scoreTableBody">
        <tr>
            <td>1</td>
            <td><input type="text" id="score-1-1" readonly></td>
            <td><input type="text" id="score-1-2" readonly></td>
            <td><input type="text" id="score-1-3" readonly></td>
            <td id="end-1">0</td>
            <td id="total-1">0</td>
        </tr>
        <tr>
            <td>2</td>
            <td><input type="text" id="score-2-1" readonly></td>
            <td><input type="text" id="score-2-2" readonly></td>
            <td><input type="text" id="score-2-3" readonly></td>
            <td id="end-2">0</td>
            <td id="total-2">0</td>
        </tr>
        <tr>
            <td>3</td>
            <td><input type="text" id="score-2-1" readonly></td>
            <td><input type="text" id="score-2-2" readonly></td>
            <td><input type="text" id="score-2-3" readonly></td>
            <td id="end-2">0</td>
            <td id="total-2">0</td>
        </tr>
    </tbody>
</table>
<!-- Reset and Undo Buttons -->
<div class="reset-buttons">
    <button onclick="resetCanvas()">Reset Target & Scores</button>
</div>


<!-- Target Canvas -->
<canvas id="targetCanvas" width="500" height="500"></canvas>

<script>
// Variables to track the round and shot number
let shots = []; // Store the shots, each shot will have its x, y coordinates, and the round and shot number.
let availableSpots = []; // Array to track available spots for reuse after deletion
let dragging = false;
let draggedShotIndex = null;
const canvas = document.getElementById('targetCanvas');
const ctx = canvas.getContext('2d');

// Function to calculate score based on the shot location on the target
function calculateScore(x, y) {
    const centerX = 250;
    const centerY = 250;
    const distance = Math.sqrt(Math.pow(x - centerX, 2) + Math.pow(y - centerY, 2));

    if (distance <= 24) return "10";
    else if (distance <= 48) return "9";
    else if (distance <= 72) return "8";
    else if (distance <= 96) return "7";
    else if (distance <= 120) return "6";
    else if (distance <= 144) return "5";
    else if (distance <= 168) return "4";
    else if (distance <= 192) return "3";
    else if (distance <= 216) return "2";
    else if (distance <= 240) return "1";
    else return "M"; // Miss
}

// Function to input score into the specified table cell (round and shot)
function inputScoreInTable(round, shot, score) {
    const inputField = document.getElementById(`score-${round}-${shot}`);
    if (inputField) {
        inputField.value = score;
        updateTotal(round); // Update the total for the current round
    }
}

// Function to update the total score for the current round
function updateTotal(round) {
    let total = 0;
    for (let i = 1; i <= 3; i++) {
        let score = document.getElementById(`score-${round}-${i}`).value;
        if (!isNaN(score) && score !== '') {
            total += parseInt(score);
        }
    }
    document.getElementById(`total-${round}`).textContent = total;
}

// Function to clear the score table (optional if needed)
function clearScoreTable() {
    for (let round = 1; round <= 2; round++) {
        for (let shot = 1; shot <= 3; shot++) {
            document.getElementById(`score-${round}-${shot}`).value = '';
        }
        document.getElementById(`total-${round}`).textContent = '0';
    }
}

// Function to render the scores based on the current shots array
function renderScores() {
    shots.forEach((shot) => {
        const score = calculateScore(shot.x, shot.y); // Recalculate the score for each shot
        inputScoreInTable(shot.round, shot.shot, score); // Update the table for the specific round and shot
    });
}

// Function to draw the archery target
function drawTarget() {
    const colors = ['#fff', '#8a8a88', '#54afd8', '#ef3e3c', '#f2cd0e'];  // White, Black, Blue, Red, Yellow
    const rings = [
        { radius: 240, color: colors[0] },   // Outer White ring
        { radius: 216, color: colors[0] },   // White ring
        { radius: 192, color: colors[1] },   // Black ring
        { radius: 168, color: colors[1] },   // Black ring
        { radius: 144, color: colors[2] },   // Blue ring
        { radius: 120, color: colors[2] },   // Blue ring
        { radius: 96,  color: colors[3] },   // Red ring
        { radius: 72,  color: colors[3] },   // Red ring
        { radius: 48,  color: colors[4] },   // Yellow ring
        { radius: 24,  color: colors[4] }    // Bullseye
    ];

    // Clear the canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw the rings
    rings.forEach(function(ring) {
        ctx.beginPath();
        ctx.arc(250, 250, ring.radius, 0, 2 * Math.PI);
        ctx.fillStyle = ring.color;
        ctx.fill();
        ctx.lineWidth = 1;
        ctx.strokeStyle = 'black';
        ctx.stroke();
    });

    // Draw the "X" at the center
    ctx.font = "15px Arial";
    ctx.fillStyle = "black";
    ctx.fillText('X', 245, 255);

    // Draw all the plotted shots
    shots.forEach(function(shot) {
        ctx.beginPath();
        ctx.arc(shot.x, shot.y, 5, 0, 2 * Math.PI);
        ctx.fillStyle = "red";
        ctx.fill();
    });
}

// Function to get shot under the cursor (if any)
function getShotUnderCursor(x, y) {
    return shots.findIndex(shot => {
        const distance = Math.sqrt(Math.pow(shot.x - x, 2) + Math.pow(shot.y - y, 2));
        return distance <= 5; // Adjust the radius for detecting a shot
    });
}

// Handle mousedown to start dragging or plot a new shot
canvas.addEventListener('mousedown', function(event) {
    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    // Check if a shot is under the cursor
    const shotIndex = getShotUnderCursor(x, y);

    if (shotIndex !== -1) {
        // Start dragging the shot
        dragging = true;
        draggedShotIndex = shotIndex;
    } else if (shots.length < 6) { // Limit shots to 6 (2 rounds of 3 shots)
        let round, shot;
        
        // Reuse an available spot if one exists, otherwise use next spot
        if (availableSpots.length > 0) {
            const spot = availableSpots.shift(); // Use the first available spot
            round = spot.round;
            shot = spot.shot;
        } else {
            round = Math.floor(shots.length / 3) + 1;
            shot = (shots.length % 3) + 1;
        }

        // Plot a new shot and calculate the score
        const score = calculateScore(x, y);
        inputScoreInTable(round, shot, score);
        shots.push({ x, y, round, shot }); // Store round and shot in the shots array
        drawTarget();
    } else {
        alert('You can only plot 6 shots.');
    }
});

// Handle mousemove to drag the shot
canvas.addEventListener('mousemove', function(event) {
    if (dragging && draggedShotIndex !== null) {
        const rect = canvas.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        // Update the shot position
        shots[draggedShotIndex].x = x;
        shots[draggedShotIndex].y = y;
        drawTarget(); // Redraw the target with the updated shot position

        // Update the score based on the new position
        const shot = shots[draggedShotIndex];
        const score = calculateScore(shot.x, shot.y);
        inputScoreInTable(shot.round, shot.shot, score); // Update the score for that specific shot
    }
});

// Stop dragging on mouseup
canvas.addEventListener('mouseup', function() {
    dragging = false;
    draggedShotIndex = null;
});

// Handle right-click to delete shot
canvas.addEventListener('contextmenu', function(event) {
    event.preventDefault();
    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    // Check if a shot is under the cursor
    const shotIndex = getShotUnderCursor(x, y);

    if (shotIndex !== -1) {
        const shot = shots[shotIndex];

        // Remove the shot from the array
        shots.splice(shotIndex, 1);

        // Clear the corresponding score from the table
        inputScoreInTable(shot.round, shot.shot, ''); // Clear the score from the table
        updateTotal(shot.round); // Update the total for the round

        // Mark the spot as available for reuse
        availableSpots.push({ round: shot.round, shot: shot.shot });

        drawTarget(); // Redraw the target without the deleted shot
    }
});

// Function to reset the canvas and score table
function resetCanvas() {
    // Clear the canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawTarget(); // Redraw the target

    // Reset shots array and available spots
    shots = [];
    availableSpots = [];

    // Clear all scores from the table
    clearScoreTable();
}

// Initial draw of the target
drawTarget();

</script>

<?php include '../../layouts/dashboard/footer.php'; ?>
