<?php
/**
 * Test Unverified Account Handling
 * Tests what happens when trying to login with an unverified account
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

echo "<h2>Unverified Account Test</h2>";

// Test 1: Create an unverified user
echo "<h3>1. Creating Unverified User:</h3>";
$test_email = 'unverified@example.com';
$test_username = 'unverified_user';
$test_password = 'testpass123';

try {
    $conn = get_db_connection();
    
    // Delete existing test user if exists
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    
    // Create unverified user (status = 'pending')
    $hashed_password = password_hash($test_password, PASSWORD_BCRYPT);
    $otp = generate_otp();
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, role, status, otp, otp_expiry) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)");
    $phone = '1111111111';
    $role = 'citizen';
    $stmt->bind_param("sssssss", $test_username, $hashed_password, $test_email, $phone, $role, $otp, $otp_expiry);
    
    if ($stmt->execute()) {
        echo "<p>✅ Unverified user created successfully</p>";
        echo "<p>Status: pending</p>";
        echo "<p>OTP: $otp</p>";
    } else {
        echo "<p>❌ Failed to create unverified user: " . $conn->error . "</p>";
        exit;
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error creating unverified user: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Try to login with unverified account
echo "<h3>2. Testing Login with Unverified Account:</h3>";
$login_result = login_user($test_username, $test_password);
echo "<p>Login result: " . ($login_result['success'] ? "❌ INCORRECTLY SUCCEEDED" : "✅ CORRECTLY FAILED") . "</p>";
echo "<p>Message: " . $login_result['message'] . "</p>";

// Test 3: Check if user is redirected to verification
echo "<h3>3. Expected Behavior:</h3>";
echo "<p>✅ User should NOT be able to login</p>";
echo "<p>✅ User should see message: 'Account is not active. Please verify your account.'</p>";
echo "<p>✅ User should be directed to verify their account with OTP</p>";

// Test 4: Show how to verify the account
echo "<h3>4. How to Verify Account:</h3>";
echo "<p>1. Go to: <a href='index.php?controller=auth&action=verify_otp&user_id=" . $stmt->insert_id . "'>OTP Verification Page</a></p>";
echo "<p>2. Enter OTP: <strong>$otp</strong></p>";
echo "<p>3. After verification, user can login normally</p>";

// Test 5: Test verification process
echo "<h3>5. Testing OTP Verification:</h3>";
$user_id = $stmt->insert_id;
$verify_result = verify_otp($user_id, $otp);
echo "<p>Verification result: " . ($verify_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
echo "<p>Message: " . $verify_result['message'] . "</p>";

// Test 6: Try login after verification
echo "<h3>6. Testing Login After Verification:</h3>";
$login_after_verify = login_user($test_username, $test_password);
echo "<p>Login after verification: " . ($login_after_verify['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
echo "<p>Message: " . $login_after_verify['message'] . "</p>";

// Test 7: Test different account statuses
echo "<h3>7. Testing Different Account Statuses:</h3>";

// Test inactive account
$stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();

$inactive_login = login_user($test_username, $test_password);
echo "<p>Inactive account login: " . (!$inactive_login['success'] ? "✅ CORRECTLY FAILED" : "❌ INCORRECTLY SUCCEEDED") . "</p>";
echo "<p>Message: " . $inactive_login['message'] . "</p>";

// Test active account
$stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();

$active_login = login_user($test_username, $test_password);
echo "<p>Active account login: " . ($active_login['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
echo "<p>Message: " . $active_login['message'] . "</p>";

// Cleanup
echo "<h3>Cleanup:</h3>";
$stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
echo "<p>✅ Test user cleaned up</p>";

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p><strong>Unverified accounts (status = 'pending') cannot login</strong></p>";
echo "<p><strong>Users must verify their account with OTP before they can login</strong></p>";
echo "<p><strong>This is a security feature to ensure email/phone verification</strong></p>";

echo "<h3>User Flow for Unverified Accounts:</h3>";
echo "<ol>";
echo "<li>User registers → Account created with status 'pending'</li>";
echo "<li>User receives OTP via email/SMS</li>";
echo "<li>User tries to login → Gets message: 'Account is not active. Please verify your account.'</li>";
echo "<li>User goes to OTP verification page</li>";
echo "<li>User enters OTP → Account status changes to 'active'</li>";
echo "<li>User can now login successfully</li>";
echo "</ol>";

echo "<p><a href='dev_messages.php' target='_blank'>View Development Messages (for OTPs)</a></p>";
?>
