<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archery Statistics</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
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
            /* background-color: rgba(0, 0, 0, 0.5); */
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
        .hero-section {
            background-image: url('assets/page_img/gradient.jpg'); /* Hero image URL */
            background-size: cover;
            background-position: center;
            height: 100vh; /* Full viewport height */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .hero-title {
            font-size: 2.5rem; /* Adjust font size for better responsiveness */
            font-weight: bold;
        }
        .hero-subtitle {
            font-size: 1.25rem; /* Adjust font size for better responsiveness */
        }
        .feature-icon {
            font-size: 2rem;
            color: #007bff;
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem; /* Adjust font size for smaller screens */
            }
            .hero-subtitle {
                font-size: 1rem; /* Adjust font size for smaller screens */
            }
        }
        .features-section {
            padding: 60px 0; /* Adjust top and bottom padding for extra space */
            min-height: 600px; /* Ensure the section has a minimum height */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
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
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Archery Stats</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Statistics</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Competitions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Training</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link"href="views/<?php echo htmlspecialchars($_SESSION['role']); ?>/dashboard.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="views/login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1 class="hero-title">Welcome to Archery Statistics</h1>
            <p class="hero-subtitle">Track and Analyze Your Archery Performance</p>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features-section container my-5">
        <div class="row text-center">
            <a class="hover-pointer col-md-4" href="comprehensive-stats.html">
                <i class="feature-icon bi bi-graph-up"></i>
                <h3>Comprehensive Stats</h3>
                <p>Get detailed statistics on your performance, including scores, accuracy, and more.</p>
            </a>
            <a class="hover-pointer col-md-4" href="competition-results.html">
                <i class="feature-icon bi bi-calendar"></i>
                <h3>Competition Results</h3>
                <p>View results from various competitions and compare your performance with others.</p>
            </a>
            <a class="hover-pointer col-md-4" href="personalized-training.html">
                <i class="feature-icon bi bi-person"></i>
                <h3>Personalized Training</h3>
                <p>Access training programs and track your progress to improve your skills.</p>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Archery Statistics. All rights reserved.</p>
    </footer>

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
