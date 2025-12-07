<?php
// Start session
session_start();

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Display APP_URL
echo "<h2>APP_URL Configuration Test</h2>";
echo "<p>APP_URL is currently set to: <strong>" . APP_URL . "</strong></p>";

// Test reset link generation
echo "<h2>Reset Link Generation Test</h2>";
$token = "test_token_" . time();
$reset_link = APP_URL . "/index.php?controller=auth&action=reset_password&token=$token";
echo "<p>Generated reset link: <a href='$reset_link'>$reset_link</a></p>";

// Check current URL
echo "<h2>Current URL</h2>";
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
echo "<p>Your current URL base is: <strong>$current_url</strong></p>";

if ($current_url !== APP_URL) {
    echo "<p style='color: red;'><strong>Warning:</strong> Your current URL doesn't match APP_URL. This could cause reset links to fail.</p>";
    echo "<p>To fix this, update APP_URL in config/config.php to: <code>$current_url/DHRS</code></p>";
}

// Check if dev_messages are working
echo "<h2>Development Email Test</h2>";
send_email('test@example.com', 'Test Reset Link', "<p>This is a test reset link: <a href='$reset_link'>Reset Password</a></p>");

if (isset($_SESSION['dev_emails']) && !empty($_SESSION['dev_emails'])) {
    echo "<p>✅ Test email stored in session</p>";
    echo "<p><a href='dev_messages.php' target='_blank'>View Development Messages</a></p>";
} else {
    echo "<p>❌ Test email not stored in session</p>";
}
?>