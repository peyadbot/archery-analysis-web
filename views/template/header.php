
<style>
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden; /* Prevent horizontal scroll */
    }
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: transparent;
        transition: background-color 0.4s ease-in-out;
        backdrop-filter: blur(1px); 
        z-index: 1000; 
        padding: 0.5rem 1rem; 
    }
    .navbar.scrolled {
        background-color: rgba(0, 0, 0, 0.4);
    }
    .navbar .nav-link,
    .navbar .navbar-brand {
        color: white;
        transition: color 0.4s ease-in-out; 
    }
    .navbar .nav-link:hover {
        color: #0056b3;
    }
    .hover-pointer {
        cursor: pointer;
        text-decoration: none; /* Remove underline from links */
        color: inherit; /* Inherit color from parent */
    }

    .hover-pointer:hover {
        background-color: rgba(0, 0, 0, 0.1); 
        text-decoration: none; /* Ensure text decoration is not overridden */
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Archery Stats</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'index.php'; ?>">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Statistics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/archery-analysis-web/views/competition/index.php">Competitions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Training</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Contact</a>
            </li>
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL . 'views/' . htmlspecialchars($_SESSION['role']) . '/dashboard.php'; ?>">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL . 'views/auth/login.php'; ?>">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
    // Show nav background during scrolling
    document.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        
        // Add a background color when scrolled 50px from the top
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>