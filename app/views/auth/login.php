<?php
require_once __DIR__ . '/../../../app/handlers/LoginHandler.php';

// For a specific page css & title 
// (page css)    $page_specific_css = 'dashboard.css';
// (page title)  $title = 'Dashboard - Archery Stats';\
// ?php echo BASE_URL . 'public/css/' . htmlspecialchars($current_page_css); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Full-page background */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease;
        }

        .login-container h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .login-container .form-control {
            border-radius: 0.5rem;
        }

        .login-container button {
            border-radius: 0.5rem;
            background: #007bff;
            font-weight: bold;
            padding: 0.75rem;
        }

        .login-container button:hover {
            background: #0056b3;
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
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="link-toggle mt-3">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        <div class="link-toggle mt-1">
            <a href="<?php echo BASE_URL . 'index.php'; ?>">Home</a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
