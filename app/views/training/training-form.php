<?php
require_once __DIR__ . '/../../../app/handlers/TrainingHandler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Training Session - Archery Stats</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center"><?php echo isset($editMode) && $editMode ? 'Edit Training' : 'Add New Training'; ?></h2>
        <form method="POST" action="training.php">
            <?php if ($editMode): ?>
                <input type="hidden" name="training_id" value="<?php echo htmlspecialchars($editTraining['training_id']); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="training_name" class="form-label">Session Name</label>
                <input type="text" class="form-control" id="training_name" name="training_name" value="<?php echo htmlspecialchars($editTraining['training_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($editTraining['location'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($editTraining['description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($editTraining['start_date'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($editTraining['end_date'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="mb-5 row">
                <div class="col-md-4">
                    <label for="bow_type" class="form-label">Bow Types</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bow_type_recurve" name="bow_type_recurve" <?php echo isset($editTraining['bow_type_recurve']) && $editTraining['bow_type_recurve'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="bow_type_recurve">Recurve</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bow_type_compound" name="bow_type_compound" <?php echo isset($editTraining['bow_type_compound']) && $editTraining['bow_type_compound'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="bow_type_compound">Compound</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bow_type_barebow" name="bow_type_barebow" <?php echo isset($editTraining['bow_type_barebow']) && $editTraining['bow_type_barebow'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="bow_type_barebow">Barebow</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <label for="event_type" class="form-label">Event Types</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="event_type_individual" name="event_type_individual" <?php echo isset($editTraining['event_type_individual']) && $editTraining['event_type_individual'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="event_type_individual">Individual</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="event_type_team" name="event_type_team" <?php echo isset($editTraining['event_type_team']) && $editTraining['event_type_team'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="event_type_team">Team</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="event_type_mixed_team" name="event_type_mixed_team" <?php echo isset($editTraining['event_type_mixed_team']) && $editTraining['event_type_mixed_team'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="event_type_mixed_team">Mixed Team</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo isset($editMode) && $editMode ? 'Update Training' : 'Add Training'; ?></button>
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
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-F6Vg05OYw96zXxDB0bV4DFPCLy/8VchM3ekZgnKCBpRRlWnEbxrJs7y+mY2t6lzM" crossorigin="anonymous"></script>
</body>
</html>
