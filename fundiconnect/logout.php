<?php
/**
 * Logout Page
 * Logs out the user and redirects to home page
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Logout user
logoutUser();

// Set flash message
setFlashMessage('success', 'You have been logged out successfully.');

// Redirect to home page
redirect('index.php');
?>
