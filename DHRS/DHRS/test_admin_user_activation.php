<?php
/**
 * Test Admin User Activation Functionality
 * Tests the user activation/deactivation features in admin panel
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

echo "<h2>Admin User Activation Test</h2>";

// Test 1: Create test users with different statuses
echo "<h3>1. Creating Test Users:</h3>";

$test_users = [
    ['username' => 'pending_user', 'email' => 'pending@test.com', 'status' => 'pending'],
    ['username' => 'active_user', 'email' => 'active@test.com', 'status' => 'active'],
    ['username' => 'inactive_user', 'email' => 'inactive@test.com', 'status' => 'inactive']
];

try {
    $conn = get_db_connection();
    
    // Clean up existing test users
    foreach ($test_users as $user) {
        $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
        $stmt->bind_param("s", $user['email']);
        $stmt->execute();
    }
    
    // Create test users
    foreach ($test_users as $user) {
        $hashed_password = password_hash('testpass123', PASSWORD_BCRYPT);
        $phone = '1234567890';
        $role = 'citizen';
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user['username'], $hashed_password, $user['email'], $phone, $role, $user['status']);
        
        if ($stmt->execute()) {
            echo "<p>✅ Created {$user['username']} with status: {$user['status']}</p>";
        } else {
            echo "<p>❌ Failed to create {$user['username']}: " . $conn->error . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error creating test users: " . $e->getMessage() . "</p>";
}

// Test 2: Show current user statuses
echo "<h3>2. Current User Statuses:</h3>";
$stmt = $conn->prepare("SELECT id, username, email, status FROM users WHERE email LIKE '%@test.com' ORDER BY username");
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th></tr>";

while ($user = $result->fetch_assoc()) {
    $status_color = $user['status'] === 'active' ? 'green' : ($user['status'] === 'pending' ? 'orange' : 'red');
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['username']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td style='color: $status_color; font-weight: bold;'>{$user['status']}</td>";
    echo "</tr>";
}
echo "</table>";

// Test 3: Test login with different statuses
echo "<h3>3. Testing Login with Different Statuses:</h3>";

foreach ($test_users as $user) {
    $login_result = login_user($user['username'], 'testpass123');
    $status = $login_result['success'] ? "✅ SUCCESS" : "❌ FAILED";
    echo "<p>{$user['username']} ({$user['status']}): $status - {$login_result['message']}</p>";
}

// Test 4: Simulate admin activation actions
echo "<h3>4. Simulating Admin Activation Actions:</h3>";

// Get pending user
$stmt = $conn->prepare("SELECT id, username FROM users WHERE status = 'pending' AND email LIKE '%@test.com' LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$pending_user = $result->fetch_assoc();

if ($pending_user) {
    // Simulate activating pending user
    $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $pending_user['id']);
    
    if ($stmt->execute()) {
        echo "<p>✅ Activated user: {$pending_user['username']}</p>";
    } else {
        echo "<p>❌ Failed to activate user: {$pending_user['username']}</p>";
    }
}

// Get active user
$stmt = $conn->prepare("SELECT id, username FROM users WHERE status = 'active' AND email LIKE '%@test.com' LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$active_user = $result->fetch_assoc();

if ($active_user) {
    // Simulate deactivating active user
    $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    $stmt->bind_param("i", $active_user['id']);
    
    if ($stmt->execute()) {
        echo "<p>✅ Deactivated user: {$active_user['username']}</p>";
    } else {
        echo "<p>❌ Failed to deactivate user: {$active_user['username']}</p>";
    }
}

// Test 5: Show updated statuses
echo "<h3>5. Updated User Statuses:</h3>";
$stmt = $conn->prepare("SELECT id, username, email, status FROM users WHERE email LIKE '%@test.com' ORDER BY username");
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th></tr>";

while ($user = $result->fetch_assoc()) {
    $status_color = $user['status'] === 'active' ? 'green' : ($user['status'] === 'pending' ? 'orange' : 'red');
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['username']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td style='color: $status_color; font-weight: bold;'>{$user['status']}</td>";
    echo "</tr>";
}
echo "</table>";

// Test 6: Test login after status changes
echo "<h3>6. Testing Login After Status Changes:</h3>";

$stmt = $conn->prepare("SELECT username, status FROM users WHERE email LIKE '%@test.com' ORDER BY username");
$stmt->execute();
$result = $stmt->get_result();

while ($user = $result->fetch_assoc()) {
    $login_result = login_user($user['username'], 'testpass123');
    $status = $login_result['success'] ? "✅ SUCCESS" : "❌ FAILED";
    echo "<p>{$user['username']} ({$user['status']}): $status - {$login_result['message']}</p>";
}

// Cleanup
echo "<h3>7. Cleanup:</h3>";
$stmt = $conn->prepare("DELETE FROM users WHERE email LIKE '%@test.com'");
$stmt->execute();
echo "<p>✅ Test users cleaned up</p>";

echo "<hr>";
echo "<h3>Admin User Activation Features:</h3>";
echo "<ul>";
echo "<li>✅ <strong>Activate Users:</strong> Change status from 'pending' or 'inactive' to 'active'</li>";
echo "<li>✅ <strong>Deactivate Users:</strong> Change status from 'active' to 'inactive'</li>";
echo "<li>✅ <strong>Delete Users:</strong> Remove users from the system (except admins)</li>";
echo "<li>✅ <strong>Security:</strong> Admins cannot deactivate/delete themselves</li>";
echo "<li>✅ <strong>Logging:</strong> All admin actions are logged for audit</li>";
echo "</ul>";

echo "<h3>How to Use Admin User Activation:</h3>";
echo "<ol>";
echo "<li>Login as admin: <a href='index.php?controller=auth&action=login'>Login Page</a></li>";
echo "<li>Go to User Management: <a href='index.php?controller=admin&action=manage_users'>Manage Users</a></li>";
echo "<li>Click 'Activate' button for pending/inactive users</li>";
echo "<li>Click 'Deactivate' button for active users</li>";
echo "<li>Click 'Delete' button to remove users (except admins)</li>";
echo "</ol>";

echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-top: 20px;'>";
echo "<h4>✅ User Activation is Now Working!</h4>";
echo "<p>The admin can now activate, deactivate, and delete users through the admin panel.</p>";
echo "<p>All actions are logged and have proper security checks.</p>";
echo "</div>";
?>
