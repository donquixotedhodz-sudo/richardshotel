<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'rhms_db');

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    
    // Check if it's a database not found error
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<h2 style='color: #d9534f;'>Database Not Found</h2>";
        echo "<p>The database '<strong>" . DB_NAME . "</strong>' does not exist.</p>";
        echo "<p>Please run the database installation script first:</p>";
        echo "<p><a href='../database/create_db.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Create Database</a></p>";
        echo "</div>";
        exit;
    }
    
    die("Database connection failed. Please check your configuration. Error: " . $e->getMessage());
}

// Security settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>