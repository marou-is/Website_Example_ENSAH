<?php
/**
 * Configuration file for database connection
 * This file contains all database credentials
 */

// Database configuration
define('DB_HOST', 'localhost:8889');
define('DB_USER', 'root');  // Default MAMP/XAMPP username
define('DB_PASS', 'root');  // Default MAMP password (change if different)
define('DB_NAME', 'projet_insc');
define('DB_CHARSET', 'utf8mb4');

/**
 * Create database connection
 * @return mysqli|false Returns mysqli connection or false on failure
 */
function getDBConnection() {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset(DB_CHARSET);
    
    return $conn;
}

/**
 * Close database connection
 * @param mysqli $conn Database connection to close
 */
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>