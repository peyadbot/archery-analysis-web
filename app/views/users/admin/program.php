<?php
require_once __DIR__ . '/../../../../app/handlers/ProgramHandler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programs - Archery Stats</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Manage Programs</h1>

        <!-- Success and Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="accordion my-4">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button <?php echo isset($editMode) && $editMode ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <?php echo isset($editMode) && $editMode ? 'Edit Program' : 'Add New Program'; ?>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse <?php echo isset($editMode) && $editMode ? 'show' : ''; ?>">
                    <div class="accordion-body">
                        <!-- Programs Form -->
                        <form action="program.php" method="POST">
                            <div class="mb-3">
                                <label for="program_name" class="form-label">Program Name</label>
                                <input type="text" class="form-control" id="program_name" name="program_name" value="<?php echo isset($editProgram) ? htmlspecialchars($editProgram['program_name']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo isset($editProgram) ? htmlspecialchars($editProgram['description']) : ''; ?></textarea>
                            </div>
                            <?php if (isset($editMode) && $editMode): ?>
                                <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($editProgram['program_id']); ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?php echo isset($editMode) && $editMode ? 'Update Program' : 'Add Program'; ?></button>
                            
                            <a href="program.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs List -->
        <div class="table-responsive">
            <h5 class="card-title mb-4">Existing Programs</h5>
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Program Name</th>
                        <th>Description</th>
                        <?php if ($isAdmin): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($programs)): ?>
                        <?php foreach ($programs as $program): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($program['program_id']); ?></td>
                                <td><?php echo htmlspecialchars($program['program_name']); ?></td>
                                <td><?php echo htmlspecialchars($program['description']); ?></td>
                                <?php if ($isAdmin): ?>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="program.php?edit=<?php echo htmlspecialchars($program['program_id']); ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="program.php?delete=<?php echo htmlspecialchars($program['program_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this program?');">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No programs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/index.php'; ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
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