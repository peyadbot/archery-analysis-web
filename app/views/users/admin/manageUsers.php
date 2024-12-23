<?php
$title = 'Admin - Manage Users';
require_once __DIR__ . '/../../../handlers/DashboardViewHandler.php';
require_once __DIR__ . '/../../../handlers/UsersHandler.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$filterRole = isset($_GET['filterRole']) ? $_GET['filterRole'] : 'all';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if ($filterRole === 'coach') {
        $stmt = $pdo->prepare('
            SELECT u.user_id, u.username, p.mareos_id, p.name, u.role, u.created_at, u.updated_at 
            FROM users u 
            LEFT JOIN profiles p ON p.user_id = u.user_id 
            WHERE u.role = "coach"
            ORDER BY u.user_id ASC
        ');
    } elseif ($filterRole === 'athlete') {
        $stmt = $pdo->prepare('
            SELECT u.user_id, u.username, p.name, p.mareos_id, u.role, u.created_at, u.updated_at 
            FROM users u 
            LEFT JOIN profiles p ON p.user_id = u.user_id 
            WHERE u.role = "athlete"
            ORDER BY u.user_id ASC
        ');
    } elseif ($filterRole === 'admin') {
        $stmt = $pdo->prepare('
            SELECT u.user_id, u.username, u.role, p.mareos_id, u.created_at, u.updated_at 
            FROM users u
            LEFT JOIN profiles p ON p.user_id = u.user_id
            WHERE u.role = "admin"
            ORDER BY u.user_id ASC
        ');
    } else {
        $stmt = $pdo->prepare('
            SELECT u.user_id, u.username, u.role, p.mareos_id, u.created_at, u.updated_at 
            FROM users u
            LEFT JOIN profiles p ON p.user_id = u.user_id
            ORDER BY u.user_id ASC
        ');
    }
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to retrieve users: ' . $e->getMessage();
    header('Location: ' . BASE_URL . 'public/home.php');
    exit();
}

?>

<?php include '../../layouts/dashboard/header.php'; ?>

<style>
    .password-info {
        font-size: 0.875rem;
    }

    .password-info.invalid {
        color: #dc3545;
        /* Red */
    }

    .password-info.valid {
        color: #198754;
        /* Green */
    }
</style>

<div class="main-content" id="mainContent">
    <div class="row bg-dark text-white py-4 mb-4" style="border-radius: 10px;">
        <div class="col">
            <h3 class="m-0">Manage Your User</h3>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <div class="btn-group">
            <a href="?filterRole=all" class="btn <?php echo ($filterRole === 'all') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                View All
            </a>
            <a href="?filterRole=athlete" class="btn <?php echo ($filterRole === 'athlete') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                View Athletes
            </a>
            <a href="?filterRole=coach" class="btn <?php echo ($filterRole === 'coach') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                View Coaches
            </a>
            <a href="?filterRole=admin" class="btn <?php echo ($filterRole === 'admin') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                View Admins
            </a>
        </div>
    </div>

    <!-- Success and Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="alertMessage" class="alert alert-success mt-2" role="alert"><?php echo $_SESSION['success'];
                                                                                unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div id="alertMessage" class="alert alert-danger mt-2"><?php echo $_SESSION['error'];
                                                                unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if ($filterRole === 'all'): ?>
        <div class="accordion mb-4">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button <?php echo isset($editMode) && $editMode ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <?php echo isset($editMode) && $editMode ? 'Edit User' : 'Add New User'; ?>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse <?php echo isset($editMode) && $editMode ? 'show' : ''; ?>">
                    <div class="accordion-body">
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

                            <!-- Show password fields only in edit mode for password update -->
                            <?php if ($editMode): ?>
                                <hr>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" id="new_password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm_new_password" class="form-control" id="confirm_new_password">
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                    <small id="passwordInfo" class="password-info">Password must be at least 8 characters long.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Update User' : 'Add User'; ?></button>
                            <a href="manageUsers.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Users Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Mareos ID</th>
                <th>Username</th>
                <?php if ($filterRole === 'athlete' || $filterRole === 'coach'): ?>
                    <th>Name</th>
                <?php endif; ?>
                <?php if ($filterRole === 'athlete'): ?>
                    <th>Mareos ID</th>
                <?php endif; ?>
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
                        <td><?php echo htmlspecialchars($user['mareos_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <?php if ($filterRole === 'athlete' || $filterRole === 'coach'): ?>
                            <td><?php echo !empty($user['name']) ? htmlspecialchars($user['name']) : '-'; ?></td>
                        <?php endif; ?>
                        <?php if ($filterRole === 'athlete'): ?>
                            <td><?php echo !empty($user['mareos_id']) ? htmlspecialchars($user['mareos_id']) : '-'; ?></td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($user['updated_at']); ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <a href="<?php echo BASE_URL . 'app/handlers/ImpersonateHandler.php?impersonate=' . htmlspecialchars($user['user_id']); ?>" class="btn btn-sm btn-primary">
                                        Impersonate
                                    </a>
                                    <a href="<?php echo BASE_URL . 'app/handlers/AthleteReportHandler.php?athlete_user_id=' . $user['user_id']; ?>" class="btn btn-success btn-sm">
                                        Download Report
                                    </a>
                                    <a href="#" class="btn btn-info btn-sm view-profile-btn" data-bs-toggle="modal" data-bs-target="#profileModal" data-user-id="<?php echo $user['user_id']; ?>">
                                        View Profile
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Impersonate</button>
                                    <button class="btn btn-sm btn-secondary" disabled>Download Report</button>
                                    <button class="btn btn-sm btn-secondary" disabled>View Profile</button>
                                <?php endif; ?>
                                <a href="manageUsers.php?edit=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="manageUsers.php?delete=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- Modal User Profile -->
<div class="modal fade" id="profileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">User Profile</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalProfileContent">
                    <!-- User profile content will be dynamically loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Fetch user profile modal
    document.addEventListener('DOMContentLoaded', function() {
        const profileModal = document.getElementById('profileModal');
        const profileContent = document.getElementById('modalProfileContent');

        document.querySelectorAll('.view-profile-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = button.getAttribute('data-user-id');

                fetch('usersProfile.php?user_id=' + userId)
                    .then(response => response.text())
                    .then(data => {
                        profileContent.innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Error loading profile:', error);
                        profileContent.innerHTML = '<p>Failed to load profile information.</p>';
                    });
            });
        });
    });

    // Password validation
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const passwordInfo = document.getElementById('passwordInfo');

        // Check if password and confirm_password fields exist
        if (passwordField && confirmPasswordField && passwordInfo) {
            // Validate password length
            passwordField.addEventListener('input', function() {
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
            confirmPasswordField.addEventListener('input', function() {
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
                    confirmPasswordField.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
    });
</script>

<?php include '../../layouts/dashboard/footer.php'; ?>