<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Archery Stats'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Page-specific CSS (if a page have different css file )-->
    <?php if (isset($pageCSS)): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL . $pageCSS; ?>">
    <?php endif; ?>
</head>
<body>
    <!-- Navbar -->
    <?php include 'header.php'; ?>
    <div class="container mx-5">
