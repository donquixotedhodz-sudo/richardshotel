<?php
// Simple script to create the database first
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'rhms_db');

try {
    // Connect to MySQL without selecting a database
    $pdo_create = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Create the database
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo_create->exec($sql);
    
    echo "<h2>Database Creation</h2>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px;'>";
    echo "<p style='color: green;'>✓ Database '" . DB_NAME . "' created successfully!</p>";
    echo "<p><a href='install.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Continue with Table Installation</a></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<h2>Database Creation Error</h2>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px;'>";
    echo "<p style='color: red;'>✗ Error creating database: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure MySQL is running and the credentials in config.php are correct.</p>";
    echo "</div>";
}
?>