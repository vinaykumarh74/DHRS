<?php
/**
 * Test Reset Link Functionality
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

echo "<h2>Reset Link Test</h2>";

// Test 1: Create a test user if it doesn't exist
echo "<h3>1. Creating Test User:</h3>";

$test_email = 'test@example.com';
$test_username = 'testuser';

try {
    $conn = get_db_connection();
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Create test user
        $hashed_password = password_hash('testpassword123', PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $phone = '1234567890';
        $role = 'citizen';
        $status = 'active';
        $stmt->bind_param("ssssss", $test_username, $hashed_password, $test_email, $phone, $role, $status);
        
        if ($stmt->execute()) {
            echo "<p>✅ Test user created successfully</p>";
        } else {
            echo "<p>❌ Failed to create test user: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>✅ Test user already exists</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 2: Request password reset
echo "<h3>2. Requesting Password Reset:</h3>";

$result = request_password_reset($test_email);
echo "<p>Result: " . ($result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
echo "<p>Message: " . $result['message'] . "</p>";

// Test 3: Check if reset token was created
echo "<h3>3. Checking Reset Token:</h3>";

try {
    $stmt = $conn->prepare("SELECT reset_token, reset_token_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (!empty($user['reset_token'])) {
            echo "<p>✅ Reset token created: " . substr($user['reset_token'], 0, 10) . "...</p>";
            echo "<p>✅ Token expiry: " . $user['reset_token_expiry'] . "</p>";
            
            // Test 4: Generate reset link
            echo "<h3>4. Generated Reset Link:</h3>";
            $reset_link = APP_URL . "/index.php?controller=auth&action=reset_password&token=" . $user['reset_token'];
            echo "<p><a href='$reset_link' target='_blank'>$reset_link</a></p>";
            
            // Test 5: Test reset link directly
            echo "<h3>5. Testing Reset Link:</h3>";
            echo "<p>Click the link above to test if it works, or test the reset_password function directly:</p>";
            
            // Test the reset_password function with a test password
            $test_new_password = 'newpassword123';
            $reset_result = reset_password($user['reset_token'], $test_new_password, $test_new_password);
            echo "<p>Reset function result: " . ($reset_result['success'] ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
            echo "<p>Reset message: " . $reset_result['message'] . "</p>";
            
        } else {
            echo "<p>❌ No reset token found</p>";
        }
    } else {
        echo "<p>❌ User not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error checking reset token: " . $e->getMessage() . "</p>";
}

// Test 6: Check development messages
echo "<h3>6. Development Messages:</h3>";
if (isset($_SESSION['dev_emails']) && !empty($_SESSION['dev_emails'])) {
    echo "<p>✅ Emails in session: " . count($_SESSION['dev_emails']) . "</p>";
    $latest_email = end($_SESSION['dev_emails']);
    echo "<p>Latest email to: " . $latest_email['to'] . "</p>";
    
    // Extract reset link from email body
    if (preg_match('/href=[\'"]([^\'"]+)[\'"]/', $latest_email['body'], $matches)) {
        echo "<p>✅ Reset link found in email: <a href='" . $matches[1] . "' target='_blank'>" . $matches[1] . "</a></p>";
    } else {
        echo "<p>❌ No reset link found in email body</p>";
    }
} else {
    echo "<p>❌ No emails in session</p>";
}

echo "<hr>";
echo "<p><a href='dev_messages.php' target='_blank'>View Development Messages</a></p>";
echo "<p><a href='index.php?controller=auth&action=forgot_password'>Test Forgot Password Page</a></p>";
?>
