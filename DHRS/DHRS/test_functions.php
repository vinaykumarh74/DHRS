<?php
/**
 * Test Script for DHRS Functions
 * 
 * This script tests that all functions are properly loaded and accessible
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

echo "<h2>DHRS Function Test</h2>";

// Test basic functions
echo "<h3>Testing Basic Functions:</h3>";
echo "<ul>";

// Test sanitize_input
$test_input = "<script>alert('test')</script>";
$sanitized = sanitize_input($test_input);
echo "<li>sanitize_input(): " . ($sanitized === htmlspecialchars($test_input, ENT_QUOTES, 'UTF-8') ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test email validation
echo "<li>is_valid_email('test@example.com'): " . (is_valid_email('test@example.com') ? "✅ PASS" : "❌ FAIL") . "</li>";
echo "<li>is_valid_email('invalid-email'): " . (!is_valid_email('invalid-email') ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test phone validation
echo "<li>is_valid_phone('1234567890'): " . (is_valid_phone('1234567890') ? "✅ PASS" : "❌ FAIL") . "</li>";
echo "<li>is_valid_phone('123'): " . (!is_valid_phone('123') ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test random string generation
$random_string = generate_random_string(10);
echo "<li>generate_random_string(10): " . (strlen($random_string) === 10 ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test OTP generation
$otp = generate_otp(6);
echo "<li>generate_otp(6): " . (strlen($otp) === 6 && is_numeric($otp) ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test date formatting
$formatted_date = format_date('2024-01-15');
echo "<li>format_date('2024-01-15'): " . ($formatted_date ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test time formatting
$formatted_time = format_time('14:30:00');
echo "<li>format_time('14:30:00'): " . ($formatted_time ? "✅ PASS" : "❌ FAIL") . "</li>";

echo "</ul>";

// Test authentication functions
echo "<h3>Testing Authentication Functions:</h3>";
echo "<ul>";

// Test is_logged_in (should be false since no session)
echo "<li>is_logged_in(): " . (!is_logged_in() ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test set_flash_message
set_flash_message('success', 'Test message');
echo "<li>set_flash_message(): " . (isset($_SESSION['flash_message']) ? "✅ PASS" : "❌ FAIL") . "</li>";

// Test get_setting (if database is available)
echo "<li>get_setting('site_name'): " . (function_exists('get_setting') ? "✅ PASS" : "❌ FAIL") . "</li>";

echo "</ul>";

// Test database connection
echo "<h3>Testing Database Connection:</h3>";
echo "<ul>";

if (function_exists('get_db_connection')) {
    echo "<li>get_db_connection() function: ✅ PASS</li>";
    
    // Try to establish connection (this might fail if database is not set up)
    try {
        $db = get_db_connection();
        echo "<li>Database connection: " . ($db && !$db->connect_error ? "✅ PASS" : "⚠️ CONNECTION FAILED (database may not be set up)") . "</li>";
    } catch (Exception $e) {
        echo "<li>Database connection: ⚠️ EXCEPTION (database may not be set up)</li>";
    }
} else {
    echo "<li>get_db_connection() function: ❌ FAIL</li>";
}

echo "</ul>";

// Test maintenance mode
echo "<h3>Testing System Functions:</h3>";
echo "<ul>";

if (function_exists('is_maintenance_mode')) {
    echo "<li>is_maintenance_mode() function: ✅ PASS</li>";
} else {
    echo "<li>is_maintenance_mode() function: ❌ FAIL</li>";
}

echo "</ul>";

echo "<h3>Test Summary:</h3>";
echo "<p>If you see mostly ✅ PASS marks above, your DHRS system functions are working correctly!</p>";
echo "<p>If you see ❌ FAIL marks, there may be issues that need to be resolved.</p>";
echo "<p>⚠️ marks indicate warnings (like database not being set up yet) but are not errors.</p>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Set up your database using the updated schema in <code>database/updated_schema.sql</code></li>";
echo "<li>Configure your database connection in <code>config/config.php</code></li>";
echo "<li>Test the full system functionality</li>";
echo "</ol>";
?>
