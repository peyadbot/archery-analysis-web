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
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($editTraining['date'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($editTraining['location'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($editTraining['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100"><?php echo $editMode ? 'Update Training' : 'Add Training'; ?></button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>