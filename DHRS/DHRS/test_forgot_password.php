<?php
/**
 * Test Script for Forgot Password and OTP Functionality
 * 
 * This script tests the forgot password and OTP functionality
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';

// Load helper functions
require_once 'includes/functions.php';

// Load database connection function
require_once 'includes/database.php';

// Load authentication functions
require_once 'includes/auth.php';

echo "<h2>DHRS Forgot Password & OTP Test</h2>";

// Test 1: Check if reset token columns exist
echo "<h3>1. Database Schema Test:</h3>";
echo "<ul>";

try {
    $db = get_db_connection();
    
    // Check if reset_token column exists
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
    if ($result->num_rows > 0) {
        echo "<li>✅ reset_token column exists</li>";
    } else {
        echo "<li>❌ reset_token column missing - run database/add_reset_token_columns.sql</li>";
    }
    
    // Check if reset_token_expiry column exists
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'reset_token_expiry'");
    if ($result->num_rows > 0) {
        echo "<li>✅ reset_token_expiry column exists</li>";
    } else {
        echo "<li>❌ reset_token_expiry column missing - run database/add_reset_token_columns.sql</li>";
    }
    
    // Don't close the connection as it's used globally
} catch (Exception $e) {
    echo "<li>❌ Database connection error: " . $e->getMessage() . "</li>";
}

echo "</ul>";

// Test 2: Test email and SMS functions
echo "<h3>2. Email & SMS Functions Test:</h3>";
echo "<ul>";

// Test send_email function
$email_result = send_email('test@example.com', 'Test Subject', 'Test Body');
echo "<li>send_email() function: " . ($email_result ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test send_sms function
$sms_result = send_sms('1234567890', 'Test SMS Message');
echo "<li>send_sms() function: " . ($sms_result ? "✅ PASS" : "❌ FAIL") . "</li>";

// Check if messages are stored in session
echo "<li>Email stored in session: " . (isset($_SESSION['dev_emails']) && count($_SESSION['dev_emails']) > 0 ? "✅ PASS" : "❌ FAIL") . "</li>";
echo "<li>SMS stored in session: " . (isset($_SESSION['dev_sms']) && count($_SESSION['dev_sms']) > 0 ? "✅ PASS" : "❌ FAIL") . "</li>";

echo "</ul>";

// Test 3: Test request_password_reset function
echo "<h3>3. Password Reset Function Test:</h3>";
echo "<ul>";

// Test with invalid email
$result = request_password_reset('invalid-email');
echo "<li>Invalid email test: " . (!$result['success'] ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test with non-existent email
$result = request_password_reset('nonexistent@example.com');
echo "<li>Non-existent email test: " . (!$result['success'] ? "✅ PASS" : "❌ FAIL") . "</li>";

echo "</ul>";

// Test 4: Show development messages link
echo "<h3>4. Development Messages:</h3>";
echo "<ul>";
echo "<li><a href='dev_messages.php' target='_blank'>View Development Messages</a> - Check OTPs and reset links here</li>";
echo "</ul>";

// Test 5: Show current session data
echo "<h3>5. Current Session Data:</h3>";
echo "<ul>";
echo "<li>Emails in session: " . (isset($_SESSION['dev_emails']) ? count($_SESSION['dev_emails']) : 0) . "</li>";
echo "<li>SMS in session: " . (isset($_SESSION['dev_sms']) ? count($_SESSION['dev_sms']) : 0) . "</li>";
echo "</ul>";

echo "<h3>Test Summary:</h3>";
echo "<p>If you see mostly ✅ PASS marks above, the forgot password and OTP functionality should be working!</p>";
echo "<p>To test the full functionality:</p>";
echo "<ol>";
echo "<li>Go to <a href='index.php?controller=auth&action=register'>Registration Page</a></li>";
echo "<li>Register a new user</li>";
echo "<li>Check the <a href='dev_messages.php' target='_blank'>Development Messages</a> page for the OTP</li>";
echo "<li>Go to <a href='index.php?controller=auth&action=forgot_password'>Forgot Password Page</a></li>";
echo "<li>Enter an email and check the Development Messages page for the reset link</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If reset_token columns are missing, run: <code>database/add_reset_token_columns.sql</code></li>";
echo "<li>Test the full registration and forgot password flow</li>";
echo "<li>In production, configure real email and SMS services</li>";
echo "</ol>";
?>
