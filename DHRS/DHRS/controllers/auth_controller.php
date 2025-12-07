<?php
/**
 * Authentication Controller
 * 
 * This controller handles user authentication, registration, login, logout,
 * password reset, and OTP verification.
 */

class AuthController {
    /**
     * Display the login page
     */
    public function login() {
        // Check if user is already logged in
        if (is_logged_in()) {
            // Redirect to appropriate dashboard based on role
            switch ($_SESSION['role']) {
                case 'citizen':
                    redirect('index.php?controller=citizen&action=dashboard');
                    break;
                case 'doctor':
                    redirect('index.php?controller=doctor&action=dashboard');
                    break;
                case 'admin':
                    redirect('index.php?controller=admin&action=dashboard');
                    break;
                default:
                    redirect('index.php');
                    break;
            }
        }
        
        $error = '';
        
        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : ''; // Don't sanitize password
            
            // Validate form data
            if (empty($username) || empty($password)) {
                $error = 'Username and password are required';
            } else {
                // Attempt to login
                $result = login_user($username, $password);
                
                if ($result['success']) {
                    // Set flash message
                    set_flash_message('success', 'Login successful. Welcome back!');
                    
                    // Redirect to appropriate dashboard based on role
                    switch ($result['user']['role']) {
                        case 'citizen':
                            redirect('index.php?controller=citizen&action=dashboard');
                            break;
                        case 'doctor':
                            redirect('index.php?controller=doctor&action=dashboard');
                            break;
                        case 'admin':
                            redirect('index.php?controller=admin&action=dashboard');
                            break;
                        default:
                            redirect('index.php');
                            break;
                    }
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Include the login page view
        include('views/auth/login.php');
    }
    
    /**
     * Display the registration page
     */
    public function register() {
        // Check if user is already logged in
        if (is_logged_in()) {
            redirect('index.php');
        }
        
        $error = '';
        $success = '';
        $user_id = 0;
        
        // Handle registration form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $first_name = isset($_POST['first_name']) ? sanitize_input($_POST['first_name']) : '';
            $last_name = isset($_POST['last_name']) ? sanitize_input($_POST['last_name']) : '';
            $username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
            $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : ''; // Don't sanitize password
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; // Don't sanitize password
            $role = isset($_POST['role']) ? sanitize_input($_POST['role']) : '';
            
            // Doctor-specific fields
            $specialization = isset($_POST['specialization']) ? sanitize_input($_POST['specialization']) : '';
            $license_number = isset($_POST['license_number']) ? sanitize_input($_POST['license_number']) : '';
            $qualification = isset($_POST['qualification']) ? sanitize_input($_POST['qualification']) : '';
            $experience_years = isset($_POST['experience_years']) ? intval($_POST['experience_years']) : 0;
            
            // Validate form data
            if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirm_password) || empty($role)) {
                $error = 'All fields are required';
            } elseif (!is_valid_email($email)) {
                $error = 'Invalid email address';
            } elseif (!is_valid_phone($phone)) {
                $error = 'Invalid phone number (must be 10 digits)';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters long';
            } elseif ($role === 'doctor' && (empty($specialization) || empty($license_number) || empty($qualification) || $experience_years <= 0)) {
                $error = 'All doctor-specific fields are required';
            } else {
                // Attempt to register
                $result = register_user($username, $password, $email, $phone, $role);
                
                if ($result['success']) {
                    $user_id = $result['user_id'];
                    
                    // Create user details based on role
                    if ($role === 'citizen') {
                        $citizen_result = create_citizen_profile($user_id, $first_name, $last_name);
                        if (!$citizen_result['success']) {
                            $error = 'Failed to create citizen profile: ' . $citizen_result['message'];
                        }
                    } elseif ($role === 'doctor') {
                        $doctor_result = create_doctor_profile($user_id, $first_name, $last_name, $specialization, $qualification, $license_number, $experience_years);
                        if (!$doctor_result['success']) {
                            $error = 'Failed to create doctor profile: ' . $doctor_result['message'];
                        }
                    }
                    
                    if (empty($error)) {
                        // Set flash message with development link
                        $dev_message = $result['message'] . ' <a href="dev_messages.php" target="_blank" class="alert-link">View OTP in Development Messages</a>';
                        set_flash_message('success', $dev_message);
                        
                        // Debug output
                        error_log("Registration successful. User ID: $user_id");
                        error_log("Flash message set: " . $result['message']);
                        
                        // Ensure session data is written before redirect
                        session_write_close();
                        
                        // Redirect to OTP verification page
                        redirect('index.php?controller=auth&action=verify_otp&user_id=' . $user_id);
                    }
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Include the registration page view
        include('views/auth/register.php');
    }
    
    /**
     * Handle OTP verification
     */
    public function verify_otp() {
        $error = '';
        $success = '';
        
        // Get user ID from query string
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        // If user_id is not provided, redirect to login page
        if ($user_id === 0) {
            set_flash_message('error', 'Invalid verification request. Please try registering again.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // If arriving via GET after registration, show a friendly success note even if flash is lost
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (!isset($_GET['resend']) || $_GET['resend'] !== 'true')) {
            $success = 'Registration successful. Please enter the OTP sent to your phone to verify your account.';
        }

        // Handle OTP form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $otp = isset($_POST['otp']) ? sanitize_input($_POST['otp']) : '';
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            
            // Validate form data
            if (empty($otp) || empty($user_id)) {
                $error = 'OTP is required';
            } else {
                // Attempt to verify OTP
                $result = verify_otp($user_id, $otp);
                
                if ($result['success']) {
                    // Set flash message
                    set_flash_message('success', $result['message']);
                    
                    // Debug output
                    error_log("OTP verification successful. User ID: $user_id");
                    error_log("Flash message set: " . $result['message']);
                    
                    // Ensure session data is written before redirect
                    session_write_close();
                    
                    // Redirect to login page
                    redirect('index.php?controller=auth&action=login');
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Handle resend OTP
        if (isset($_GET['resend']) && $_GET['resend'] === 'true') {
            // Get user by ID
            $user = get_user_by_id($user_id);
            
            // If AJAX request, return JSON
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            if ($user) {
                $result = resend_otp($user['username']);
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    exit();
                }
                if ($result['success']) {
                    $success = $result['message'];
                    $user_id = $result['user_id'];
                } else {
                    $error = $result['message'];
                }
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                    exit();
                }
                $error = 'User not found';
            }
        }
        
        // Include the OTP verification page view
        include('views/auth/verify_otp.php');
    }

    /**
     * Resend OTP endpoint (supports AJAX)
     */
    public function resend_otp() {
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        if ($user_id === 0) {
            $response = ['success' => false, 'message' => 'Invalid user'];
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
            set_flash_message('danger', $response['message']);
            redirect('index.php?controller=auth&action=verify_otp');
        }
        
        $user = get_user_by_id($user_id);
        if (!$user) {
            $response = ['success' => false, 'message' => 'User not found'];
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
            set_flash_message('danger', $response['message']);
            redirect('index.php?controller=auth&action=verify_otp&user_id=' . $user_id);
        }
        
        $result = resend_otp($user['username']);
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
        
        if ($result['success']) {
            set_flash_message('success', $result['message']);
        } else {
            set_flash_message('danger', $result['message']);
        }
        redirect('index.php?controller=auth&action=verify_otp&user_id=' . $user_id);
    }
    
    /**
     * Handle user logout
     */
    public function logout() {
        // Logout user
        logout_user();
        
        // Set flash message
        set_flash_message('success', 'You have been logged out successfully');
        
        // Redirect to home page
        redirect('index.php');
    }
    
    /**
     * Display the forgot password page
     */
    public function forgot_password() {
        // Check if user is already logged in
        if (is_logged_in()) {
            redirect('index.php');
        }
        
        $error = '';
        $success = '';
        
        // Handle forgot password form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
            
            // Validate form data
            if (empty($email) || !is_valid_email($email)) {
                $error = 'Valid email address is required';
            } else {
                // Attempt to send password reset email
                $result = request_password_reset($email);
                
                if ($result['success']) {
                    $success = $result['message'] . ' <a href="dev_messages.php" target="_blank" class="alert-link">View Reset Link in Development Messages</a>';
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Include the forgot password page view
        include('views/auth/forgot_password.php');
    }
    
    /**
     * Display the reset password page
     */
    public function reset_password() {
        // Check if user is already logged in
        if (is_logged_in()) {
            redirect('index.php');
        }
        
        $error = '';
        $success = '';
        
        // Get token from query string
        $token = isset($_GET['token']) ? sanitize_input($_GET['token']) : '';
        
        if (empty($token)) {
            // Set flash message
            set_flash_message('error', 'Invalid or missing reset token');
            
            // Redirect to forgot password page
            redirect('index.php?controller=auth&action=forgot_password');
        }
        
        // Handle reset password form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $token = isset($_POST['token']) ? sanitize_input($_POST['token']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : ''; // Don't sanitize password
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; // Don't sanitize password
            
            // Validate form data
            if (empty($token) || empty($password) || empty($confirm_password)) {
                $error = 'All fields are required';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters long';
            } else {
                // Attempt to reset password
                $result = reset_password($token, $password, $confirm_password);
                
                if ($result['success']) {
                    // Set flash message
                    set_flash_message('success', $result['message']);
                    
                    // Redirect to login page
                    redirect('index.php?controller=auth&action=login');
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Include the reset password page view
        include('views/auth/reset_password.php');
    }
}