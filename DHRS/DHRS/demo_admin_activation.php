<?php
/**
 * Demo Admin User Activation
 * Shows how admin can activate/deactivate users
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

echo "<h2>Admin User Activation Demo</h2>";

// Show current users
echo "<h3>Current Users in Database:</h3>";

try {
    $conn = get_db_connection();
    
    $stmt = $conn->prepare("SELECT id, username, email, role, status, created_at FROM users ORDER BY id");
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background-color: #f8f9fa;'>";
    echo "<th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Admin Actions</th>";
    echo "</tr>";
    
    while ($user = $result->fetch_assoc()) {
        $status_color = $user['status'] === 'active' ? '#28a745' : ($user['status'] === 'pending' ? '#ffc107' : '#dc3545');
        $role_color = $user['role'] === 'admin' ? '#dc3545' : ($user['role'] === 'doctor' ? '#28a745' : '#17a2b8');
        
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td><span style='color: $role_color; font-weight: bold;'>" . ucfirst($user['role']) . "</span></td>";
        echo "<td><span style='color: $status_color; font-weight: bold;'>" . ucfirst($user['status']) . "</span></td>";
        echo "<td>" . date('M d, Y', strtotime($user['created_at'])) . "</td>";
        echo "<td>";
        
        // Show admin actions based on user status and role
        if ($user['role'] !== 'admin') {
            if ($user['status'] === 'active') {
                echo "<a href='index.php?controller=admin&action=deactivate_user&id={$user['id']}' style='color: #ffc107; text-decoration: none; margin-right: 10px;'>🔒 Deactivate</a>";
            } else {
                echo "<a href='index.php?controller=admin&action=activate_user&id={$user['id']}' style='color: #28a745; text-decoration: none; margin-right: 10px;'>✅ Activate</a>";
            }
            echo "<a href='index.php?controller=admin&action=delete_user&id={$user['id']}' style='color: #dc3545; text-decoration: none;' onclick='return confirm(\"Are you sure you want to delete this user?\")'>🗑️ Delete</a>";
        } else {
            echo "<span style='color: #6c757d;'>Admin Account</span>";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>User Status Meanings:</h3>";
echo "<ul>";
echo "<li><span style='color: #28a745; font-weight: bold;'>Active:</span> User can login and use the system</li>";
echo "<li><span style='color: #ffc107; font-weight: bold;'>Pending:</span> User registered but not verified (needs OTP verification)</li>";
echo "<li><span style='color: #dc3545; font-weight: bold;'>Inactive:</span> User account is disabled by admin</li>";
echo "</ul>";

echo "<h3>Admin Actions Available:</h3>";
echo "<ul>";
echo "<li><strong>✅ Activate:</strong> Change user status to 'active' (allows login)</li>";
echo "<li><strong>🔒 Deactivate:</strong> Change user status to 'inactive' (blocks login)</li>";
echo "<li><strong>🗑️ Delete:</strong> Remove user from system (cannot be undone)</li>";
echo "</ul>";

echo "<h3>Security Features:</h3>";
echo "<ul>";
echo "<li>✅ Admins cannot deactivate/delete themselves</li>";
echo "<li>✅ Admin accounts cannot be deleted</li>";
echo "<li>✅ All admin actions are logged for audit</li>";
echo "<li>✅ Confirmation dialogs prevent accidental actions</li>";
echo "</ul>";

echo "<h3>How to Use:</h3>";
echo "<ol>";
echo "<li><strong>Login as Admin:</strong> <a href='index.php?controller=auth&action=login'>Login Page</a></li>";
echo "<li><strong>Go to User Management:</strong> <a href='index.php?controller=admin&action=manage_users'>Manage Users</a></li>";
echo "<li><strong>Click the action buttons</strong> in the table above</li>";
echo "<li><strong>Confirm the action</strong> in the popup dialog</li>";
echo "</ol>";

echo "<div style='background-color: #d1ecf1; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8; margin-top: 20px;'>";
echo "<h4>💡 Example Scenarios:</h4>";
echo "<ul>";
echo "<li><strong>New User Registration:</strong> User registers → Status: 'pending' → Admin can activate to allow login</li>";
echo "<li><strong>User Misbehavior:</strong> Admin can deactivate user to temporarily block access</li>";
echo "<li><strong>Account Cleanup:</strong> Admin can delete inactive or problematic accounts</li>";
echo "<li><strong>Doctor Verification:</strong> Admin can activate doctor accounts after verifying credentials</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-top: 20px;'>";
echo "<h4>✅ Admin User Activation is Ready!</h4>";
echo "<p>The admin panel now has full user management capabilities including activation, deactivation, and deletion.</p>";
echo "<p>All actions are secure, logged, and have proper confirmation dialogs.</p>";
echo "</div>";
?>
