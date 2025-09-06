<?php
// Database configuration for RHMS (Restaurant Hotel Management System)
// This file contains database connection settings for MySQL

// Database connection parameters
define('DB_HOST', 'localhost');        // Database host (XAMPP default)
define('DB_USERNAME', 'root');         // Database username (XAMPP default)
define('DB_PASSWORD', '');             // Database password (XAMPP default - empty)
define('DB_NAME', 'rhms_db');          // Database name
define('DB_CHARSET', 'utf8mb4');       // Character set

// Additional database settings
define('DB_PORT', 3306);               // MySQL port (default)

// Error reporting settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Timezone setting
date_default_timezone_set('UTC');

?>