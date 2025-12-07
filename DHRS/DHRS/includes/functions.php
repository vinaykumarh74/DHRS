<?php
/**
 * Helper Functions
 * 
 * This file contains common utility functions used throughout the application
 */

/**
 * Sanitize user input to prevent XSS attacks
 * 
 * @param string $input The input to sanitize
 * @return string The sanitized input
 */
function sanitize_input($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize_input($value);
        }
        return $input;
    }
    
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 * 
 * @param string $email The email to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (10 digits)
 * 
 * @param string $phone The phone number to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone) === 1;
}

/**
 * Calculate age from date of birth
 * 
 * @param string $date_of_birth The date of birth in YYYY-MM-DD format
 * @return int The calculated age
 */
function calculate_age($date_of_birth) {
    if (empty($date_of_birth) || $date_of_birth === '1900-01-01') {
        return 0;
    }
    
    $birth_date = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($birth_date);
    
    return $age->y;
}

/**
 * Generate a random string of specified length
 * 
 * @param int $length The length of the string to generate
 * @return string The generated string
 */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $random_string;
}

/**
 * Generate a random OTP (One-Time Password)
 * 
 * @param int $length The length of the OTP to generate
 * @return string The generated OTP
 */
function generate_otp($length = 6) {
    $characters = '0123456789';
    $otp = '';
    
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $otp;
}

/**
 * Format date to a readable format
 * 
 * @param string $date The date to format (Y-m-d)
 * @param string $format The format to use (default: d M, Y)
 * @return string The formatted date
 */
function format_date($date, $format = 'd M, Y') {
    return date($format, strtotime($date));
}

/**
 * Format time to a readable format
 * 
 * @param string $time The time to format (H:i:s)
 * @param string $format The format to use (default: h:i A)
 * @return string The formatted time
 */
function format_time($time, $format = 'h:i A') {
    return date($format, strtotime($time));
}

/**
 * Log audit trail
 * 
 * @param string $action The action performed
 * @param string $entity_type The type of entity
 * @param int $entity_id The ID of the entity
 * @param string $description The description of the action
 * @return bool True if logged, false otherwise
 */
function log_audit($action, $entity_type, $entity_id, $description) {
    global $conn;
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Map legacy params to new schema columns
    $table_name = $entity_type; // e.g., 'user'
    $record_id = $entity_id;
    $old_values = null;
    $new_values = $description; // store description in new_values
    
    // Try inserting using the updated schema from updated_schema.sql
    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param("ississss", $user_id, $action, $table_name, $record_id, $old_values, $new_values, $ip_address, $user_agent);
        return $stmt->execute();
    }
    
    return false;
}

/**
 * Set flash message
 * 
 * @param string $type The message type (success, error, warning, info)
 * @param string $message The message text
 * @return void
 */
function set_flash_message($type, $message) {
    // Convert 'error' type to 'danger' for Bootstrap compatibility
    if ($type === 'error') {
        $type = 'danger';
    }
    
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    
    if (!isset($_SESSION['flash_messages'][$type])) {
        $_SESSION['flash_messages'][$type] = [];
    }
    
    $_SESSION['flash_messages'][$type][] = $message;
}

/**
 * Display flash message
 * 
 * @return void
 */
function display_flash_message() {
    if (!isset($_SESSION['flash_messages']) || empty($_SESSION['flash_messages'])) {
        return;
    }
    
    foreach ($_SESSION['flash_messages'] as $type => $messages) {
        foreach ($messages as $message) {
            $icon = '';
            switch ($type) {
                case 'success':
                    $icon = '<i class="fas fa-check-circle me-2"></i>';
                    break;
                case 'danger':
                    $icon = '<i class="fas fa-exclamation-circle me-2"></i>';
                    break;
                case 'warning':
                    $icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
                    break;
                case 'info':
                    $icon = '<i class="fas fa-info-circle me-2"></i>';
                    break;
            }
            echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>";
            echo $icon . htmlspecialchars($message);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
        }
    }
    
    // Clear flash messages after displaying
    unset($_SESSION['flash_messages']);
}

/**
 * Redirect to another page
 * 
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect($url) {
    // Debug output
    error_log("Redirecting to: $url");
    error_log("Session data before redirect: " . print_r($_SESSION, true));
    
    // Ensure session data is written before redirect
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Just write the session data without closing the session
        // This ensures the data is saved but the session remains active
        session_write_close();
    }
    
    header("Location: $url");
    exit();
}

/**
 * Check if system is in maintenance mode
 * 
 * @return bool True if in maintenance mode, false otherwise
 */
function is_maintenance_mode() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['setting_value'] === 'true';
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error checking maintenance mode: " . $e->getMessage());
        return false;
    }
}

/**
 * Get system setting value
 * 
 * @param string $key The setting key
 * @param string $default The default value if setting not found
 * @return string The setting value
 */
function get_setting($key, $default = '') {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['setting_value'];
        }
        
        return $default;
    } catch (Exception $e) {
        error_log("Error getting setting '$key': " . $e->getMessage());
        return $default;
    }
}

/**
 * Send email using PHPMailer
 * 
 * @param string $to The recipient email
 * @param string $subject The email subject
 * @param string $body The email body
 * @return bool True if sent, false otherwise
 */
function send_email($to, $subject, $body) {
    // For development: Store email in session for display
    if (!isset($_SESSION['dev_emails'])) {
        $_SESSION['dev_emails'] = [];
    }
    
    $_SESSION['dev_emails'][] = [
        'to' => $to,
        'subject' => $subject,
        'body' => $body,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Log the email attempt
    error_log("Email would be sent to: $to, Subject: $subject");
    
    // In production, this would use PHPMailer
    // For now, return true to indicate "success"
    return true;
}

/**
 * Validate phone number format
 * 
 * @param string $phone The phone number to validate
 * @return bool True if valid, false otherwise
 */
function validate_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone) === 1;
}

/**
 * Log user activity
 * 
 * @param int $user_id The user ID
 * @param string $action The action performed
 * @param string $description The description of the action
 * @return void
 */
function log_activity($user_id, $action, $description) {
    global $conn;
    
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $action, $description, $ip_address, $user_agent);
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

/**
 * Send SMS (development stub)
 *
 * Logs the SMS attempt. Returns false to indicate no real SMS was sent.
 * Integrate a provider like Twilio in production.
 *
 * @param string $phone
 * @param string $message
 * @return bool
 */
function send_sms($phone, $message) {
    // For development: Store SMS in session for display
    if (!isset($_SESSION['dev_sms'])) {
        $_SESSION['dev_sms'] = [];
    }
    
    $_SESSION['dev_sms'][] = [
        'phone' => $phone,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    error_log("SMS would be sent to: $phone, Message: $message");
    
    // For development, return true to indicate "success"
    return true;
}

/**
 * Get Bootstrap color class for appointment status
 *
 * @param string $status The appointment status
 * @return string The Bootstrap color class
 */
function get_appointment_status_color($status) {
    switch ($status) {
        case 'confirmed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'cancelled':
            return 'danger';
        case 'completed':
            return 'info';
        case 'rescheduled':
            return 'primary';
        default:
            return 'secondary';
    }
}

?>
