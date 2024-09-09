<?php
// session_start();
require_once __DIR__ . '/../../handlers/ProfileHandler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Archery Stats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4"><?php echo $profile ? 'Edit Profile' : 'Update Profile'; ?></h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-section mb-4">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="profile_picture" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    <?php if (!empty($profile['profile_picture'])): ?>
                        <img src="<?php echo '/public/images/user_img/' . htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail mt-2">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($profile['date_of_birth'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($profile['phone_number'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="row mb-3 mt-5">
                <div class="col-md-6">
                    <label for="ic_number" class="form-label">IC Number</label>
                    <input type="text" class="form-control" id="ic_number" name="ic_number" value="<?php echo htmlspecialchars($profile['ic_number'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="ic_file" class="form-label">Upload IC</label>
                    <input type="file" class="form-control" id="ic_file" name="ic_file">
                    <?php if (!empty($profile['ic_file'])): ?>
                        <a href="<?php echo '/public/images/user_img/' . htmlspecialchars($profile['ic_file']); ?>" target="_blank">View IC</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="passport_number" class="form-label">Passport Number</label>
                    <input type="text" class="form-control" id="passport_number" name="passport_number" value="<?php echo htmlspecialchars($profile['passport_number'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="passport_file" class="form-label">Upload Passport</label>
                    <input type="file" class="form-control" id="passport_file" name="passport_file">
                    <?php if (!empty($profile['passport_file'])): ?>
                        <a href="<?php echo '/public/images/user_img/' . htmlspecialchars($profile['passport_file']); ?>" target="_blank">View Passport</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="passport_expiry_date" class="form-label">Passport Expiry Date</label>
                    <input type="date" class="form-control" id="passport_expiry_date" name="passport_expiry_date" value="<?php echo htmlspecialchars($profile['passport_expiry_date'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="passport_issue_place" class="form-label">Passport Issue Place</label>
                    <input type="text" class="form-control" id="passport_issue_place" name="passport_issue_place" value="<?php echo htmlspecialchars($profile['passport_issue_place'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-3 mt-5">
                <div class="col-12">
                    <label for="home_address" class="form-label">Home Address</label>
                    <textarea class="form-control" id="home_address" name="home_address" rows="3"><?php echo htmlspecialchars($profile['home_address'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
    
        <!-- Submit and Reset Buttons -->
        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
