<?php
/**
 * Digital Health Record System (DHRS)
 * 
 * Main entry point for the application
 * 
 * This file handles routing and initializes the application
 */

// Start session with strict settings
// Note: These settings must be set before session_start()
ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Use session_start with options array (PHP 7.0+)
    session_start([
        'cookie_lifetime' => defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 1800, // Default to 30 minutes
        'cookie_httponly' => 1,
        'use_strict_mode' => 1
    ]);
}

// Session is now started

// Load configuration
require_once 'config/config.php';

// Load helper functions
require_once 'includes/functions.php';

// Load database connection function
require_once 'includes/database.php';

// Load authentication functions
require_once 'includes/auth.php';

// Default controller and action
$controller = isset($_GET['controller']) ? sanitize_input($_GET['controller']) : 'home';
$action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';

// Define allowed controllers and their actions
$allowed_routes = [
    'home' => ['index', 'about', 'contact', 'services', 'faq'],
    'auth' => ['login', 'register', 'logout', 'forgot_password', 'reset_password', 'verify_otp', 'resend_otp'],
    'citizen' => ['dashboard', 'profile', 'health_profile', 'update_profile', 'update_health_profile'],
    'doctor' => ['dashboard', 'profile', 'my_patients', 'my_schedule', 'patient_details', 'update_profile', 'update_schedule'],
    'admin' => ['dashboard', 'manage_users', 'statistics', 'settings', 'update_setting', 'activate_user', 'deactivate_user', 'delete_user', 'export_users'],
    'appointment' => ['book', 'view', 'cancel', 'complete', 'process_booking', 'confirm', 'doctor_appointments'],
    'prescription' => ['create', 'view', 'download', 'list', 'process_create', 'my_prescriptions'],
    'medical_record' => ['create', 'view', 'update', 'delete', 'download', 'upload', 'my_records'],
    'lab_report' => ['create', 'view', 'download', 'list', 'my_reports'],
    'vaccination' => ['record', 'view', 'list', 'certificate', 'my_vaccinations'],
    'notification' => ['list', 'mark_read', 'settings'],
    'api' => ['get_doctors', 'get_slots', 'check_username', 'send_otp']
];

// Validate route
if (!array_key_exists($controller, $allowed_routes) || !in_array($action, $allowed_routes[$controller])) {
    // Invalid route, redirect to 404 page
    header("HTTP/1.0 404 Not Found");
    include('views/errors/404.php');
    exit();
}

// Check if controller file exists
$controller_file = 'controllers/' . $controller . '_controller.php';
if (!file_exists($controller_file)) {
    // Controller file not found, redirect to 404 page
    header("HTTP/1.0 404 Not Found");
    include('views/errors/404.php');
    exit();
}

// Include controller file
require_once $controller_file;

// Create controller class name
$controller_class = str_replace('_', '', ucwords($controller, '_')) . 'Controller';

// Check if controller class exists
if (!class_exists($controller_class)) {
    // Controller class not found, redirect to 404 page
    header("HTTP/1.0 404 Not Found");
    include('views/errors/404.php');
    exit();
}

// Create controller instance
$controller_instance = new $controller_class();

// Check if action method exists
if (!method_exists($controller_instance, $action)) {
    // Action method not found, redirect to 404 page
    header("HTTP/1.0 404 Not Found");
    include('views/errors/404.php');
    exit();
}

// Call action method
$controller_instance->$action();