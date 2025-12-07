<?php
// Start session
session_start();

// Set a test value in session
$_SESSION['test_value'] = 'This is a test value at ' . date('Y-m-d H:i:s');

// Output session information
echo '<h1>Session Test</h1>';
echo '<p>Session ID: ' . session_id() . '</p>';
echo '<p>Session Data:</p>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// Set a flash message for testing
if (!isset($_SESSION['flash_messages'])) {
    $_SESSION['flash_messages'] = [];
}
if (!isset($_SESSION['flash_messages']['success'])) {
    $_SESSION['flash_messages']['success'] = [];
}
$_SESSION['flash_messages']['success'][] = 'This is a test flash message at ' . date('Y-m-d H:i:s');

echo '<p><a href="test_session_check.php">Check Session in Another Page</a></p>';