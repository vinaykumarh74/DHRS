<?php
/**
 * Complete Reset Flow Test
 * Tests the entire forgot password -> reset password flow
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

echo "<h2>Complete Reset Flow Test</h2>";

// Step 1: Create a test user
echo "<h3>Step 1: Creating Test User</h3>";
$test_email = 'reset_test@example.com';
$test_username = 'resettest';
$original_password = 'originalpass123';

try {
    $conn = get_db_connection();
    
    // Delete existing test user if exists
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    
    // Create new test user
    $hashed_password = password_hash($original_password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $phone = '9876543210';
    $role = 'citizen';
    $status = 'active';
    $stmt->bind_param("ssssss", $test_username, $hashed_password, $test_email, $phone, $role, $status);
    
    if ($stmt->execute()) {
        echo "<p>✅ Test user created successfully</p>";
    } else {
        echo "<p>❌ Failed to create test user: " . $conn->error . "</p>";
        exit;
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error creating test user: " . $e->getMessage() . "</p>";
    exit;
}

// Step 2: Test login with original password
echo "<h3>Step 2: Testing Login with Original Password</h3>";
$login_result = login_user($test_username, $original_password);
echo "<p>Login result: " . ($login_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";

// Step 3: Request password reset
echo "<h3>Step 3: Requesting Password Reset</h3>";
$reset_request_result = request_password_reset($test_email);
echo "<p>Reset request result: " . ($reset_request_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
echo "<p>Message: " . $reset_request_result['message'] . "</p>";

// Step 4: Get the reset token
echo "<h3>Step 4: Getting Reset Token</h3>";
$stmt = $conn->prepare("SELECT reset_token, reset_token_expiry FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (!empty($user['reset_token'])) {
        echo "<p>✅ Reset token obtained: " . substr($user['reset_token'], 0, 10) . "...</p>";
        $reset_token = $user['reset_token'];
    } else {
        echo "<p>❌ No reset token found</p>";
        exit;
    }
} else {
    echo "<p>❌ User not found</p>";
    exit;
}

// Step 5: Test password reset with new password
echo "<h3>Step 5: Resetting Password</h3>";
$new_password = 'newpassword123';
$reset_result = reset_password($reset_token, $new_password, $new_password);
echo "<p>Reset result: " . ($reset_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
echo "<p>Message: " . $reset_result['message'] . "</p>";

// Step 6: Test login with old password (should fail)
echo "<h3>Step 6: Testing Login with Old Password (Should Fail)</h3>";
$old_login_result = login_user($test_username, $original_password);
echo "<p>Old password login: " . (!$old_login_result['success'] ? "✅ CORRECTLY FAILED" : "❌ INCORRECTLY SUCCEEDED") . "</p>";

// Step 7: Test login with new password (should succeed)
echo "<h3>Step 7: Testing Login with New Password (Should Succeed)</h3>";
$new_login_result = login_user($test_username, $new_password);
echo "<p>New password login: " . ($new_login_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";

// Step 8: Test that reset token is cleared
echo "<h3>Step 8: Verifying Reset Token is Cleared</h3>";
$stmt = $conn->prepare("SELECT reset_token FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (empty($user['reset_token'])) {
        echo "<p>✅ Reset token properly cleared</p>";
    } else {
        echo "<p>❌ Reset token not cleared</p>";
    }
}

// Step 9: Test expired token
echo "<h3>Step 9: Testing Expired Token</h3>";
// Create an expired token
$expired_token = 'expired_token_123';
$expired_time = date('Y-m-d H:i:s', strtotime('-2 hours'));
$stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
$stmt->bind_param("sss", $expired_token, $expired_time, $test_email);
$stmt->execute();

$expired_reset_result = reset_password($expired_token, 'testpass123', 'testpass123');
echo "<p>Expired token test: " . (!$expired_reset_result['success'] ? "✅ CORRECTLY FAILED" : "❌ INCORRECTLY SUCCEEDED") . "</p>";
echo "<p>Expired token message: " . $expired_reset_result['message'] . "</p>";

// Step 10: Test invalid token
echo "<h3>Step 10: Testing Invalid Token</h3>";
$invalid_reset_result = reset_password('invalid_token_123', 'testpass123', 'testpass123');
echo "<p>Invalid token test: " . (!$invalid_reset_result['success'] ? "✅ CORRECTLY FAILED" : "❌ INCORRECTLY SUCCEEDED") . "</p>";
echo "<p>Invalid token message: " . $invalid_reset_result['message'] . "</p>";

// Cleanup
echo "<h3>Cleanup</h3>";
$stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
echo "<p>✅ Test user cleaned up</p>";

echo "<hr>";
echo "<h3>Test Summary</h3>";
echo "<p>If all steps show ✅ SUCCESS or ✅ CORRECTLY FAILED, the reset link functionality is working properly!</p>";
echo "<p><a href='dev_messages.php' target='_blank'>View Development Messages</a></p>";
echo "<p><a href='index.php?controller=auth&action=forgot_password'>Test Forgot Password Page</a></p>";
?>
