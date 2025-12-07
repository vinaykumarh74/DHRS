<?php
/**
 * Database Configuration File
 * 
 * This file contains the database connection parameters and other
 * configuration settings for the Digital Health Record System.
 */

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change in production
define('DB_PASS', ''); // Change in production
define('DB_NAME', 'dhrs_db');

// Application settings
define('APP_NAME', 'Digital Health Record System');
define('APP_URL', 'http://localhost/DHRS');

// Session settings
define('SESSION_LIFETIME', 1800); // 30 minutes

// Security settings
define('HASH_COST', 10); // For password hashing

// File upload settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,pdf,doc,docx');
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');

// Email settings (for PHPMailer)
define('MAIL_HOST', 'smtp.example.com'); // Change in production
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'noreply@example.com'); // Change in production
define('MAIL_PASSWORD', 'your-password'); // Change in production
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'noreply@example.com');
define('MAIL_FROM_NAME', APP_NAME);

// SMS settings (for Twilio)
define('TWILIO_SID', 'your-twilio-sid'); // Change in production
define('TWILIO_TOKEN', 'your-twilio-token'); // Change in production
define('TWILIO_FROM', '+1234567890'); // Change in production

// Error reporting settings
ini_set('display_errors', 1); // Set to 1 during development
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/logs/error.log');

// Timezone settings
date_default_timezone_set('Asia/Kolkata'); // Set to Ballari's timezone

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // Log error and display friendly message
    error_log($e->getMessage());
    die("Database connection error. Please try again later or contact support.");
}