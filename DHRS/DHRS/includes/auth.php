<?php
/**
 * Authentication Functions
 * 
 * This file contains functions related to user authentication, registration,
 * and session management for the Digital Health Record System.
 */

/**
 * Register a new user
 * 
 * @param string $username The username
 * @param string $password The password
 * @param string $email The email address
 * @param string $phone The phone number
 * @param string $role The user role (citizen, doctor, admin)
 * @return array The result of the registration
 */
function register_user($username, $password, $email, $phone, $role = 'citizen') {
    global $conn;
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($email) || empty($phone)) {
        return [
            'success' => false,
            'message' => 'All fields are required'
        ];
    }
    
    // Validate email
    if (!is_valid_email($email)) {
        return [
            'success' => false,
            'message' => 'Invalid email address'
        ];
    }
    
    // Validate phone
    if (!is_valid_phone($phone)) {
        return [
            'success' => false,
            'message' => 'Invalid phone number (must be 10 digits)'
        ];
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return [
            'success' => false,
            'message' => 'Username already exists'
        ];
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return [
            'success' => false,
            'message' => 'Email already exists'
        ];
    }
    
    // Check if phone already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return [
            'success' => false,
            'message' => 'Phone number already exists'
        ];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    
    // Generate OTP
    $otp = generate_otp();
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, role, status, otp, otp_expiry) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)");
    $stmt->bind_param("sssssss", $username, $hashed_password, $email, $phone, $role, $otp, $otp_expiry);
    
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        
        // Initialize variables
        $sms_sent = false;
        $email_sent = false;
        
        // Try to send OTP via SMS if the library exists
        if (file_exists('vendor/twilio/sdk/src/Twilio/autoload.php')) {
            $message = "Your OTP for Digital Health Record System registration is: $otp. Valid for 15 minutes.";
            $sms_sent = send_sms($phone, $message);
        }
        
        // Try to send OTP via Email if the library exists
        if (file_exists('vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
            $subject = "Verify Your Account - Digital Health Record System";
            $body = "<h2>Welcome to Digital Health Record System</h2>"
                  . "<p>Your One-Time Password (OTP) for account verification is: <strong>$otp</strong></p>"
                  . "<p>This OTP is valid for 15 minutes.</p>"
                  . "<p>If you did not request this, please ignore this email.</p>";
            $email_sent = send_email($email, $subject, $body);
        }
        
        // Skip logging for now to avoid potential issues
        // log_audit('register', 'user', $user_id, "User registered with username: $username");
        
        // Always show OTP in development environment since libraries are missing
        $otp_message = "Registration successful. For development purposes, your OTP is: $otp";
        if ($sms_sent || $email_sent) {
            $otp_message = 'Registration successful. Please verify your account with the OTP sent to your phone and email.';
        }
        
        return [
            'success' => true,
            'message' => $otp_message,
            'user_id' => $user_id
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ];
    }
}

/**
 * Verify user account with OTP
 * 
 * @param int $user_id The user ID
 * @param string $otp The OTP
 * @return array The result of the verification
 */
function verify_otp($user_id, $otp) {
    global $conn;
    
    // Validate inputs
    if (empty($user_id) || empty($otp)) {
        return [
            'success' => false,
            'message' => 'User ID and OTP are required'
        ];
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'User not found'
        ];
    }
    
    $user = $result->fetch_assoc();
    
    // Check if OTP is expired
    if (strtotime($user['otp_expiry']) < time()) {
        return [
            'success' => false,
            'message' => 'OTP has expired. Please request a new one.'
        ];
    }
    
    // Check if OTP matches
    if ($user['otp'] !== $otp) {
        return [
            'success' => false,
            'message' => 'Invalid OTP'
        ];
    }
    
    // Update user status
    $stmt = $conn->prepare("UPDATE users SET status = 'active', otp = NULL, otp_expiry = NULL WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        // Log the verification
        log_audit('verify', 'user', $user_id, "User verified account with OTP");
        
        return [
            'success' => true,
            'message' => 'Account verified successfully. You can now login.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Verification failed. Please try again.'
        ];
    }
}

/**
 * Resend OTP to user
 * 
 * @param string $username The username or email or phone
 * @return array The result of the resend
 */
function resend_otp($username) {
    global $conn;
    
    // Validate input
    if (empty($username)) {
        return [
            'success' => false,
            'message' => 'Username, email, or phone is required'
        ];
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT id, email, phone, status FROM users WHERE username = ? OR email = ? OR phone = ?");
    $stmt->bind_param("sss", $username, $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'User not found'
        ];
    }
    
    $user = $result->fetch_assoc();
    
    // Check if user is already active
    if ($user['status'] === 'active') {
        return [
            'success' => false,
            'message' => 'Account is already verified'
        ];
    }
    
    // Generate new OTP
    $otp = generate_otp();
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // Update user OTP
    $stmt = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE id = ?");
    $stmt->bind_param("ssi", $otp, $otp_expiry, $user['id']);
    
    if ($stmt->execute()) {
        // Send OTP via SMS
        $message = "Your new OTP for Digital Health Record System is: $otp. Valid for 15 minutes.";
        $sms_sent = send_sms($user['phone'], $message);
        
        // Send OTP via Email
        $subject = "New OTP - Digital Health Record System";
        $body = "<h2>Digital Health Record System</h2>"
              . "<p>Your new One-Time Password (OTP) for account verification is: <strong>$otp</strong></p>"
              . "<p>This OTP is valid for 15 minutes.</p>"
              . "<p>If you did not request this, please ignore this email.</p>";
        $email_sent = send_email($user['email'], $subject, $body);
        
        // Log the resend
        log_audit('resend_otp', 'user', $user['id'], "User requested new OTP");
        
        // For development environment, show OTP in message if SMS and email failed
        $otp_message = 'New OTP sent successfully to your phone and email.';
        if (!$sms_sent && !$email_sent) {
            $otp_message = "For development purposes, your new OTP is: $otp";
        }
        
        return [
            'success' => true,
            'message' => $otp_message,
            'user_id' => $user['id']
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to send new OTP. Please try again.'
        ];
    }
}

/**
 * Login user
 * 
 * @param string $username The username or email or phone
 * @param string $password The password
 * @return array The result of the login
 */
function login_user($username, $password) {
    global $conn;
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        return [
            'success' => false,
            'message' => 'Username and password are required'
        ];
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT id, username, password, email, phone, role, status FROM users WHERE username = ? OR email = ? OR phone = ?");
    $stmt->bind_param("sss", $username, $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'Invalid username or password'
        ];
    }
    
    $user = $result->fetch_assoc();
    
    // Check if user is active
    if ($user['status'] !== 'active') {
        return [
            'success' => false,
            'message' => 'Account is not active. Please verify your account.'
        ];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Log failed login attempt
        // Ensure user ID is available before logging
        $user_id = isset($user['id']) ? $user['id'] : 0;
        log_audit('login_failed', 'user', $user_id, "Failed login attempt for username: {$username}");
        
        return [
            'success' => false,
            'message' => 'Invalid username or password'
        ];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    $_SESSION['last_activity'] = time();
    
    // Log successful login
    log_audit('login', 'user', $user['id'], "User logged in: {$user['username']}");
    
    // Get user details based on role
    $user_details = get_user_details($user['id'], $user['role']);
    
    return [
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'details' => $user_details
        ]
    ];
}

/**
 * Get user details based on role
 * 
 * @param int $user_id The user ID
 * @param string $role The user role
 * @return array|null The user details or null if not found
 */
function get_user_details($user_id, $role) {
    global $conn;
    
    $table = '';
    
    switch ($role) {
        case 'citizen':
            $table = 'citizens';
            break;
        case 'doctor':
            $table = 'doctors';
            break;
        case 'admin':
            $table = 'administrators';
            break;
        default:
            return null;
    }
    
    $stmt = $conn->prepare("SELECT * FROM $table WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    return $result->fetch_assoc();
}

/**
 * Logout user
 * 
 * @return void
 */
function logout_user() {
    // Log logout if user is logged in
    if (isset($_SESSION['user_id'])) {
        log_audit('logout', 'user', $_SESSION['user_id'], "User logged out: {$_SESSION['username']}");
    }
    
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Check if session has expired
 * 
 * @return bool True if expired, false otherwise
 */
function is_session_expired() {
    if (!isset($_SESSION['last_activity'])) {
        return true;
    }
    
    $inactive_time = time() - $_SESSION['last_activity'];
    
    return $inactive_time > SESSION_LIFETIME;
}

/**
 * Update session activity timestamp
 * 
 * @return void
 */
function update_session_activity() {
    $_SESSION['last_activity'] = time();
}

/**
 * Request password reset
 * 
 * @param string $email The user email
 * @return array The result of the request
 */
function request_password_reset($email) {
    $conn = get_db_connection();
    
    // Validate input
    if (empty($email) || !is_valid_email($email)) {
        return [
            'success' => false,
            'message' => 'Valid email address is required'
        ];
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'Email not found'
        ];
    }
    
    $user = $result->fetch_assoc();
    
    // Generate reset token
    $token = generate_random_string(32);
    $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Update user with reset token
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
    $stmt->bind_param("ssi", $token, $token_expiry, $user['id']);
    
    if ($stmt->execute()) {
        // Send reset email
        $reset_link = APP_URL . "/index.php?controller=auth&action=reset_password&token=$token";
        
        $subject = "Password Reset - Digital Health Record System";
        $body = "<h2>Password Reset Request</h2>"
              . "<p>Hello {$user['username']},</p>"
              . "<p>We received a request to reset your password. Click the link below to reset your password:</p>"
              . "<p><a href='$reset_link'>Reset Password</a></p>"
              . "<p>This link is valid for 1 hour.</p>"
              . "<p>If you did not request this, please ignore this email.</p>";
        
        send_email($email, $subject, $body);
        
        // Log the request
        log_audit('password_reset_request', 'user', $user['id'], "Password reset requested for user: {$user['username']}");
        
        return [
            'success' => true,
            'message' => 'Password reset instructions sent to your email.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to process password reset request. Please try again.'
        ];
    }
}

/**
 * Reset password
 * 
 * @param string $token The reset token
 * @param string $password The new password
 * @param string $confirm_password The confirm password
 * @return array The result of the reset
 */
function reset_password($token, $password, $confirm_password) {
    $conn = get_db_connection();
    
    // Validate inputs
    if (empty($token) || empty($password) || empty($confirm_password)) {
        return [
            'success' => false,
            'message' => 'All fields are required'
        ];
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        return [
            'success' => false,
            'message' => 'Passwords do not match'
        ];
    }
    
    // Get user by token
    $stmt = $conn->prepare("SELECT id, username, reset_token_expiry FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'Invalid or expired token'
        ];
    }
    
    $user = $result->fetch_assoc();
    
    // Check if token is expired
    if (strtotime($user['reset_token_expiry']) < time()) {
        return [
            'success' => false,
            'message' => 'Reset token has expired. Please request a new one.'
        ];
    }
    
    // Hash new password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    
    // Update user password and clear token
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user['id']);
    
    if ($stmt->execute()) {
        // Log the password reset
        log_audit('password_reset', 'user', $user['id'], "Password reset completed for user: {$user['username']}");
        
        return [
            'success' => true,
            'message' => 'Password reset successful. You can now login with your new password.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to reset password. Please try again.'
        ];
    }
}

/**
 * Change password
 * 
 * @param int $user_id The user ID
 * @param string $current_password The current password
 * @param string $new_password The new password
 * @param string $confirm_password The confirm password
 * @return array The result of the change
 */
function change_password($user_id, $current_password, $new_password, $confirm_password) {
    global $conn;
    
    // Validate inputs
    if (empty($user_id) || empty($current_password) || empty($new_password) || empty($confirm_password)) {
        return [
            'success' => false,
            'message' => 'All fields are required'
        ];
    }
    
    // Check if passwords match
    if ($new_password !== $confirm_password) {
        return [
            'success' => false,
            'message' => 'New passwords do not match'
        ];
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT password, username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'User not found'
        ];
    }
    
    $user = $result->fetch_assoc();
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        return [
            'success' => false,
            'message' => 'Current password is incorrect'
        ];
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    
    // Update user password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        // Log the password change
        log_audit('password_change', 'user', $user_id, "Password changed for user: {$user['username']}");
        
        return [
            'success' => true,
            'message' => 'Password changed successfully'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to change password. Please try again.'
        ];
    }
}

/**
 * Update user profile
 * 
 * @param int $user_id The user ID
 * @param array $data The profile data
 * @return array The result of the update
 */
function update_user_profile($user_id, $data) {
    global $conn;
    
    // Validate user ID
    if (empty($user_id)) {
        return [
            'success' => false,
            'message' => 'User ID is required'
        ];
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'User not found'
        ];
    }
    
    $user = $result->fetch_assoc();
    $role = $user['role'];
    
    // Determine table and fields based on role
    $table = '';
    $fields = [];
    $types = '';
    $values = [];
    
    switch ($role) {
        case 'citizen':
            $table = 'citizens';
            
            // Define allowed fields and their types
            $allowed_fields = [
                'first_name' => 's',
                'last_name' => 's',
                'date_of_birth' => 's',
                'gender' => 's',
                'blood_group' => 's',
                'address' => 's',
                'city' => 's',
                'state' => 's',
                'pincode' => 's',
                'emergency_contact_name' => 's',
                'emergency_contact_phone' => 's',
                'profile_picture' => 's'
            ];
            break;
            
        case 'doctor':
            $table = 'doctors';
            
            // Define allowed fields and their types
            $allowed_fields = [
                'first_name' => 's',
                'last_name' => 's',
                'specialization' => 's',
                'qualification' => 's',
                'experience_years' => 'i',
                'clinic_address' => 's',
                'consultation_fee' => 'd',
                'available_days' => 's',
                'available_time_start' => 's',
                'available_time_end' => 's',
                'profile_picture' => 's',
                'bio' => 's'
            ];
            break;
            
        case 'admin':
            $table = 'administrators';
            
            // Define allowed fields and their types
            $allowed_fields = [
                'first_name' => 's',
                'last_name' => 's',
                'department' => 's',
                'position' => 's',
                'profile_picture' => 's'
            ];
            break;
            
        default:
            return [
                'success' => false,
                'message' => 'Invalid user role'
            ];
    }
    
    // Build update query
    $set_clause = [];
    
    foreach ($data as $key => $value) {
        if (array_key_exists($key, $allowed_fields)) {
            $set_clause[] = "$key = ?";
            $types .= $allowed_fields[$key];
            $values[] = $value;
        }
    }
    
    if (empty($set_clause)) {
        return [
            'success' => false,
            'message' => 'No valid fields to update'
        ];
    }
    
    // Add user_id to values and types
    $types .= 'i';
    $values[] = $user_id;
    
    // Prepare and execute update query
    $sql = "UPDATE $table SET " . implode(', ', $set_clause) . " WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters dynamically
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        // Log the profile update
        log_audit('profile_update', 'user', $user_id, "Profile updated for user: {$user['username']}");
        
        return [
            'success' => true,
            'message' => 'Profile updated successfully'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to update profile. Please try again.'
        ];
    }
}

/**
 * Check if user exists
 * 
 * @param string $field The field to check (username, email, phone)
 * @param string $value The value to check
 * @return bool True if exists, false otherwise
 */
function user_exists($field, $value) {
    global $conn;
    
    $allowed_fields = ['username', 'email', 'phone'];
    
    if (!in_array($field, $allowed_fields)) {
        return false;
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE $field = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

/**
 * Create citizen profile
 * 
 * @param int $user_id The user ID
 * @param string $first_name The first name
 * @param string $last_name The last name
 * @return array The result of the creation
 */
function create_citizen_profile($user_id, $first_name, $last_name) {
    global $conn;
    
    try {
        // Generate health ID
        $health_id = 'CIT' . date('Y') . str_pad($user_id, 6, '0', STR_PAD_LEFT);
        
        // Match updated_schema.sql columns (postal_code instead of pincode)
        $stmt = $conn->prepare(
            "INSERT INTO citizens (
                user_id, first_name, last_name, date_of_birth, gender, health_id,
                address, city, state, postal_code
            ) VALUES (
                ?, ?, ?, '1900-01-01', 'other', ?,
                'Address not provided', 'City not provided', 'State not provided', '000000'
            )"
        );
        $stmt->bind_param("isss", $user_id, $first_name, $last_name, $health_id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Citizen profile created successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create citizen profile'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error creating citizen profile: ' . $e->getMessage()
        ];
    }
}

/**
 * Create doctor profile
 * 
 * @param int $user_id The user ID
 * @param string $first_name The first name
 * @param string $last_name The last name
 * @param string $specialization The specialization
 * @param string $qualification The qualification
 * @param string $license_number The license number
 * @param int $experience_years The years of experience
 * @return array The result of the creation
 */
function create_doctor_profile($user_id, $first_name, $last_name, $specialization, $qualification, $license_number, $experience_years) {
    global $conn;
    
    try {
        // Match updated_schema.sql required columns
        $stmt = $conn->prepare(
            "INSERT INTO doctors (
                user_id, first_name, last_name, date_of_birth, gender,
                specialization, license_number, qualification, experience_years,
                bio, consultation_fee, address, city, state, postal_code, verification_status
            ) VALUES (
                ?, ?, ?, '1900-01-01', 'other',
                ?, ?, ?, ?,
                NULL, NULL, 'Address not provided', 'City not provided', 'State not provided', '000000', 'pending'
            )"
        );
        $stmt->bind_param("isssssi", $user_id, $first_name, $last_name, $specialization, $license_number, $qualification, $experience_years);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Doctor profile created successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create doctor profile'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error creating doctor profile: ' . $e->getMessage()
        ];
    }
}

/**
 * Get user by ID
 * 
 * @param int $user_id The user ID
 * @return array|null The user data or null if not found
 */
function get_user_by_id($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, username, email, phone, role, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get user by username, email, or phone
 * 
 * @param string $identifier The username, email, or phone
 * @return array|null The user data or null if not found
 */
function get_user_by_identifier($identifier) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, username, email, phone, role, status, created_at FROM users WHERE username = ? OR email = ? OR phone = ?");
    $stmt->bind_param("sss", $identifier, $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    $user = $result->fetch_assoc();
    $user['details'] = get_user_details($user['id'], $user['role']);
    
    return $user;
}