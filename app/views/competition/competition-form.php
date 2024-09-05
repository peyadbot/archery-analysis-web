<?php
require_once __DIR__ . '/../../../app/handlers/CompetitionHandler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($editMode) && $editMode ? 'Edit Competition' : 'Add Competition'; ?> - Archery Stats</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center"><?php echo isset($editMode) && $editMode ? 'Edit Competition' : 'Add New Competition'; ?></h2>
        <form method="POST" action="competition.php">
            <?php if ($editMode): ?>
                <input type="hidden" name="competition_id" value="<?php echo htmlspecialchars($editCompetition['competition_id']); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="competition_name" class="form-label">Competition Name</label>
                <input type="text" class="form-control" id="competition_name" name="competition_name" value="<?php echo htmlspecialchars($editCompetition['competition_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3 row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($editCompetition['start_date'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($editCompetition['end_date'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="registration_deadline" class="form-label">Registration Deadline</label>
                    <input type="date" class="form-control" id="registration_deadline" name="registration_deadline" value="<?php echo htmlspecialchars($editCompetition['registration_deadline'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($editCompetition['location'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($editCompetition['description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="level_id" class="form-label">Event Level</label>
                <select class="form-select" id="level_id" name="level_id" required>
                    <option value="">Select Event Level</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?php echo $level['level_id']; ?>" <?php echo (isset($editCompetition['level_id']) && $editCompetition['level_id'] == $level['level_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level['level_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-5 row">
                <div class="col-md-4">
                    <label for="bow_type" class="form-label">Bow Types</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="bow_type_recurve" id="bow_type_recurve" 
                        <?php if (isset($editCompetition) && $editCompetition['bow_type_recurve']) echo 'checked'; ?>>
                        <label class="form-check-label" for="bow_type_recurve">Recurve</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="bow_type_compound" id="bow_type_compound" 
                        <?php if (isset($editCompetition) && $editCompetition['bow_type_compound']) echo 'checked'; ?>>
                        <label class="form-check-label" for="bow_type_compound">Compound</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="bow_type_barebow" id="bow_type_barebow" 
                        <?php if (isset($editCompetition) && $editCompetition['bow_type_barebow']) echo 'checked'; ?>>
                        <label class="form-check-label" for="bow_type_barebow">Barebow</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <label for="event_type" class="form-label">Event Types</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="event_type_individual" id="event_type_individual" 
                        <?php if (isset($editCompetition) && $editCompetition['event_type_individual']) echo 'checked'; ?>>
                        <label class="form-check-label" for="event_type_individual">Individual</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="event_type_team" id="event_type_team" 
                        <?php if (isset($editCompetition) && $editCompetition['event_type_team']) echo 'checked'; ?>>
                        <label class="form-check-label" for="event_type_team">Team</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="event_type_mixed_team" id="event_type_mixed_team" 
                        <?php if (isset($editCompetition) && $editCompetition['event_type_mixed_team']) echo 'checked'; ?>>
                        <label class="form-check-label" for="event_type_mixed_team">Mixed Team</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Update Competition' : 'Add Competition'; ?></button>
        </form>
    </div>

    <script>
        // Start & End date form selector configuration
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // Set min date for start_date to today
            const today = new Date().toISOString().split('T')[0];
            startDateInput.setAttribute('min', today);

            // Update the end_date min value based on start_date
            startDateInput.addEventListener('change', function() {
                const startDate = new Date(startDateInput.value).toISOString().split('T')[0];
                endDateInput.setAttribute('min', startDate);
                // Clear end_date if it's less than the new start_date
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = '';
                }
            });

            // Validate end_date
            endDateInput.addEventListener('change', function() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (endDate < startDate) {
                    alert('End date must be after the start date.');
                    endDateInput.value = '';
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>