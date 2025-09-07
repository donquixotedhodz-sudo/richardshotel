<?php
// Database installation script
// Run this file once to set up the database

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'rhms_db';

// Read SQL file
$sqlFile = __DIR__ . '/setup.sql';
if (!file_exists($sqlFile)) {
    die('Error: setup.sql file not found!');
}

$sql = file_get_contents($sqlFile);

try {
    // Connect to MySQL server (without selecting database first)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Execute SQL statements
    // Remove comments and split by semicolon, but handle multi-line statements properly
    $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
    $statements = preg_split('/;\s*$/m', $sql, -1, PREG_SPLIT_NO_EMPTY);
    
    echo "<h2>Database Installation</h2>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px;'>";
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                // Get the first line or first 50 characters for display
                $displayText = strlen($statement) > 50 ? substr($statement, 0, 50) . '...' : $statement;
                $displayText = preg_replace('/\s+/', ' ', $displayText); // Normalize whitespace
                echo "<p style='color: green;'>✓ Executed: " . htmlspecialchars($displayText) . "</p>";
            } catch (PDOException $e) {
                $displayText = strlen($statement) > 50 ? substr($statement, 0, 50) . '...' : $statement;
                $displayText = preg_replace('/\s+/', ' ', $displayText); // Normalize whitespace
                echo "<p style='color: red;'>✗ Error executing: " . htmlspecialchars($displayText) . "<br>" . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
    
    echo "<h3 style='color: green;'>Database installation completed!</h3>";
    echo "<p><strong>Default Admin Account:</strong></p>";
    echo "<ul>";
    echo "<li>Email: admin@richardshotel.com</li>";
    echo "<li>Password: admin123</li>";
    echo "</ul>";
    echo "<p style='color: red;'><strong>Important:</strong> Please change the admin password after first login!</p>";
    echo "<p><a href='../includes/login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    echo "<p><a href='../index.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Home Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Database Installation Failed</h2>";
    echo "<p style='color: red; font-family: Arial, sans-serif;'>Error: " . $e->getMessage() . "</p>";
    echo "<p style='font-family: Arial, sans-serif;'>Please make sure:</p>";
    echo "<ul style='font-family: Arial, sans-serif;'>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Database credentials are correct</li>";
    echo "</ul>";
}

echo "</div>";
?>