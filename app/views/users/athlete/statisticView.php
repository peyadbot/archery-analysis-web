<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/StatisticHandler.php';

if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$athlete = getAthleteData($pdo, $user_id);

if (!$athlete) {
    $_SESSION['error'] = 'Athlete profile not found. Please complete your profile.';
    header('Location: ' . BASE_URL . 'app/views/profiles/profile.php');
    exit();
}

$mareos_id = $athlete['mareos_id'];

// Get type and view from URL parameters
$type = isset($_GET['type']) ? $_GET['type'] : 'competition';
$view = isset($_GET['view']) ? $_GET['view'] : ($type === 'competition' ? 'local' : 'team');

$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// Fetch stats and chart data using StatisticHandler functions
$scores = getScores($pdo, $mareos_id, $type, $view, $start_date, $end_date);
$avg_stats = getAverageStats($pdo, $mareos_id, $type, $view, $start_date, $end_date);
$best_lowest_stats = getBestAndLowestStats($pdo, $mareos_id, $type, $view, $start_date, $end_date);
$monthly_match_data = getMonthlyMatchData($pdo, $mareos_id, $type, $view);
$monthly_matchs = prepareMonthlyMatchs($monthly_match_data);
?>

<!-- match Scores Table -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <!-- Date Range Filter -->
            <div class="col-12 col-md-9 mb-3 mb-md-0">
                <form id="dateRangeForm" class="row row-cols-1 row-cols-sm-2 row-cols-md-auto g-3 align-items-center">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" name="view" value="<?php echo $view; ?>">
                    <div class="col">
                        <label for="start_date" class="visually-hidden">From:</label>
                        <div class="input-group">
                            <span class="input-group-text">From:</span>
                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col">
                        <label for="end_date" class="visually-hidden">To:</label>
                        <div class="input-group">
                            <span class="input-group-text">To:</span>
                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                    <div class="col">
                        <button type="button" id="resetDateFilter" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>

            <!-- Add Score and Download Report Buttons -->
            <div class="col-12 col-md-3 d-flex justify-content-md-end">
                <?php if ($type === 'competition'): ?>
                    <?php if ($view === 'local'): ?>
                        <a href="compScoring.php?view=local" class="btn btn-success me-2">
                            <i class="bi bi-plus-lg me-2"></i> Add Local Score
                        </a>
                    <?php elseif ($view === 'international'): ?>
                        <a href="compScoring.php?view=international" class="btn btn-success me-2">
                            <i class="bi bi-plus-lg me-2"></i> Add International Score
                        </a>
                    <?php endif; ?>
                <?php elseif ($type === 'training'): ?>
                    <?php if ($view === 'team'): ?>
                        <a href="trainScoring.php?view=team" class="btn btn-success me-2">
                            <i class="bi bi-plus-lg me-2"></i> Add Team Score
                        </a>
                    <?php elseif ($view === 'personal'): ?>
                        <a href="trainScoring.php?view=personal" class="btn btn-success me-2">
                            <i class="bi bi-plus-lg me-2"></i> Add Personal Score
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <button id="download-report-pdf" class="btn btn-primary">Download Report</button>
            </div>
        </div>

        <!-- Table match scores -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped shadow-sm" id="matchTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Event Distance</th>
                        <th>M1 Score</th>
                        <th>M1 10+X</th>
                        <th>M1 10/9</th>
                        <th>M2 Score</th>
                        <th>M2 10+X</th>
                        <th>M2 10/9</th>
                        <th>Total Score</th>
                        <th>10+X</th>
                        <th>10/9</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody id="matchTableBody">
                    <?php if (!empty($scores)): ?>
                        <?php foreach ($scores as $score): ?>
                            <?php
                            // Calculate per arrow statistics
                            $m1_per_arrow = round($score['m1_score'] / 36, 2);
                            $m2_per_arrow = round($score['m2_score'] / 36, 2);
                            $total_per_arrow = round($score['total_score'] / 72, 2);
                            $m1_10X_per_arrow = round($score['m1_10X'] / 36, 2);
                            $m1_109_per_arrow = round($score['m1_109'] / 36, 2);
                            $m2_10X_per_arrow = round($score['m2_10X'] / 36, 2);
                            $m2_109_per_arrow = round($score['m2_109'] / 36, 2);
                            $total_10X_per_arrow = round($score['total_10X'] / 72, 2);
                            $total_109_per_arrow = round($score['total_109'] / 72, 2);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($score[$type === 'competition' ? 'competition_id' : 'training_id']); ?></td>
                                <td><?php echo htmlspecialchars($score['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($score['event_distance']); ?></td>
                                <td class="table-primary"><?php echo htmlspecialchars($score['m1_score']) . " (" . $m1_per_arrow . ")"; ?></td>
                                <td class="table-primary"><?php echo htmlspecialchars($score['m1_10X']) . " (" . $m1_10X_per_arrow . ")"; ?></td>
                                <td class="table-primary"><?php echo htmlspecialchars($score['m1_109']) . " (" . $m1_109_per_arrow . ")"; ?></td>
                                <td class="table-info"><?php echo htmlspecialchars($score['m2_score']) . " (" . $m2_per_arrow . ")"; ?></td>
                                <td class="table-info"><?php echo htmlspecialchars($score['m2_10X']) . " (" . $m2_10X_per_arrow . ")"; ?></td>
                                <td class="table-info"><?php echo htmlspecialchars($score['m2_109']) . " (" . $m2_109_per_arrow . ")"; ?></td>
                                <td><?php echo htmlspecialchars($score['total_score']) . " (" . $total_per_arrow . ")"; ?></td>
                                <td><?php echo htmlspecialchars($score['total_10X']) . " (" . $total_10X_per_arrow . ")"; ?></td>
                                <td><?php echo htmlspecialchars($score['total_109']) . " (" . $total_109_per_arrow . ")"; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($score['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="13" class="text-center">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center" id="pagination">
            </ul>
        </nav>
    </div>
</div>

<!-- Display average per arrow statistics -->
<div class="card mb-4 shadow-sm statistics">
    <div class="card-body">
        <h5 class="card-title text-primary mb-4">Averages Score Per Arrow</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">M1 Statistics</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Score per Arrow
                                <span class="badge bg-primary rounded-pill"><?php echo round($avg_stats['avg_m1_per_arrow'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                10+X per Arrow
                                <span class="badge bg-primary rounded-pill"><?php echo round($avg_stats['avg_m1_10X_per_arrow'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                10/9 per Arrow
                                <span class="badge bg-primary rounded-pill"><?php echo round($avg_stats['avg_m1_109_per_arrow'], 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">M2 Statistics</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Score per Arrow
                                <span class="badge bg-primary rounded-pill"><?php echo round($avg_stats['avg_m2_per_arrow'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                10+X per Arrow
                                <span class="badge bg-primary rounded-pill"><?php echo round($avg_stats['avg_m2_10X_per_arrow'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                10/9 per Arrow
                                <span class="badge bg-primary rounded-pill"><?php echo round($avg_stats['avg_m2_109_per_arrow'], 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Overall Statistics</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Score per Arrow
                                <span class="badge bg-success rounded-pill"><?php echo round($avg_stats['avg_total_score'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                10+X per Arrow
                                <span class="badge bg-success rounded-pill"><?php echo round($avg_stats['avg_total_10X'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                10/9 per Arrow
                                <span class="badge bg-success rounded-pill"><?php echo round($avg_stats['avg_total_109'], 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Average Per Arrow Chart -->
<div class="card shadow-sm mb-4 avg-chart">
    <div class="card-body">
        <h4 class="text-primary">Average Per Arrow</h4>
        <div style="position: relative; height: 60vh; width: 100%;">
            <canvas id="avgPerArrowChart"></canvas>
        </div>
    </div>
</div>

<!-- Scoring Chart -->
<div class="card shadow-sm mb-4 scoring-chart">
    <div class="card-body">
        <h4 class="text-primary">Scoring Trends</h4>
        <div style="position: relative; height: 60vh; width: 100%;">
            <canvas id="scoringTrendChart"></canvas>
        </div>
    </div>
</div>

<!-- Best and Lowest Performances -->
<div class="card mb-4 shadow-sm statistics">
    <div class="card-body">
        <h5 class="card-title text-primary mb-4">Best and Lowest Performances</h5>
        <div class="row">
            <!-- Best and Lowest Total Scores -->
            <div class="col mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Scores</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Best Total Score
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['best_total_score']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Most 10+X
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['best_total_10X']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Most 10/9
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['best_total_109']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Lowest Total Score
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['lowest_total_score']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fewest 10+X
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['lowest_total_10X']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fewest 10/9
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['lowest_total_109']; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Best and Lowest M1 Performances -->
            <div class="col mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">M1 Performances</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Best M1 Score
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['best_m1_score']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Most M1 10+X
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['max_m1_10X']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Most M1 10/9
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['max_m1_109']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Lowest M1 Score
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['lowest_m1_score']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fewest M1 10+X
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['min_m1_10X']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fewest M1 10/9
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['min_m1_109']; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Best and Lowest M2 Performances -->
            <div class="col mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">M2 Performances</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Best M2 Score
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['best_m2_score']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Most M2 10+X
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['max_m2_10X']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Most M2 10/9
                                <span class="badge bg-primary rounded-pill"><?php echo $best_lowest_stats['max_m2_109']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Lowest M2 Score
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['lowest_m2_score']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fewest M2 10+X
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['min_m2_10X']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fewest M2 10/9
                                <span class="badge bg-danger rounded-pill"><?php echo $best_lowest_stats['min_m2_109']; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly match Chart -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="text-primary">match Counter</h4>
        <div style="position: relative; height: 60vh; width: 100%;">
            <canvas id="monthlymatchsBar"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js and PDF Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
<script>
    // Pagination Script for match table
    document.addEventListener('DOMContentLoaded', function() {
        const rowsPerPage = 4; // The number of rows to show per page
        const tableBody = document.getElementById('matchTableBody');
        const rows = tableBody.getElementsByTagName('tr');
        const pagination = document.getElementById('pagination');
        let currentPage = 1;

        function displayRows() {
            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = 'none';
            }

            // Calculate the range of rows to display based on the current page
            let start = (currentPage - 1) * rowsPerPage;
            let end = start + rowsPerPage;

            for (let i = start; i < end && i < rows.length; i++) {
                rows[i].style.display = '';
            }
        }

        function setupPagination() {
            pagination.innerHTML = '';

            // Calculate the total number of pages
            let totalPages = Math.ceil(rows.length / rowsPerPage);

            // Previous button
            const prevButton = document.createElement('li');
            prevButton.classList.add('page-item');
            prevButton.innerHTML = `<a class="page-link" href="#">Previous</a>`;
            prevButton.onclick = function() {
                if (currentPage > 1) {
                    currentPage--;
                    displayRows();
                    setupPagination();
                }
            };
            pagination.appendChild(prevButton);

            // Page number buttons
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('li');
                pageButton.classList.add('page-item');
                if (i === currentPage) {
                    pageButton.classList.add('active');
                }
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.onclick = function() {
                    currentPage = i;
                    displayRows();
                    setupPagination();
                };
                pagination.appendChild(pageButton);
            }

            // Next button
            const nextButton = document.createElement('li');
            nextButton.classList.add('page-item');
            nextButton.innerHTML = `<a class="page-link" href="#">Next</a>`;
            nextButton.onclick = function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayRows();
                    setupPagination();
                }
            };
            pagination.appendChild(nextButton);
        }
        displayRows();
        setupPagination();
    });

    // Date data filtering
    document.getElementById('dateRangeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('start_date', startDate);
        currentUrl.searchParams.set('end_date', endDate);
        window.location.href = currentUrl.toString();
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('start_date')) {
        document.getElementById('start_date').value = urlParams.get('start_date');
    }

    if (urlParams.has('end_date')) {
        document.getElementById('end_date').value = urlParams.get('end_date');
    }

    // Reset date filter
    document.addEventListener('DOMContentLoaded', function() {
        const resetButton = document.getElementById('resetDateFilter');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const dateRangeForm = document.getElementById('dateRangeForm');

        resetButton.addEventListener('click', function() {
            startDateInput.value = '';
            endDateInput.value = '';
            dateRangeForm.submit();
        });
    });

    window.onload = function() {
        // PDF Download Script
        document.getElementById('download-report-pdf').addEventListener('click', function() {
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF('portrait', 'mm', 'a4');
            const pageWidth = pdf.internal.pageSize.getWidth();
            const padding = 10;
            const pageHeight = pdf.internal.pageSize.getHeight();

            // Header with athlete info and report date
            const mareosId = '<?php echo $mareos_id; ?>';
            const currentDate = new Date().toLocaleDateString();
            pdf.setFontSize(7);
            pdf.text(`${mareosId}-${currentDate}`, padding, 15);

            // Table column headers
            const headers = [
                ["ID", "Event Name", "Event Distance", "M1 Score", "M1 10+X", "M1 10/9", "M2 Score", "M2 10+X", "M2 10/9", "Total Score", "10+X", "10/9", "Created At"]
            ];

            const data = <?php echo json_encode($scores); ?>;
            const rows = data.map(score => [
                score.<?php echo isset($type) && $type === 'competition' ? 'competition_id' : 'training_id'; ?>,
                score.event_name,
                score.event_distance,
                `${score.m1_score} (${(score.m1_score / 36).toFixed(2)})`,
                `${score.m1_10X} (${(score.m1_10X / 36).toFixed(2)})`,
                `${score.m1_109} (${(score.m1_109 / 36).toFixed(2)})`,
                `${score.m2_score} (${(score.m2_score / 36).toFixed(2)})`,
                `${score.m2_10X} (${(score.m2_10X / 36).toFixed(2)})`,
                `${score.m2_109} (${(score.m2_109 / 36).toFixed(2)})`,
                `${score.total_score} (${(score.total_score / 72).toFixed(2)})`,
                `${score.total_10X} (${(score.total_10X / 72).toFixed(2)})`,
                `${score.total_109} (${(score.total_109 / 72).toFixed(2)})`,
                new Date(score.created_at).toLocaleDateString()
            ]);

            pdf.autoTable({
                head: headers,
                body: rows,
                startY: 30,
                theme: 'striped',
                headStyles: {
                    fillColor: [75, 192, 192]
                },
                styles: {
                    fontSize: 7,
                    cellPadding: 2
                },
            });

            // Capture Best and Lowest
            html2canvas(document.querySelectorAll('.statistics')[1], {
                scale: 2,
                useCORS: true
            }).then(bestLowestCanvas => {
                const bestLowestImgData = bestLowestCanvas.toDataURL('image/png');
                const bestLowestImgWidth = pageWidth - (2 * padding);
                const bestLowestImgHeight = bestLowestCanvas.height * bestLowestImgWidth / bestLowestCanvas.width;

                // Capture Average Statistics
                html2canvas(document.querySelector('.statistics'), {
                    scale: 2,
                    useCORS: true
                }).then(avgStatsCanvas => {
                    const avgStatsImgData = avgStatsCanvas.toDataURL('image/png');
                    const avgStatsImgWidth = pageWidth - (2 * padding);
                    const avgStatsImgHeight = avgStatsCanvas.height * avgStatsImgWidth / avgStatsCanvas.width;

                    // Capture Performance Trends Chart
                    html2canvas(document.getElementById('scoringTrendChart'), {
                        scale: 2,
                        useCORS: true
                    }).then(perfCanvas => {
                        const perfImgData = perfCanvas.toDataURL('image/png');
                        const perfImgWidth = pageWidth - (2 * padding);
                        const perfImgHeight = perfCanvas.height * perfImgWidth / perfCanvas.width;

                        // Capture Average Score Per Arrow Chart
                        html2canvas(document.getElementById('avgPerArrowChart'), {
                            scale: 2,
                            useCORS: true
                        }).then(avgPerArrowCanvas => {
                            const avgPerArrowImgData = avgPerArrowCanvas.toDataURL('image/png');
                            const avgPerArrowImgWidth = pageWidth - (2 * padding);
                            const avgPerArrowImgHeight = avgPerArrowCanvas.height * avgPerArrowImgWidth / avgPerArrowCanvas.width;

                            pdf.addPage();
                            pdf.setFontSize(14);
                            pdf.text("Performances Statistics", pageWidth / 2, 20, {
                                align: "center"
                            });
                            pdf.addImage(bestLowestImgData, 'PNG', padding, 30, bestLowestImgWidth, bestLowestImgHeight);
                            pdf.addImage(avgStatsImgData, 'PNG', padding, 50 + bestLowestImgHeight, avgStatsImgWidth, avgStatsImgHeight);

                            pdf.addPage();
                            pdf.setFontSize(14);
                            pdf.text("Performance Charts", pageWidth / 2, 20, {
                                align: "center"
                            });

                            pdf.text("Scoring Trends", padding, 35);
                            pdf.addImage(perfImgData, 'PNG', padding, 40, perfImgWidth, perfImgHeight);

                            const yPositionAvgPerArrow = 40 + perfImgHeight + 20;
                            pdf.text("Average Score Per Arrow", padding, yPositionAvgPerArrow);
                            pdf.addImage(avgPerArrowImgData, 'PNG', padding, yPositionAvgPerArrow + 5, avgPerArrowImgWidth, avgPerArrowImgHeight);

                            const pdfBlob = pdf.output('blob');
                            const pdfUrl = URL.createObjectURL(pdfBlob);
                            window.open(pdfUrl);
                        });
                    });
                });
            });
        });

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
        const monthlymatchsData = [
            <?php echo implode(',', $monthly_matchs); ?>
        ];

        // Monthly matchs counter chart
        const monthlymatchsBarCtx = document.getElementById('monthlymatchsBar').getContext('2d');
        const monthlymatchsBar = new Chart(monthlymatchsBarCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'matchs per Month',
                    data: monthlymatchsData,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const value = tooltipItem.raw;
                                const total = tooltipItem.dataset.data.reduce((acc, cur) => acc + cur, 0);
                                return `${tooltipItem.label}: ${value} matches`;
                            }
                        }
                    }
                }
            }
        });

        const scoreM1Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m1_score']; ?>,
            <?php endforeach; ?>
        ];
        const scoreM2Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m2_score']; ?>,
            <?php endforeach; ?>
        ];
        const m1_10XData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m1_10X']; ?>,
            <?php endforeach; ?>
        ];
        const m1_109Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m1_109']; ?>,
            <?php endforeach; ?>
        ];
        const m2_10XData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m2_10X']; ?>,
            <?php endforeach; ?>
        ];
        const m2_109Data = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['m2_109']; ?>,
            <?php endforeach; ?>
        ];
        const total10sData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['total_10X']; ?>,
            <?php endforeach; ?>
        ];
        const total9sData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['total_109']; ?>,
            <?php endforeach; ?>
        ];
        const totalScoreData = [
            <?php foreach ($scores as $score): ?>
                <?php echo $score['total_score']; ?>,
            <?php endforeach; ?>
        ];
        const averageScores = scoreM1Data.map((_, i) => {
            return (scoreM1Data[i] + scoreM2Data[i] + total10sData[i] + total9sData[i]) / 4;
        });

        // Performance Trends Chart
        const ctx = document.getElementById('scoringTrendChart').getContext('2d');
        const scoringTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($scores as $score): ?> '<?php echo htmlspecialchars(isset($type) && $type === 'competition' ? $score['competition_id'] : $score['training_id']); ?>', <?php endforeach; ?>
                ],
                datasets: [{
                        label: 'M1 Score',
                        data: scoreM1Data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'M2 Score',
                        data: scoreM2Data,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'M1 10+X',
                        data: m1_10XData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'M1 10/9',
                        data: m1_109Data,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'M2 10+X',
                        data: m2_10XData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'M2 10/9',
                        data: m2_109Data,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Total 10s',
                        data: total10sData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Total 9s',
                        data: total9sData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Total Score',
                        data: totalScoreData,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            font: {
                                size: 14
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 10,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        // Average Score Per Arrow Chart
        const avgPerArrowLineCtx = document.getElementById('avgPerArrowChart').getContext('2d');

        if (avgPerArrowLineCtx) {
            const totalScoreAvgData = [
                <?php foreach ($scores as $score): ?>
                    <?php echo round($score['total_score'] / 72, 2); ?>,
                <?php endforeach; ?>
            ];

            const m1AvgData = [
                <?php foreach ($scores as $score): ?>
                    <?php echo round($score['m1_score'] / 36, 2); ?>,
                <?php endforeach; ?>
            ];

            const m2AvgData = [
                <?php foreach ($scores as $score): ?>
                    <?php echo round($score['m2_score'] / 36, 2); ?>,
                <?php endforeach; ?>
            ];

            const total10XAvgData = [
                <?php foreach ($scores as $score): ?>
                    <?php echo round($score['total_10X'] / 72, 2); ?>,
                <?php endforeach; ?>
            ];

            const total109AvgData = [
                <?php foreach ($scores as $score): ?>
                    <?php echo round($score['total_109'] / 72, 2); ?>,
                <?php endforeach; ?>
            ];

            const avgPerArrowLineLabels = [
                <?php foreach ($scores as $score): ?> '<?php echo htmlspecialchars(isset($type) && $type === 'competition' ? $score['competition_id'] : $score['training_id']); ?>',
                <?php endforeach; ?>
            ];

            const avgPerArrowLineChart = new Chart(avgPerArrowLineCtx, {
                type: 'line',
                data: {
                    labels: avgPerArrowLineLabels,
                    datasets: [{
                            label: 'Average Total Score Per Arrow',
                            data: totalScoreAvgData,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Average M1 Score Per Arrow',
                            data: m1AvgData,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Average M2 Score Per Arrow',
                            data: m2AvgData,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Average Total 10+X Per Arrow',
                            data: total10XAvgData,
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Average Total 10/9 Per Arrow',
                            data: total109AvgData,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                font: {
                                    size: 14
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 10,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error("Canvas element not found for the Average Score Per Arrow chart.");
        }
    };
</script>