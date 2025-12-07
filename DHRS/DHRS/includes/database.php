<?php
/**
 * Database Connection Function
 * 
 * This file contains the function to establish a database connection
 */

/**
 * Get database connection
 * 
 * @return mysqli The database connection
 */
function get_db_connection() {
    global $conn;
    
    // If connection is not established or closed, create a new one
    if (!isset($conn) || $conn->connect_error) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            die("Database connection error. Please try again later or contact support.");
        }
        
        // Set charset to utf8mb4
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}