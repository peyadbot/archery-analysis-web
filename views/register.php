<?php
session_start();
require_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // Assuming you want to allow role selection

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            $error = 'Username already exists';
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            $stmt->execute([$username, $hashed_password, $role]);

            // Redirect to login page after successful registration
            header('Location: login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
        }
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
</head>
<body>
    <div class="register-container">
        <h2 class="text-center">Register</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
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
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <div class="link-toggle">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
</body>
</html>
