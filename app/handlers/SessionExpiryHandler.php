<!-- Session timeout -->

<?php 
session_start();

// 900 seconds = 15 minutes
define('SESSION_TIMEOUT', 900); 

function checkSessionTimeout() {
    if (isset($_SESSION['user_id'])) {
        if (isset($_SESSION['last_activity'])) {
            $timeSinceLastActivity = time() - $_SESSION['last_activity'];
            
            if ($timeSinceLastActivity > SESSION_TIMEOUT) {
                // Session has expired
                session_unset();
                session_destroy();
                header('Location: ' . BASE_URL . 'app/views/auth/login.php?timeout=1');
                exit();
            }
        }
        $_SESSION['last_activity'] = time();
    } else {
        header('Location: ' . BASE_URL . 'app/views/auth/login.php');
        exit();
    }
}