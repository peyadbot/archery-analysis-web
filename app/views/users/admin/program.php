<?php
require_once __DIR__ . '/../../../handlers/ProgramHandler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programs - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Manage Programs</h1>

        <!-- Success and Error Messages -->
        <?php if ($success): ?>
            <div id="successMessage" class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div id="errorMessage" class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Program Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo isset($editMode) && $editMode ? 'Edit Program' : 'Add New Program'; ?></h5>
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
                    <?php if (isset($editMode) && $editMode): ?>
                        <a href="program.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Programs List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing Programs</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Program Name</th>
                            <th scope="col">Description</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($programs)): ?>
                            <?php foreach ($programs as $program): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($program['program_name']); ?></td>
                                    <td><?php echo htmlspecialchars($program['description']); ?></td>
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
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No programs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="index.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hide success and error messages
        setTimeout(function() {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 2000); 
    </script>
</body>
</html>
