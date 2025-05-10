<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout in seconds (1 minute = 60 seconds)
$timeout_duration = 600;

if (isset($_SESSION['LAST_ACTIVITY']) && 
   (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    header("Location: lock_screen.php");
    exit();
}
?>