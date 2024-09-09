<?php
session_start();
require_once __DIR__ . '/../../../handlers/UserHandler.php';
require_once __DIR__ . '/../../../../config/config.php';

// Fetch users from the database
try {
    $stmt = $pdo->prepare('SELECT user_id, username, role, created_at, updated_at FROM users');
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to retrieve users: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .password-info {
        font-size: 0.875rem;
    }
    .password-info.invalid {
        color: #dc3545; /* Red */
    }
    .password-info.valid {
        color: #198754; /* Green */
    }
</style>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Manage Users</h3>

        <!-- Success and Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="accordion mb-4">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button <?php echo isset($editMode) && $editMode ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <?php echo isset($editMode) && $editMode ? 'Edit User' : 'Add New User'; ?>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse <?php echo isset($editMode) && $editMode ? 'show' : ''; ?>">
                    <div class="accordion-body">
                        <!-- Programs Form -->
                        <form method="POST" id="registrationForm">
                            <?php if ($editMode): ?>
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($editUser['user_id']); ?>">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" id="username" value="<?php echo $editMode ? htmlspecialchars($editUser['username']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" class="form-select" id="role" required>
                                    <option value="athlete" <?php echo $editMode && $editUser['role'] == 'athlete' ? 'selected' : ''; ?>>Athlete</option>
                                    <option value="coach" <?php echo $editMode && $editUser['role'] == 'coach' ? 'selected' : ''; ?>>Coach</option>
                                    <option value="admin" <?php echo $editMode && $editUser['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            <?php if (!$editMode): ?>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                    <div class="password-info" id="passwordInfo">Password must be at least 8 characters long.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Update User' : 'Add User'; ?></button>
                            <a href="<?php echo BASE_URL . 'app/views/users/admin/user.php'; ?>" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($user['updated_at']); ?></td>
                            <?php if ($isAdmin): ?>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="user.php?edit=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="user.php?delete=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

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

        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const passwordInfo = document.getElementById('passwordInfo');
            const form = document.querySelector('form'); // Get the form element

            // Validate password length
            passwordField.addEventListener('input', function () {
                const password = passwordField.value;
                if (password.length > 0) {
                    if (password.length >= 8) {
                        passwordInfo.classList.add('valid');
                        passwordInfo.classList.remove('invalid');
                        passwordInfo.textContent = 'Password is valid.';
                    } else {
                        passwordInfo.classList.add('invalid');
                        passwordInfo.classList.remove('valid');
                        passwordInfo.textContent = 'Password must be at least 8 characters long.';
                    }
                } else {
                    passwordInfo.classList.remove('valid', 'invalid');
                    passwordInfo.textContent = 'Password must be at least 8 characters long.';
                }
            });

            // Validate confirm password
            confirmPasswordField.addEventListener('input', function () {
                const password = passwordField.value;
                const confirmPassword = confirmPasswordField.value;
                if (password.length > 0 && confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        confirmPasswordField.classList.add('is-valid');
                        confirmPasswordField.classList.remove('is-invalid');
                    } else {
                        confirmPasswordField.classList.add('is-invalid');
                        confirmPasswordField.classList.remove('is-valid');
                    }
                } else {
                    // Reset validation state if either field is empty
                    confirmPasswordField.classList.remove('is-valid', 'is-invalid');
                }
            });
        });
    </script>
</body>

</html>