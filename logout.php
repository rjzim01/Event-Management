<?php
// Start the session
session_start();

// Set a flash message
$_SESSION['flash_message'] = "Logout successful";

// Destroy the session
session_destroy();

session_start();

// Set a flash message
$_SESSION['flash_message'] = "Logout successful";

// Redirect to the login page
header('Location: login.php');
exit;
?>
