<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

try {
    // Test database connection
    $conn = new mysqli('localhost', 'root', '', 'dhrs_db');
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test if admin user exists
    $stmt = $conn->prepare("SELECT id, username, email, role, status FROM users WHERE username = 'admin'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo "<p style='color: green;'>✓ Admin user found:</p>";
        echo "<ul>";
        echo "<li>ID: " . $admin['id'] . "</li>";
        echo "<li>Username: " . $admin['username'] . "</li>";
        echo "<li>Email: " . $admin['email'] . "</li>";
        echo "<li>Role: " . $admin['role'] . "</li>";
        echo "<li>Status: " . $admin['status'] . "</li>";
        echo "</ul>";
        
        // Test password verification
        $test_password = 'password';
        $hashed_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        
        if (password_verify($test_password, $hashed_password)) {
            echo "<p style='color: green;'>✓ Password verification successful!</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification failed!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Admin user not found!</p>";
    }
    
    // Test if administrators table exists and has data
    $stmt = $conn->prepare("SELECT * FROM administrators LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin_details = $result->fetch_assoc();
        echo "<p style='color: green;'>✓ Administrator details found:</p>";
        echo "<ul>";
        echo "<li>First Name: " . $admin_details['first_name'] . "</li>";
        echo "<li>Last Name: " . $admin_details['last_name'] . "</li>";
        echo "<li>Department: " . $admin_details['department'] . "</li>";
        echo "<li>Position: " . $admin_details['position'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ Administrator details not found!</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>PHP Info</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active') . "</p>";
echo "<p>Current Working Directory: " . getcwd() . "</p>";
?>
