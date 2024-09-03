<?php
    require_once __DIR__ . '/../../../app/handlers/RegisterHandler.php';
    include __DIR__ . '/../layouts/header.php'
?>

<style>
    .register-container {
        max-width: 400px;
        width: 100%;
        padding: 2rem;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .register-container h2 {
        margin-bottom: 1rem;
    }
    .alert {
        margin-bottom: 1rem;
    }
    .password-info {
        font-size: 0.875rem;
    }
    .password-info.invalid {
        color: #dc3545; /* Red */
    }
    .password-info.valid {
        color: #198754; /* Green */
    }
    .link-toggle {
        text-align: center;
        margin-top: 1rem;
    }
</style>

<div class="register-container">
    <h2 class="text-center">Register</h2>
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="POST" action="" id="registrationForm">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="password-info" id="passwordInfo">Password must be at least 8 characters long.</div>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="coach">Coach</option>
                <option value="athlete">Athlete</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
        <div class="link-toggle">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const passwordInfo = document.getElementById('passwordInfo');
        const form = document.getElementById('registrationForm');

        // Validate password length
        form.addEventListener('input', function () {
            const password = passwordField.value;
            if (password.length > 0) {
                if (password.length >= 8) {
                    passwordInfo.classList.add('valid');
                    passwordInfo.classList.remove('invalid');
                } else {
                    passwordInfo.classList.add('invalid');
                    passwordInfo.classList.remove('valid');
                }
            }  else {
                passwordInfo.classList.remove('valid', 'invalid');
            }
        });

        // Validate confirm password
        form.addEventListener('input', function () {
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>