<?php
ob_start();
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/CompStatisticHandler.php';
require_once __DIR__ . '/../../../handlers/CompCompareHandler.php';

if ($_SESSION['role'] !== 'athlete') {
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$result = handleCompCompare($pdo, $user_id);

if (isset($result['error'])) {
    $_SESSION['error'] = $result['error'];
    header('Location: ' . $result['redirect']);
    exit();
}

extract($result);

$colors = [
    'rgba(75, 192, 192, 1)',
    'rgba(255, 99, 132, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(54, 162, 235, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 159, 64, 1)',
];
?>

<!-- Chart.js, jspdf, and html2canvas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<!-- Competition and Metrics Selection Accordion -->
<div class="accordion shadow-sm mb-4" id="competitionAccordion">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Competition and Metrics Selection
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#competitionAccordion">
            <div class="accordion-body">
                <form id="competitionForm" action="" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Select Metrics to Display</label>
                            <?php foreach ($all_metrics as $metric_key => $metric_name): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="metrics[]"
                                        value="<?php echo $metric_key; ?>" id="metric_<?php echo $metric_key; ?>"
                                        <?php echo in_array($metric_key, $selected_metrics) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="metric_<?php echo $metric_key; ?>">
                                        <?php echo $metric_name; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-6">
                            <div id="competitionSelectionGroup">
                                <?php
                                $num_competitions = min(6, max(2, count($selected_competitions)));
                                for ($i = 0; $i < $num_competitions; $i++):
                                ?>
                                    <div class="row mb-3">
                                        <div class="col-md-10">
                                            <label for="competition<?php echo $i; ?>" class="form-label">Select Competition <?php echo $i + 1; ?></label>
                                            <select id="competition<?php echo $i; ?>" name="competitions[]" class="form-control competition-select" required>
                                                <option value="">Select a competition</option>
                                                <?php foreach ($competitions as $competition): ?>
                                                    <option value="<?php echo htmlspecialchars($competition['competition_id']); ?>"
                                                        <?php echo (isset($selected_competitions[$i]) && $competition['competition_id'] == $selected_competitions[$i]) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($competition['competition_id']) . ' - ' . htmlspecialchars($competition['event_name']) . ' (' . date('d/m/Y', strtotime($competition['created_at'])) . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <?php if ($i > 1): ?>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-btn">
                                                    <i class="bi bi-dash-lg"></i> Remove
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <button type="button" class="btn btn-success add-more-btn">
                                        <i class="bi bi-plus-lg"></i> Add Competition
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Compare</button>
                            <a href="?view=compare" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Comparison Results Table -->
<?php if (!empty($comparison_data)): ?>
    <div class="card shadow-sm mb-4" id="resultsContainer">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-primary">Comparison Results</h4>

                <!-- Download PDF Report Button -->
                <button id="download-report-pdf" class="btn btn-primary">Download Report</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Competition Name</th>
                            <th>Event Distance</th>
                            <?php foreach ($selected_metrics as $metric): ?>
                                <th><?php echo $all_metrics[$metric]; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comparison_data as $index => $data):
                            $color = $colors[$index % count($colors)];
                        ?>
                            <tr style="background-color: <?php echo str_replace('1)', '0.2)', $color); ?>">
                                <td style="font-weight: bold; color: <?php echo $color; ?>">
                                    <?php echo htmlspecialchars($data['competition_id']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($data['event_distance']); ?></td>
                                <?php foreach ($selected_metrics as $metric): ?>
                                    <td>
                                        <?php
                                        echo htmlspecialchars($data[$metric]);
                                        if ($metric === 'total_score' || $metric === 'total_10X' || $metric === 'total_109') {
                                            $avgPerArrow = number_format($data[$metric] / 72, 2);
                                            echo " ({$avgPerArrow})";
                                        } elseif ($metric === 'm1_score' || $metric === 'm2_score' || $metric === 'm1_10X' || $metric === 'm2_10X' || $metric === 'm1_109' || $metric === 'm2_109') {
                                            $avgPerArrow = number_format($data[$metric] / 36, 2);
                                            echo " ({$avgPerArrow})";
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Average Per Arrow Chart -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="text-primary">Average Per Arrow Comparison</h4>
            <div style="position: relative; height: 60vh; width: 100%;">
                <canvas id="avgPerArrowChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Scoring Trends Chart -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="text-primary">Scoring Trends</h4>
            <div style="position: relative; height: 60vh; width: 100%;">
                <canvas id="scoreTrendChart"></canvas>
            </div>
        </div>
    </div>


<?php endif; ?>

<script>
    // Function to update disabled options
    function updateDisabledOptions() {
        const selects = document.querySelectorAll('.competition-select');
        const selectedValues = Array.from(selects).map(select => select.value);

        selects.forEach(select => {
            Array.from(select.options).forEach(option => {
                if (option.value && option.value !== select.value) {
                    option.disabled = selectedValues.includes(option.value);
                }
            });
        });
    }

    // Add event listeners to all select elements
    document.querySelectorAll('.competition-select').forEach(select => {
        select.addEventListener('change', updateDisabledOptions);
    });

    updateDisabledOptions();

    // Add more competitions
    document.querySelector('.add-more-btn').addEventListener('click', function() {
        const competitionGroup = document.getElementById('competitionSelectionGroup');

        if (competitionGroup.children.length >= 6) {
            alert('You can compare a maximum of 6 competitions.');
            return;
        }

        const newSelection = document.createElement('div');
        newSelection.classList.add('row', 'mb-3');
        newSelection.innerHTML = `
            <div class="col-md-10">
                <label for="competition${competitionGroup.children.length}" class="form-label">Select Competition ${competitionGroup.children.length + 1}</label>
                <select id="competition${competitionGroup.children.length}" name="competitions[]" class="form-control competition-select" required>
                    <option value="">Select a competition</option>
                    <?php foreach ($competitions as $competition): ?>
                        <option value="<?php echo htmlspecialchars($competition['competition_id']); ?>">
                            <?php echo htmlspecialchars($competition['competition_id']) . ' - ' . htmlspecialchars($competition['event_name']) . ' (' . date('d/m/Y', strtotime($competition['created_at'])) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mt-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-btn">
                    <i class="bi bi-dash-lg"></i> Remove
                </button>
            </div>
        `;
        competitionGroup.appendChild(newSelection);

        const newSelect = newSelection.querySelector('.competition-select');
        newSelect.addEventListener('change', updateDisabledOptions);

        updateDisabledOptions();

        newSelection.querySelector('.remove-btn').addEventListener('click', function() {
            newSelection.remove();
            updateDisabledOptions();
        });
    });

    // Listener to remove dynamically added competitions
    document.getElementById('competitionSelectionGroup').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-btn')) {
            e.target.closest('.row').remove();
            updateDisabledOptions();
        }
    });


    // Charts Setup
    const ctx = document.getElementById('scoreTrendChart');
    const avgCtx = document.getElementById('avgPerArrowChart');

    if (ctx && avgCtx && <?php echo !empty($comparison_data) ? 'true' : 'false'; ?>) {
        const scoreTrendCtx = ctx.getContext('2d');
        const avgPerArrowCtx = avgCtx.getContext('2d');

        const labels = <?php echo json_encode(array_map(function ($metric) use ($all_metrics) {
                            return $all_metrics[$metric];
                        }, $selected_metrics)); ?>;

        const datasets = [
            <?php
            $colors = [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
            ];
            foreach ($comparison_data as $index => $data):
                $color = $colors[$index % count($colors)];
            ?> {
                    label: '<?php echo htmlspecialchars($data['competition_id']); ?>',
                    data: [
                        <?php foreach ($selected_metrics as $metric) {
                            echo htmlspecialchars($data[$metric]) . ',';
                        } ?>
                    ],
                    backgroundColor: '<?php echo str_replace('1)', '0.2)', $color); ?>',
                    borderColor: '<?php echo $color; ?>',
                    borderWidth: 2,
                    tension: 0.3
                },
            <?php endforeach; ?>
        ];

        const scoreTrendChart = new Chart(scoreTrendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Score',
                            font: {
                                size: 14
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Metrics',
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
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

        const avgDatasets = [
            <?php foreach ($comparison_data as $index => $data):
                $color = $colors[$index % count($colors)];
            ?> {
                    label: '<?php echo htmlspecialchars($data['competition_id']); ?>',
                    data: [
                        <?php foreach ($selected_metrics as $metric) {
                            if ($metric === 'total_score' || $metric === 'total_10X' || $metric === 'total_109') {
                                echo '(' . $data[$metric] . ' / 72).toFixed(2),';
                            } elseif ($metric === 'm1_score' || $metric === 'm2_score' || $metric === 'm1_10X' || $metric === 'm2_10X' || $metric === 'm1_109' || $metric === 'm2_109') {
                                echo '(' . $data[$metric] . ' / 36).toFixed(2),';
                            } else {
                                echo $data[$metric] . ',';
                            }
                        } ?>
                    ],
                    backgroundColor: '<?php echo str_replace('1)', '0.2)', $color); ?>',
                    borderColor: '<?php echo $color; ?>',
                    borderWidth: 2,
                    tension: 0.3
                },
            <?php endforeach; ?>
        ];

        const avgPerArrowChart = new Chart(avgPerArrowCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: avgDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Average Per Arrow',
                            font: {
                                size: 14
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Metrics',
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }


    document.addEventListener('DOMContentLoaded', function() {
        const downloadButton = document.getElementById('download-report-pdf');

        if (downloadButton) {
            downloadButton.addEventListener('click', function() {
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF('portrait', 'mm', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const padding = 10;

                // Header with athlete info and report date
                const mareosId = '<?php echo $mareos_id; ?>';
                const currentDate = new Date().toLocaleDateString();
                pdf.setFontSize(7);
                pdf.text(`${mareosId}-${currentDate}`, padding, 15);

                pdf.setFontSize(16);
                pdf.text('Competition Comparison Report', pageWidth / 2, 25, {
                    align: 'center'
                });

                // Capture Comparison Results Table
                html2canvas(document.querySelector('#resultsContainer .table-responsive'), {
                    scale: 2,
                    useCORS: true
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = pageWidth - (2 * padding);
                    const imgHeight = canvas.height * imgWidth / canvas.width;

                    pdf.addImage(imgData, 'PNG', padding, 30, imgWidth, imgHeight);

                    // Add a new page for the charts
                    pdf.addPage();

                    // Capture Average Per Arrow Chart
                    html2canvas(document.getElementById('avgPerArrowChart'), {
                        scale: 2,
                        useCORS: true
                    }).then(avgPerArrowCanvas => {
                        const avgPerArrowImgData = avgPerArrowCanvas.toDataURL('image/png');
                        const avgPerArrowImgWidth = pageWidth - (2 * padding);
                        const avgPerArrowImgHeight = avgPerArrowCanvas.height * avgPerArrowImgWidth / avgPerArrowCanvas.width;

                        pdf.setFontSize(14);
                        pdf.text("Average Per Arrow Comparison", pageWidth / 2, 20, {
                            align: "center"
                        });
                        pdf.addImage(avgPerArrowImgData, 'PNG', padding, 30, avgPerArrowImgWidth, avgPerArrowImgHeight);

                        // Capture Scoring Trends Chart
                        html2canvas(document.getElementById('scoreTrendChart'), {
                            scale: 2,
                            useCORS: true
                        }).then(scoreTrendCanvas => {
                            const scoreTrendImgData = scoreTrendCanvas.toDataURL('image/png');
                            const scoreTrendImgWidth = pageWidth - (2 * padding);
                            const scoreTrendImgHeight = scoreTrendCanvas.height * scoreTrendImgWidth / scoreTrendCanvas.width;

                            const yPosition = 30 + avgPerArrowImgHeight + 20; // 20 is for spacing
                            pdf.text("Scoring Trends", pageWidth / 2, yPosition, {
                                align: "center"
                            });
                            pdf.addImage(scoreTrendImgData, 'PNG', padding, yPosition + 10, scoreTrendImgWidth, scoreTrendImgHeight);

                            // Save the PDF
                            const pdfBlob = pdf.output('blob');
                            const pdfUrl = URL.createObjectURL(pdfBlob);
                            window.open(pdfUrl);
                        });
                    });
                });
            });
        } else {
            console.warn("Download button not found in the DOM");
        }
    });
</script>