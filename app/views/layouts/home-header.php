<!-- Main header template -->
<style>
    /* Navbar Styling */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        padding: 1rem;
        z-index: 1000;
        background-color: transparent;
        transition: background-color 0.4s ease, backdrop-filter 0.4s ease;
        backdrop-filter: blur(10px); /* Soft blur for modern look */
    }
    .navbar.scrolled {
        background-color: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px); /* Additional blur on scroll */
    }
    .navbar .nav-link,
    .navbar .navbar-brand {
        color: #ffffff;
        transition: color 0.4s ease;
    }
    .navbar .nav-link:hover {
        color: #007bff;
    }
    /* Hover Effects for Dropdowns and Links */
    .hover-pointer {
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .hover-pointer:hover {
        background-color: rgba(0, 0, 0, 0.1);
        color: #007bff; /* Hover color for nav items */
    }
    /* Dropdown Menu Styling */
    .dropdown-menu {
        background-color: #343a40; /* Darker background for dropdown */
        border-radius: 0.5rem;
    }
    .dropdown-item {
        color: #ffffff;
        transition: background-color 0.3s ease;
    }
    .dropdown-item:hover {
        background-color: #007bff;
        color: #fff;
    }
    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar-brand {
            font-size: 1.25rem;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid m-3">
        <a class="navbar-brand" href="<?php echo BASE_URL . 'index.php'; ?>">
            <img src="https://github.com/mdo.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
            Archery Stats
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL . 'app/views/profiles/profile.php'; ?>">Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL . 'app/views/users/' . htmlspecialchars($_SESSION['role']) . '/index.php'; ?>">Dashboard</a></li>
                            <form method="POST" action="">
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
    // Navbar scroll effect
    document.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>