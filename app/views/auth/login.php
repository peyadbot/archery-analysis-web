<?php
    require_once __DIR__ . '/../../../app/handlers/LoginHandler.php';
    include __DIR__ . '/../layouts/header.php'
?>

<style>
    .login-container {
        max-width: 400px;
        width: 100%;
        padding: 2rem;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .login-container h2 {
        margin-bottom: 1rem;
    }
    .alert {
        margin-bottom: 1rem;
    }
    .text-center {
        text-align: center;
    }
    .link-toggle {
        text-align: center;
        margin-top: 1rem;
    }
</style>

<div class="login-container">
    <h2 class="text-center">Login MEE</h2>
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
        <div class="link-toggle">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>