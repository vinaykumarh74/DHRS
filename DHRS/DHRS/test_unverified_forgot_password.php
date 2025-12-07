<?php
/**
 * Test Forgot Password with Unverified Account
 * Tests what happens when an unverified account tries to reset password
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

echo "<h2>Unverified Account Forgot Password Test</h2>";

// Test 1: Create an unverified user
echo "<h3>1. Creating Unverified User:</h3>";
$test_email = 'unverified_reset@example.com';
$test_username = 'unverified_reset_user';
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
    $phone = '2222222222';
    $role = 'citizen';
    $stmt->bind_param("sssssss", $test_username, $hashed_password, $test_email, $phone, $role, $otp, $otp_expiry);
    
    if ($stmt->execute()) {
        echo "<p>✅ Unverified user created successfully</p>";
        echo "<p>Status: pending</p>";
        echo "<p>Email: $test_email</p>";
    } else {
        echo "<p>❌ Failed to create unverified user: " . $conn->error . "</p>";
        exit;
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error creating unverified user: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Try forgot password with unverified account
echo "<h3>2. Testing Forgot Password with Unverified Account:</h3>";
$forgot_result = request_password_reset($test_email);
echo "<p>Forgot password result: " . ($forgot_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
echo "<p>Message: " . $forgot_result['message'] . "</p>";

// Test 3: Check if reset token was created
echo "<h3>3. Checking if Reset Token was Created:</h3>";
$stmt = $conn->prepare("SELECT reset_token, reset_token_expiry FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (!empty($user['reset_token'])) {
        echo "<p>✅ Reset token was created: " . substr($user['reset_token'], 0, 10) . "...</p>";
        echo "<p>✅ Token expiry: " . $user['reset_token_expiry'] . "</p>";
        
        // Test 4: Try to reset password with unverified account
        echo "<h3>4. Testing Password Reset with Unverified Account:</h3>";
        $new_password = 'newpassword123';
        $reset_result = reset_password($user['reset_token'], $new_password, $new_password);
        echo "<p>Reset result: " . ($reset_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
        echo "<p>Message: " . $reset_result['message'] . "</p>";
        
        // Test 5: Try login with new password (should still fail because account is unverified)
        echo "<h3>5. Testing Login with New Password (Unverified Account):</h3>";
        $login_result = login_user($test_username, $new_password);
        echo "<p>Login result: " . ($login_result['success'] ? "❌ INCORRECTLY SUCCEEDED" : "✅ CORRECTLY FAILED") . "</p>";
        echo "<p>Message: " . $login_result['message'] . "</p>";
        
    } else {
        echo "<p>❌ No reset token found</p>";
    }
} else {
    echo "<p>❌ User not found</p>";
}

// Test 6: Show the proper flow for unverified accounts
echo "<h3>6. Proper Flow for Unverified Accounts:</h3>";
echo "<p><strong>For unverified accounts, users should:</strong></p>";
echo "<ol>";
echo "<li>✅ First verify their account with OTP</li>";
echo "<li>✅ Then they can use forgot password if needed</li>";
echo "<li>❌ Or they can reset password but still need to verify account to login</li>";
echo "</ol>";

// Test 7: Verify account first, then test forgot password
echo "<h3>7. Testing: Verify Account First, Then Forgot Password:</h3>";

// Verify the account
$user_id = $stmt->insert_id;
$verify_result = verify_otp($user_id, $otp);
echo "<p>Account verification: " . ($verify_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";

// Now try forgot password again
$forgot_result2 = request_password_reset($test_email);
echo "<p>Forgot password after verification: " . ($forgot_result2['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";

// Test login with new password after verification
$stmt = $conn->prepare("SELECT reset_token FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!empty($user['reset_token'])) {
    $reset_result2 = reset_password($user['reset_token'], 'finalpassword123', 'finalpassword123');
    echo "<p>Password reset after verification: " . ($reset_result2['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
    
    $final_login = login_user($test_username, 'finalpassword123');
    echo "<p>Final login test: " . ($final_login['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
}

// Cleanup
echo "<h3>Cleanup:</h3>";
$stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
echo "<p>✅ Test user cleaned up</p>";

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p><strong>Unverified accounts CAN use forgot password, but they still need to verify their account to login</strong></p>";
echo "<p><strong>This allows users to reset their password even if they forgot it before verification</strong></p>";
echo "<p><strong>However, they must still complete the OTP verification process to activate their account</strong></p>";

echo "<h3>Recommended User Flow:</h3>";
echo "<ol>";
echo "<li>User registers → Account created with status 'pending'</li>";
echo "<li>User receives OTP via email/SMS</li>";
echo "<li>If user forgot password before verification → Can use forgot password</li>";
echo "<li>User must still verify account with OTP to activate it</li>";
echo "<li>After verification → User can login with new password</li>";
echo "</ol>";

echo "<p><a href='dev_messages.php' target='_blank'>View Development Messages (for OTPs and reset links)</a></p>";
?>
