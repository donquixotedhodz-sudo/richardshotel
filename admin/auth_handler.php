<?php
// Simple Admin Authentication Handler
session_start();

// Simple hardcoded admin credentials (you can modify these)
$admin_username = 'admin';
$admin_password = 'admin123';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login' || $action === 'admin_login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validate credentials
        if (empty($username) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter both username and password.'
            ]);
            exit;
        }
        
        if ($username === $admin_username && $password === $admin_password) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_login_time'] = time();
            
            // Set remember me cookie if checked
            if ($remember) {
                $cookie_value = base64_encode($username . '|' . time());
                setcookie('admin_remember', $cookie_value, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful! Redirecting to dashboard...'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid username or password.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action.'
        ]);
    }
} else {
    // Redirect to login page if not POST request
    header('Location: index.php');
    exit;
}
?>