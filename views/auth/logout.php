<?php
// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: /archery-analysis-web/index.php');
    exit();
}