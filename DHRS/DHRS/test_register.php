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
set_flash_message('success', 'This is a test flash message from test_register.php');
echo '<p>Flash message set.</p>';

// Debug output
error_log("Flash message set in test_register.php");
error_log("Session data after setting flash message: " . print_r($_SESSION, true));

// Output session information
echo '<h1>Test Registration</h1>';
echo '<p>Session ID: ' . session_id() . '</p>';
echo '<p>Session Data:</p>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// Display flash messages
echo '<h2>Flash Messages</h2>';
echo '<p>Before display_flash_message:</p>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

display_flash_message();

echo '<p>After display_flash_message:</p>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

echo '<p><a href="index.php">Go to Home Page</a></p>';