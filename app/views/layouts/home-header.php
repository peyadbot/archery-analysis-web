<!-- Main header tamplate -->
<style>

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
    /* Transparent Header */
    .transparent-header {
        background-color: transparent;
    }

    /* Colored Header */
    .colored-header {
        background-color: #343a40; /* or any color of your choice */
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
<div class="container-fluid ">
    <a class="navbar-brand" href="<?php echo BASE_URL . 'public/home.php'; ?>">
        <img src="https://github.com/mdo.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
        Archery Stats
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'app\views\training\training.php'; ?>">Training</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Competition
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL . 'app\views\competition\competition.php'; ?>">All</a></li>
                    <li><a class="dropdown-item" href="http://localhost/archery-analysis-web/app/views/competition/competition.php?filter_level=1">International</a></li>
                    <li><a class="dropdown-item" href="http://localhost/archery-analysis-web/app/views/competition/competition.php?filter_level=2">Local</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Athlete
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">All</a></li>
                    <li><a class="dropdown-item" href="#">Compound</a></li>
                    <li><a class="dropdown-item" href="#">Barebow</a></li>
                    <li><a class="dropdown-item" href="#">Recurve</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Coaches
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">All</a></li>
                    <li><a class="dropdown-item" href="#">Compound</a></li>
                    <li><a class="dropdown-item" href="#">Barebow</a></li>
                    <li><a class="dropdown-item" href="#">Recurve</a></li>
                </ul>
            </li>
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/profile.php'; ?>">Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/index.php'; ?>">Dashboard</a></li>
                        <form method="POST" action="<?php echo BASE_URL . 'app/handlers/LogoutHandler.php'; ?>">
                            <button type="submit" name="logout" class="dropdown-item">
                                Sign out
                            </button>
                        </form>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL . 'app/views/auth/login.php'; ?>">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
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