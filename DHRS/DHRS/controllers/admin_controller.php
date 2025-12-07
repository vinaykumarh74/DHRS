<?php
/**
 * Admin Controller
 * 
 * This controller handles admin dashboard and administrative functions.
 */

class AdminController {
    /**
     * Display the admin dashboard
     */
    public function dashboard() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get admin user data for display
        $admin_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get admin profile
        $stmt = $db->prepare("SELECT a.*, u.email, u.phone FROM administrators a 
                            JOIN users u ON a.user_id = u.id 
                            WHERE a.user_id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        // Get system statistics
        $stmt = $db->prepare("SELECT COUNT(*) as total_users FROM users WHERE status = 'active'");
        $stmt->execute();
        $total_users = $stmt->get_result()->fetch_assoc()['total_users'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as total_citizens FROM users WHERE role = 'citizen' AND status = 'active'");
        $stmt->execute();
        $total_citizens = $stmt->get_result()->fetch_assoc()['total_citizens'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as total_doctors FROM users WHERE role = 'doctor' AND status = 'active'");
        $stmt->execute();
        $total_doctors = $stmt->get_result()->fetch_assoc()['total_doctors'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as total_admins FROM users WHERE role = 'admin' AND status = 'active'");
        $stmt->execute();
        $total_admins = $stmt->get_result()->fetch_assoc()['total_admins'];
        
        // Include the admin dashboard view
        include('views/admin/dashboard.php');
    }
    
    /**
     * Display and manage all users
     */
    public function manage_users() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        $db = get_db_connection();
        
        // Get all users
        $stmt = $db->prepare("SELECT id, username, email, phone, role, status, created_at FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Include the manage users view
        include('views/admin/manage_users.php');
    }
    
    /**
     * Display system statistics
     */
    public function statistics() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        $db = get_db_connection();
        
        // Get user statistics
        $stmt = $db->prepare("SELECT COUNT(*) as total_users, 
                              SUM(CASE WHEN role = 'citizen' THEN 1 ELSE 0 END) as total_citizens,
                              SUM(CASE WHEN role = 'doctor' THEN 1 ELSE 0 END) as total_doctors,
                              SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins
                              FROM users WHERE status = 'active'");
        $stmt->execute();
        $user_stats = $stmt->get_result()->fetch_assoc();
        
        // Get additional statistics
        $stmt = $db->prepare("SELECT COUNT(*) as total_appointments FROM appointments");
        $stmt->execute();
        $total_appointments = $stmt->get_result()->fetch_assoc()['total_appointments'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as total_prescriptions FROM prescriptions");
        $stmt->execute();
        $total_prescriptions = $stmt->get_result()->fetch_assoc()['total_prescriptions'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as total_lab_reports FROM lab_reports");
        $stmt->execute();
        $total_lab_reports = $stmt->get_result()->fetch_assoc()['total_lab_reports'];
        
        // Include the statistics view
        include('views/admin/statistics.php');
    }
    
    /**
     * Display system settings
     */
    public function settings() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        $db = get_db_connection();
        
        // Get system settings
        $stmt = $db->prepare("SELECT setting_key, setting_value, description FROM system_settings ORDER BY setting_key");
        $stmt->execute();
        $settings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Include the settings view
        include('views/admin/settings.php');
    }
    
    /**
     * Update system setting
     */
    public function update_setting() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $setting_key = isset($_POST['setting_key']) ? sanitize_input($_POST['setting_key']) : '';
        $setting_value = isset($_POST['setting_value']) ? sanitize_input($_POST['setting_value']) : '';
        
        if (empty($setting_key) || empty($setting_value)) {
            echo json_encode(['success' => false, 'message' => 'Setting key and value are required']);
            return;
        }
        
        $db = get_db_connection();
        
        // Update the setting
        $stmt = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param("ss", $setting_value, $setting_key);
        
        if ($stmt->execute()) {
            // Log the action
            log_audit('update_setting', 'system_setting', 0, "Setting '$setting_key' updated to '$setting_value'");
            echo json_encode(['success' => true, 'message' => 'Setting updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update setting']);
        }
    }
    
    /**
     * Activate a user account
     */
    public function activate_user() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($user_id === 0) {
            set_flash_message('error', 'Invalid user ID');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        $db = get_db_connection();
        
        // Check if user exists
        $stmt = $db->prepare("SELECT id, username, status FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'User not found');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        $user = $result->fetch_assoc();
        
        // Update user status to active
        $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            set_flash_message('success', "User '{$user['username']}' has been activated successfully");
            
            // Log the action
            log_audit('user_activated', 'admin', $_SESSION['user_id'], "Admin activated user: {$user['username']} (ID: $user_id)");
        } else {
            set_flash_message('error', 'Failed to activate user');
        }
        
        redirect('index.php?controller=admin&action=manage_users');
    }
    
    /**
     * Deactivate a user account
     */
    public function deactivate_user() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($user_id === 0) {
            set_flash_message('error', 'Invalid user ID');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        // Prevent admin from deactivating themselves
        if ($user_id === $_SESSION['user_id']) {
            set_flash_message('error', 'You cannot deactivate your own account');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        $db = get_db_connection();
        
        // Check if user exists
        $stmt = $db->prepare("SELECT id, username, status FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'User not found');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        $user = $result->fetch_assoc();
        
        // Update user status to inactive
        $stmt = $db->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            set_flash_message('success', "User '{$user['username']}' has been deactivated successfully");
            
            // Log the action
            log_audit('user_deactivated', 'admin', $_SESSION['user_id'], "Admin deactivated user: {$user['username']} (ID: $user_id)");
        } else {
            set_flash_message('error', 'Failed to deactivate user');
        }
        
        redirect('index.php?controller=admin&action=manage_users');
    }
    
    /**
     * Delete a user account
     */
    public function delete_user() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($user_id === 0) {
            set_flash_message('error', 'Invalid user ID');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        // Prevent admin from deleting themselves
        if ($user_id === $_SESSION['user_id']) {
            set_flash_message('error', 'You cannot delete your own account');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        $db = get_db_connection();
        
        // Check if user exists
        $stmt = $db->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'User not found');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        $user = $result->fetch_assoc();
        
        // Prevent deletion of admin accounts
        if ($user['role'] === 'admin') {
            set_flash_message('error', 'Cannot delete admin accounts');
            redirect('index.php?controller=admin&action=manage_users');
        }
        
        // Delete user (cascade will handle related records)
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            set_flash_message('success', "User '{$user['username']}' has been deleted successfully");
            
            // Log the action
            log_audit('user_deleted', 'admin', $_SESSION['user_id'], "Admin deleted user: {$user['username']} (ID: $user_id)");
        } else {
            set_flash_message('error', 'Failed to delete user');
        }
        
        redirect('index.php?controller=admin&action=manage_users');
    }
    
    /**
     * Export users to Excel
     */
    public function export_users() {
        // Check if user is logged in and is an admin
        if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
            set_flash_message('error', 'You do not have permission to access this page');
            redirect('index.php?controller=auth&action=login');
        }
        
        $db = get_db_connection();
        
        // Get all users with their details
        $query = "SELECT 
                    u.id,
                    u.username,
                    u.email,
                    u.phone,
                    u.role,
                    u.status,
                    u.created_at,
                    CASE 
                        WHEN u.role = 'citizen' THEN CONCAT(c.first_name, ' ', c.last_name)
                        WHEN u.role = 'doctor' THEN CONCAT(d.first_name, ' ', d.last_name)
                        WHEN u.role = 'admin' THEN CONCAT(a.first_name, ' ', a.last_name)
                        ELSE 'N/A'
                    END as full_name,
                    CASE 
                        WHEN u.role = 'citizen' THEN c.health_id
                        WHEN u.role = 'doctor' THEN d.license_number
                        WHEN u.role = 'admin' THEN a.department
                        ELSE 'N/A'
                    END as identifier
                  FROM users u
                  LEFT JOIN citizens c ON u.id = c.user_id AND u.role = 'citizen'
                  LEFT JOIN doctors d ON u.id = d.user_id AND u.role = 'doctor'
                  LEFT JOIN administrators a ON u.id = a.user_id AND u.role = 'admin'
                  ORDER BY u.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Set headers for Excel download
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8 compatibility with Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add CSV headers
        fputcsv($output, [
            'ID',
            'Username',
            'Full Name',
            'Email',
            'Phone',
            'Role',
            'Status',
            'Identifier',
            'Created Date'
        ]);
        
        // Add user data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['username'],
                $user['full_name'],
                $user['email'],
                $user['phone'],
                ucfirst($user['role']),
                ucfirst($user['status']),
                $user['identifier'],
                date('Y-m-d H:i:s', strtotime($user['created_at']))
            ]);
        }
        
        fclose($output);
        exit();
    }
}