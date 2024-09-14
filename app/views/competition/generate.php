<?php
// Start the session and include the necessary files
ob_start();
session_start();
require_once __DIR__ . '/../../../config/config.php'; // Adjust the path as per your project structure

// Fetch competitions with no generated_code
$stmt = $pdo->query("SELECT competition_id FROM competitions WHERE generated_code IS NULL OR generated_code = 'fd3fbacb'");
$competitions = $stmt->fetchAll();

foreach ($competitions as $competition) {
    // Generate a unique random code
    $generated_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    // Update the competition with the generated_code
    $stmt = $pdo->prepare("UPDATE competitions SET generated_code = ? WHERE competition_id = ?");
    $stmt->execute([$generated_code, $competition['competition_id']]);
}

echo "Generated codes for competitions missing generated_code.";

