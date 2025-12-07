<?php
/**
 * Create Admin User Script
 * This script creates an admin user for the DHRS system
 */

// Load configuration
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

echo "<h2>Create Admin User</h2>";

// Admin credentials
$admin_username = 'admin';
$admin_email = 'admin@dhrs.com';
$admin_phone = '9999999999';
$admin_password = 'admin123';
$admin_first_name = 'System';
$admin_last_name = 'Administrator';

try {
    $conn = get_db_connection();
    
    // Check if admin user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $admin_username, $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>❌ Admin user already exists!</p>";
        
        // Show existing admin details
        $stmt = $conn->prepare("SELECT id, username, email, role, status FROM users WHERE username = ?");
        $stmt->bind_param("s", $admin_username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        echo "<h3>Existing Admin User:</h3>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $admin['id'] . "</li>";
        echo "<li><strong>Username:</strong> " . $admin['username'] . "</li>";
        echo "<li><strong>Email:</strong> " . $admin['email'] . "</li>";
        echo "<li><strong>Role:</strong> " . $admin['role'] . "</li>";
        echo "<li><strong>Status:</strong> " . $admin['status'] . "</li>";
        echo "</ul>";
        
        echo "<h3>Login Credentials:</h3>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        
    } else {
        // Create admin user
        $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $role = 'admin';
        $status = 'active'; // Admin is automatically active
        $stmt->bind_param("ssssss", $admin_username, $hashed_password, $admin_email, $admin_phone, $role, $status);
        
        if ($stmt->execute()) {
            $admin_user_id = $stmt->insert_id;
            echo "<p>✅ Admin user created successfully!</p>";
            
            // Create admin profile
            $stmt = $conn->prepare("INSERT INTO administrators (user_id, first_name, last_name, department, position) VALUES (?, ?, ?, ?, ?)");
            $department = 'System Administration';
            $position = 'System Administrator';
            $stmt->bind_param("issss", $admin_user_id, $admin_first_name, $admin_last_name, $department, $position);
            
            if ($stmt->execute()) {
                echo "<p>✅ Admin profile created successfully!</p>";
            } else {
                echo "<p>⚠️ Admin user created but profile creation failed: " . $conn->error . "</p>";
            }
            
            echo "<h3>Admin User Details:</h3>";
            echo "<ul>";
            echo "<li><strong>ID:</strong> " . $admin_user_id . "</li>";
            echo "<li><strong>Username:</strong> " . $admin_username . "</li>";
            echo "<li><strong>Email:</strong> " . $admin_email . "</li>";
            echo "<li><strong>Phone:</strong> " . $admin_phone . "</li>";
            echo "<li><strong>Role:</strong> " . $role . "</li>";
            echo "<li><strong>Status:</strong> " . $status . "</li>";
            echo "<li><strong>Department:</strong> " . $department . "</li>";
            echo "<li><strong>Position:</strong> " . $position . "</li>";
            echo "</ul>";
            
        } else {
            echo "<p>❌ Failed to create admin user: " . $conn->error . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Login Credentials:</h3>";
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;'>";
    echo "<p><strong>Username:</strong> <code>admin</code></p>";
    echo "<p><strong>Password:</strong> <code>admin123</code></p>";
    echo "</div>";
    
    echo "<h3>Admin Dashboard Access:</h3>";
    echo "<p><a href='index.php?controller=auth&action=login' class='btn btn-primary'>Go to Login Page</a></p>";
    echo "<p><a href='index.php?controller=admin&action=dashboard' class='btn btn-success'>Go to Admin Dashboard</a></p>";
    
    echo "<h3>Admin Features:</h3>";
    echo "<ul>";
    echo "<li>✅ User Management - View, edit, delete users</li>";
    echo "<li>✅ Doctor Verification - Approve/reject doctor registrations</li>";
    echo "<li>✅ System Statistics - View system analytics</li>";
    echo "<li>✅ System Settings - Configure system parameters</li>";
    echo "<li>✅ Activity Logs - Monitor user activities</li>";
    echo "</ul>";
    
    echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-top: 20px;'>";
    echo "<h4>⚠️ Security Note:</h4>";
    echo "<p>Please change the default admin password after first login for security reasons.</p>";
    echo "<p>You can change the password from the admin dashboard or profile settings.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
