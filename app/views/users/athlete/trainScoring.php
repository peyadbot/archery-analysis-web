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

<!-- Dynamic input for number of ends -->
<div style="margin-bottom: 20px;">
    <label for="endCount">Number of ends: </label>
    <input type="number" id="endCount" value="3" min="1" max="10">
    <button onclick="generateScoreTable()">Generate Table</button>
</div>

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
        <!-- Rows will be generated dynamically here -->
    </tbody>
</table>

<!-- Reset and Undo Buttons -->
<div class="reset-buttons">
    <button onclick="resetTable()">Reset Scores</button>
</div>

<script>
// Function to generate dynamic score table based on user input
function generateScoreTable() {
    const endCount = document.getElementById('endCount').value;
    const tableBody = document.getElementById('scoreTableBody');
    
    tableBody.innerHTML = ''; // Clear existing rows
    
    for (let i = 1; i <= endCount; i++) {
        const row = `
            <tr>
                <td>${i}</td>
                <td><input type="number" id="score-${i}-1" oninput="updateTotal(${i})" min="0" max="10"></td>
                <td><input type="number" id="score-${i}-2" oninput="updateTotal(${i})" min="0" max="10"></td>
                <td><input type="number" id="score-${i}-3" oninput="updateTotal(${i})" min="0" max="10"></td>
                <td id="end-${i}">0</td>
                <td id="total-${i}">0</td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', row);
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

// Function to reset the table and input fields
function resetTable() {
    const tableBody = document.getElementById('scoreTableBody');
    tableBody.innerHTML = ''; // Clear all rows
    generateScoreTable(); // Regenerate a fresh table
}

// Initial generation of the table when the page loads
generateScoreTable();

</script>

<?php include '../../layouts/dashboard/footer.php'; ?>
