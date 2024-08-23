<?php
session_start();

require_once __DIR__ . '/../../controllers/logout.php';
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in and is a coach
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Input - Archery Stats</title>
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
        .form-row {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Coach Input Form</h1>
        <form action="process_coach_input.php" method="POST" enctype="multipart/form-data">
            <!-- Personal Information -->
            <div class="form-section">
                <h4>Personal Information</h4>
                <div class="form-row row">
                    <div class="col-md-4">
                        <label for="profile-picture" class="form-label">Upload Profile Picture</label>
                        <input type="file" class="form-control" id="profile-picture" name="profile_picture">
                    </div>
                    <div class="col-md-4">
                        <label for="full-name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full-name" name="full_name" required>
                    </div>
                    <div class="col-md-4">
                        <label for="date-of-birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date-of-birth" name="date_of_birth" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="contact-number" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contact-number" name="contact_number" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Home Address</label>
                        <input type="address" class="form-control" id="address" name="address" required>
                    </div>
                </div>
            </div>

            <!-- Professional Details -->
            <div class="form-section">
                <h4>Professional Details</h4>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="license-number" class="form-label">Coaching License Number</label>
                        <input type="text" class="form-control" id="license-number" name="license_number">
                    </div>
                    <div class="col-md-6">
                        <label for="coaching-level" class="form-label">Coaching Level/Certification</label>
                        <input type="text" class="form-control" id="coaching-level" name="coaching_level">
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="years-of-experience" class="form-label">Years of Experience</label>
                        <input type="number" class="form-control" id="years-of-experience" name="years_of_experience" min="0">
                    </div>
                    <div class="col-md-6">
                        <label for="specialization" class="form-label">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization">
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-6">
                        <label for="current-team" class="form-label">Current Team/Club</label>
                        <input type="text" class="form-control" id="current-team" name="current_team">
                    </div>
                </div>
            </div>

            <!-- Education & Training -->
            <div class="form-section">
                <h4>Education & Training</h4>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="education-background" class="form-label">Educational Background</label>
                        <textarea class="form-control" id="education-background" name="education_background" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="certifications" class="form-label">Coaching Certifications/Workshops</label>
                        <textarea class="form-control" id="certifications" name="certifications" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="professional-development" class="form-label">Professional Development Courses</label>
                        <textarea class="form-control" id="professional-development" name="professional_development" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="first-aid" class="form-label">First Aid Certification</label>
                        <input type="text" class="form-control" id="first-aid" name="first_aid">
                    </div>
                </div>
            </div>

            <!-- Previous Teams/Clubs Details -->
            <div class="form-section">
                <h4>Previous Teams/Clubs</h4>
                <div id="teams-clubs-container">
                    <div class="mb-3 team-club-entry form-row row">
                        <div class="col-md-4">
                            <label for="team_name_1" class="form-label">Team/Club Name</label>
                            <input type="text" class="form-control" id="team_name_1" name="team_name[]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date_1" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date_1" name="start_date[]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="end_date_1" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date_1" name="end_date[]" required>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" id="add-team-club">Add More</button>
            </div>

            <!-- Archery Background -->
            <div class="form-section">
                <h4>Archery Background</h4>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="personal-archery-experience" class="form-label">Personal Archery Experience</label>
                        <textarea class="form-control" id="personal-archery-experience" name="personal_archery_experience" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="competitions-participated" class="form-label">Competitions Participated</label>
                        <textarea class="form-control" id="competitions-participated" name="competitions_participated" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="awards-honors" class="form-label">Awards & Honors as an Athlete</label>
                        <textarea class="form-control" id="awards-honors" name="awards_honors" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h4>Additional Information</h4>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="languages-spoken" class="form-label">Languages Spoken</label>
                        <input type="text" class="form-control" id="languages-spoken" name="languages_spoken">
                    </div>
                </div>
                <div class="form-row row">
                    <div class="col-md-12">
                        <label for="coaching-philosophy" class="form-label">Coaching Philosophy</label>
                        <textarea class="form-control" id="coaching-philosophy" name="coaching_philosophy" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        document.getElementById('add-team-club').addEventListener('click', function() {
            const container = document.getElementById('teams-clubs-container');
            const index = container.children.length + 1;
            const newEntry = document.createElement('div');
            newEntry.classList.add('mb-3', 'team-club-entry');
            newEntry.innerHTML = `
                <div class="mb-3 team-club-entry form-row row">    
                    <div class="col-md-4">
                        <label for="team_name_${index}" class="form-label">Team/Club Name</label>
                        <input type="text" class="form-control" id="team_name_${index}" name="team_name[]" required>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date_${index}" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date_${index}" name="start_date[]" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date_${index}" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date_${index}" name="end_date[]" required>
                    </div>
                    <div class="col-md-2 d-flex flex-column align-items-center justify-content-end">
                        <button type="button" class="btn btn-danger btn-sm remove-team-club" style="height: 38px;">Remove</button>
                    </div>
                </div>
            `;
            container.appendChild(newEntry);
        });
        
        document.getElementById('teams-clubs-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-team-club')) {
                e.target.parentElement.parentElement.remove();
            }
        });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
