<?php
require_once __DIR__ . '/../../../app/handlers/CompetitionHandler.php';
require_once __DIR__ . '/../../../app/handlers/CompetitionViewHandler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competitions - Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<style>
    /* Custom Styles */
    body {
        background-color: #f8f9fa;
    }

    .card-header {
        background-color: #007bff;
        color: white;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
</style>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h3>All Competitions</h3>
            <?php if ($isAdminOrCoach): ?>
                <a class="btn btn-primary w-25 ms-2" href="competition-form.php" role="button">Add New</a>
            <?php endif; ?>
        </div>

        <div class="accordion mb-4 w-25">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Search & Filter
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse <?php echo isset($editMode) && $editMode ? 'show' : ''; ?>">
                    <div class="accordion-body">
                        <form class="row g-3" method="GET">
                            <!-- Search input -->
                            <div class="col-md-7">
                                <label for="search" class="form-label">Search</label>
                                <input id="search" class="form-control" type="search" name="search" placeholder="Search" aria-label="Search">
                            </div>

                            <!-- Dropdown to select search type (location, ID, or name) -->
                            <div class="col-md-5">
                                <label for="search_criteria" class="form-label">Search By</label>
                                <select id="search_criteria" class="form-select" name="search_criteria">
                                    <option value="name">Name</option>
                                    <option value="location">Location</option>
                                    <option value="id">ID</option>
                                </select>
                            </div>

                            <!-- Dropdown to filter by level -->
                            <div class="col-md-6">
                                <label for="filter_level" class="form-label">Level</label>
                                <select id="filter_level" class="form-select" name="filter_level">
                                    <option value="">All</option>
                                    <option value="1">International</option>
                                    <option value="2">Local</option>
                                </select>
                            </div>

                            <!-- Dropdown to filter by bow type -->
                            <div class="col-md-6">
                                <label for="filter_bow_type" class="form-label">Bow Type</label>
                                <select id="filter_bow_type" class="form-select" name="filter_bow_type">
                                    <option value="">All</option>    
                                    <option value="barebow">Barebow</option>
                                    <option value="compound">Compound</option>
                                    <option value="recurve">Recurve</option>
                                </select>
                            </div>

                            <!-- Dropdown to filter by event type -->
                            <div class="col-md-6">
                                <label for="filter_event_type" class="form-label">Event Type</label>
                                <select id="filter_event_type" class="form-select" name="filter_event_type">
                                    <option value="">All</option>    
                                    <option value="team">Team</option>
                                    <option value="mixed_team">Mixed Team</option>
                                    <option value="individual">Individual</option>
                                </select>
                            </div>

                            <!-- Submit button and Add New button (if admin or coach) -->
                            <div class="align-items-end">
                                <button class="btn btn-outline-success w-75" type="submit">Search</button>
                                <a href="<?php echo BASE_URL . 'app/views/competition/competition.php'; ?>" class="btn btn-secondary">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- Success and Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Competition Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Registration</th>
                        <th>Location</th>
                        <th>Bow</th>
                        <th>Event</th>
                        <th>Level</th>
                        <th>Description</th>
                        <?php if ($isAdminOrCoach): ?>
                            <th>Added By</th>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($competitions)): ?>
                        <?php foreach ($competitions as $competition) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($competition['competition_id']); ?></td>
                                <td><?php echo htmlspecialchars($competition['competition_name']); ?></td>
                                <td><?php echo htmlspecialchars($competition['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($competition['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($competition['registration_deadline']); ?></td>
                                <td><?php echo htmlspecialchars($competition['location']); ?></td>
                                <td><?php echo formatCompetitionBowTypes($competition); ?></td>
                                <td><?php echo formatCompetitionEventTypes($competition); ?></td>
                                <td><?php echo htmlspecialchars($competition['level_name']); ?></td>
                                <td><?php echo htmlspecialchars($competition['description']); ?></td>
                                <?php if ($isAdminOrCoach): ?>
                                    <td><?php echo htmlspecialchars($competition['username']); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="competition-form.php?edit=<?php echo htmlspecialchars($competition['competition_id']); ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <?php if ($isAdmin): ?>
                                                <a href="competition.php?delete=<?php echo htmlspecialchars($competition['competition_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this competition?');">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo $isAdminOrCoach ? '10' : '12'; ?>" class="text-center">No competition found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($isAdminOrCoach): ?>
            <a href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/index.php'; ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL . 'index.php'; ?>" class="btn btn-secondary mt-3">Back to Home</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hide success and error messages
        setTimeout(function() {
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 2000);
    </script>
</body>

</html>