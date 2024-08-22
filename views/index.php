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
</head>
<style>
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden; /* Prevent horizontal scroll */
    }
    .hero-section {
        background-image: url('../public/images/page_img/gradient.jpg'); /* Hero image URL */
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
</style>

<body>
    <!-- Navbar -->
    <?php include 'tamplate/header.php'; ?>

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
                <h3>Statistics</h3>
                <p>Get detailed statistics on your performance, including scores, accuracy, and more.</p>
            </a>
            <a class="hover-pointer col-md-4" href="competition-results.html">
                <i class="feature-icon bi bi-calendar"></i>
                <h3>Competition</h3>
                <p>View results from various competitions and compare your performance with others.</p>
            </a>
            <a class="hover-pointer col-md-4" href="personalized-training.html">
                <i class="feature-icon bi bi-person"></i>
                <h3>Athlete</h3>
                <p>Access training programs and track your progress to improve your skills.</p>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'tamplate/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
