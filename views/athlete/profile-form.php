<?php
session_start();

require_once __DIR__ . '/../../controllers/logout.php';
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and is a coach
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'athlete') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Form - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .form-section {
            margin-bottom: 2rem;
        }
        .form-section h4 {
            margin-bottom: 1rem;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 0.5rem;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Profile Form</h1>
        <form action="process_profile.php" method="POST" enctype="multipart/form-data">
            <!-- Personal Information -->
            <div class="form-section">
                <h4>Personal Information</h4>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="first-name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first-name" name="first_name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last-name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last-name" name="last_name" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone-number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone-number" name="phone_number" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="profile-picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile-picture" name="profile_picture">
                    </div>
                    <div class="col-md-6">
                        <label for="ic-number" class="form-label">IC Number</label>
                        <input type="text" class="form-control" id="ic-number" name="ic_number" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="passport-number" class="form-label">Passport Number</label>
                        <input type="text" class="form-control" id="passport-number" name="passport_number">
                    </div>
                    <div class="col-md-6">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
