<?php
// Start session
session_start();

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Set a test flash message - output after all session handling
ob_start();
echo '<p>Setting flash message...</p>';
set_flash_message('success', 'This is a test flash message from test_redirect.php');
echo '<p>Flash message set.</p>';

// Debug output
error_log("Flash message set in test_redirect.php");
error_log("Session data after setting flash message: " . print_r($_SESSION, true));

// Redirect to index.php
echo '<p>Redirecting to index.php...</p>';

// Flush output buffer before redirect
ob_end_flush();

redirect('index.php');