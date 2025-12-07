<?php
// Start session
session_start();

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Set a test flash message
echo '<p>Setting flash message...</p>';
set_flash_message('success', 'This is a test flash message from test_flash.php');
echo '<p>Flash message set.</p>';

// Debug output
error_log("Flash message set in test_flash.php");
error_log("Session data after setting flash message: " . print_r($_SESSION, true));

// Redirect to index.php
echo '<p>Redirecting to index.php in 3 seconds...</p>';
echo '<script>setTimeout(function() { window.location.href = "index.php"; }, 3000);</script>';

// Output session information
echo '<h1>Test Flash Message</h1>';
echo '<p>Session ID: ' . session_id() . '</p>';
echo '<p>Session Data:</p>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';