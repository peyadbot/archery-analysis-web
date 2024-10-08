<?php
    require_once __DIR__ . '/../../../app/handlers/RegisterHandler.php';
    $view = $_GET['view'] ?? 'register'; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }

        .register-container {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease;
        }

        .register-container h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .form-control {
            border-radius: 0.5rem;
        }

        button {
            border-radius: 0.5rem;
            background: #007bff;
            font-weight: bold;
            padding: 0.75rem;
        }

        button:hover {
            background: #0056b3;
        }

        .password-info {
            font-size: 0.875rem;
            margin-top: 0.5rem;
            color: #dc3545; /* Initial red color */
        }

        .password-info.valid {
            color: #198754; /* Green for valid state */
        }

        .link-toggle {
            text-align: center;
            margin-top: 1.5rem;
        }

        .link-toggle a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .link-toggle a:hover {
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>    
    <div class="register-container justify-content-center">
        <div class="d-flex justify-content-center mb-4">
            <div class="btn-group">
                <a href="login.php" class="btn <?php echo ($view === 'login') ? 'btn-secondary' : 'btn-outline-secondary'; ?>">Login</a>
                <a href="register.php" class="btn <?php echo ($view === 'register') ? 'btn-secondary' : 'btn-outline-secondary'; ?>">Register</a>
            </div>
        </div>

        <h2>Register</h2>
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
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="coach">Coach</option>
                    <option value="athlete">Athlete</option>
                </select>
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
            <button type="submit" class="btn btn-primary w-100">Register</button>
            
            <div class="link-toggle mt-4">
                <a href="<?php echo BASE_URL . 'index.php'; ?>">Home</a>
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
                } else {
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
