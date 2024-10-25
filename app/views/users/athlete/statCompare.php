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

<?php include '../../layouts/dashboard/header.php'; ?>

<!-- Chart.js, jspdf, and html2canvas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<div class="main-content" id="mainContent">
    <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Compare Statistics</h3>
        </div>
    </div>

    <form id="comparisonForm" action="" method="POST" class="shadow-sm">
        <div class="accordion" id="comparisonAccordion">
            <!-- Comparison Type Section -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="comparisonTypeHeading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#comparisonTypeCollapse" aria-expanded="true" aria-controls="comparisonTypeCollapse">
                        Select Comparison Type
                    </button>
                </h2>
                <div id="comparisonTypeCollapse" class="accordion-collapse collapse show" aria-labelledby="comparisonTypeHeading" data-bs-parent="#comparisonAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <!-- Metrics Selection -->
                            <div class="col-12">
                                <label class="form-label fw-bold">Metrics to Display</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($all_metrics as $metric_key => $metric_name): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="metrics[]" value="<?php echo $metric_key; ?>" id="metric_<?php echo $metric_key; ?>" <?php echo in_array($metric_key, $selected_metrics) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="metric_<?php echo $metric_key; ?>"><?php echo $metric_name; ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <hr class="hr my-5" />

                        <div class="row">
                            <!-- Competitions Selection -->
                            <div class="col-md-6">
                                <div id="competitionSelectionGroup">
                                    <?php
                                    $num_competitions = min(6, max(2, count($selected_competitions)));
                                    for ($i = 0; $i < $num_competitions; $i++):
                                    ?>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="competition<?php echo $i; ?>">Comp. <?php echo $i + 1; ?></label>
                                            <select id="competition<?php echo $i; ?>" name="competitions[]" class="form-control competition-select" required>
                                                <option value="">Select a competition</option>
                                                <?php foreach ($competitions as $competition): ?>
                                                    <option value="<?php echo htmlspecialchars($competition['competition_id']); ?>"
                                                        <?php echo (isset($selected_competitions[$i]) && $competition['competition_id'] == $selected_competitions[$i]) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($competition['competition_id']) . ' - ' . htmlspecialchars($competition['event_name']) . ' (' . date('d/m/Y', strtotime($competition['created_at'])) . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if ($i > 1): ?>
                                                <button class="btn btn-outline-danger remove-btn" type="button">Remove</button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary add-more-btn mt-2">Add Competition</button>
                            </div>
                            <div class="d-flex mt-4 justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">Compare</button>
                                <a href="?view=compare" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Comparison Results Section -->
    <?php if (!empty($comparison_data)): ?>
        <div class="card shadow-sm mb-4 mt-4" id="resultsContainer">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <h5 class="card-title mb-0">Comparison Results</h5>
                <button id="download-report-pdf" class="btn btn-light btn-sm float-end">Download Report</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                            <?php
                            $ordered_data = [];
                            foreach ($selected_competitions as $selected_id) {
                                foreach ($comparison_data as $data) {
                                    if ($data['competition_id'] == $selected_id) {
                                        $ordered_data[] = $data;
                                        break;
                                    }
                                }
                            }

                            foreach ($ordered_data as $index => $data):
                                $color = $colors[$index % count($colors)];
                            ?>
                                <tr style="background-color: <?php echo str_replace('1)', '0.2)', $color); ?>">
                                    <td style="font-weight: bold; color: <?php echo $color; ?>"><?php echo htmlspecialchars($data['competition_id']); ?></td>
                                    <td><?php echo htmlspecialchars($data['event_distance']); ?></td>
                                    <?php foreach ($selected_metrics as $metric): ?>
                                        <td>
                                            <?php
                                            echo htmlspecialchars($data[$metric]);
                                            $divisor = ($metric === 'total_score' || $metric === 'total_10X' || $metric === 'total_109') ? 72 : 36;
                                            $avgPerArrow = number_format($data[$metric] / $divisor, 2);
                                            echo " ({$avgPerArrow})";
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
            </div>
        </div>
    <?php endif; ?>
</div>

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

    // Event listeners to all select elements
    document.querySelectorAll('.competition-select').forEach(select => {
        select.addEventListener('change', updateDisabledOptions);
    });

    updateDisabledOptions();

    // Add more competitions
    document.querySelector('.add-more-btn').addEventListener('click', function() {
        const competitionGroup = document.getElementById('competitionSelectionGroup');
        const currentCount = competitionGroup.children.length;

        if (currentCount >= 6) {
            alert('You can compare a maximum of 6 competitions.');
            return;
        }

        const newSelection = document.createElement('div');
        newSelection.className = 'input-group mb-3';
        newSelection.innerHTML = `
        <label class="input-group-text" for="competition${currentCount}">Comp. ${currentCount + 1}</label>
        <select id="competition${currentCount}" name="competitions[]" class="form-control competition-select" required>
            <option value="">Select a competition</option>
            <?php foreach ($competitions as $competition): ?>
                <option value="<?php echo htmlspecialchars($competition['competition_id']); ?>">
                    <?php echo htmlspecialchars($competition['competition_id']) . ' - ' . htmlspecialchars($competition['event_name']) . ' (' . date('d/m/Y', strtotime($competition['created_at'])) . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-danger remove-btn" type="button">Remove</button>
    `;
        competitionGroup.appendChild(newSelection);

        const newSelect = newSelection.querySelector('.competition-select');
        newSelect.addEventListener('change', updateDisabledOptions);
        updateDisabledOptions();
    });

    // Update the remove button event listener
    document.getElementById('competitionSelectionGroup').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-btn')) {
            e.target.closest('.input-group').remove();

            // Update the labels for remaining competitions
            const competitions = this.querySelectorAll('.input-group');
            competitions.forEach((comp, index) => {
                comp.querySelector('.input-group-text').textContent = `Comp. ${index + 1}`;
                comp.querySelector('select').id = `competition${index}`;
            });

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

    // Download Report PDF
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

                html2canvas(document.querySelector('#resultsContainer .table-responsive'), {
                    scale: 2,
                    useCORS: true
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = pageWidth - (2 * padding);
                    const imgHeight = canvas.height * imgWidth / canvas.width;

                    pdf.addImage(imgData, 'PNG', padding, 30, imgWidth, imgHeight);

                    // Capture Average Per Arrow Chart
                    html2canvas(document.getElementById('avgPerArrowChart'), {
                        scale: 2,
                        useCORS: true
                    }).then(avgPerArrowCanvas => {
                        const avgPerArrowImgData = avgPerArrowCanvas.toDataURL('image/png');
                        const avgPerArrowImgWidth = pageWidth - (2 * padding);
                        const avgPerArrowImgHeight = avgPerArrowCanvas.height * avgPerArrowImgWidth / avgPerArrowCanvas.width;

                        pdf.setFontSize(14);
                        pdf.text("Average Per Arrow Comparison", pageWidth / 2, imgHeight + 45, {
                            align: "center"
                        });
                        pdf.addImage(avgPerArrowImgData, 'PNG', padding, imgHeight + 55, avgPerArrowImgWidth, avgPerArrowImgHeight);

                        // Capture Scoring Trends Chart
                        html2canvas(document.getElementById('scoreTrendChart'), {
                            scale: 2,
                            useCORS: true
                        }).then(scoreTrendCanvas => {
                            const scoreTrendImgData = scoreTrendCanvas.toDataURL('image/png');
                            const scoreTrendImgWidth = pageWidth - (2 * padding);
                            const scoreTrendImgHeight = scoreTrendCanvas.height * scoreTrendImgWidth / scoreTrendCanvas.width;

                            const yPosition = imgHeight + avgPerArrowImgHeight + 70;
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

<?php include '../../layouts/dashboard/footer.php'; ?>