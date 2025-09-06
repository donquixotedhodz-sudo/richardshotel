<?php
/**
 * Authentication Handler for RHMS
 * Processes signup and login requests
 */

session_start();
require_once '../database/Database.php';

class AuthHandler {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new Database();
            // Initialize tables if they don't exist
            $this->db->initializeTables();
        } catch (Exception $e) {
            $this->sendResponse(false, 'Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle signup request
     */
    public function handleSignup() {
        try {
            // Validate input
            $firstName = $this->sanitizeInput($_POST['firstName'] ?? '');
            $lastName = $this->sanitizeInput($_POST['lastName'] ?? '');
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $phone = $this->sanitizeInput($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            
            // Validation
            if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
                $this->sendResponse(false, 'All fields are required.');
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(false, 'Invalid email format.');
                return;
            }
            
            if (strlen($password) < 6) {
                $this->sendResponse(false, 'Password must be at least 6 characters long.');
                return;
            }
            
            if ($password !== $confirmPassword) {
                $this->sendResponse(false, 'Passwords do not match.');
                return;
            }
            
            // Check if email already exists
            $existingUser = $this->db->fetchOne(
                "SELECT id FROM users WHERE email = ?", 
                [$email]
            );
            
            if ($existingUser) {
                $this->sendResponse(false, 'Email address is already registered.');
                return;
            }
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $userId = $this->db->insert(
                "INSERT INTO users (first_name, last_name, email, phone, password_hash, is_verified, status) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$firstName, $lastName, $email, $phone, $passwordHash, true, 'active']
            );
            
            if ($userId) {
                // Log the user in
                $this->createUserSession($userId, $email);
                $this->sendResponse(true, 'Account created successfully! Welcome to Richard\'s Hotel.');
            } else {
                $this->sendResponse(false, 'Failed to create account. Please try again.');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'An error occurred during signup: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle login request
     */
    public function handleLogin() {
        try {
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $rememberMe = isset($_POST['rememberMe']);
            
            // Validation
            if (empty($email) || empty($password)) {
                $this->sendResponse(false, 'Email and password are required.');
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(false, 'Invalid email format.');
                return;
            }
            
            // Check rate limiting
            if ($this->isRateLimited($email)) {
                $this->sendResponse(false, 'Too many login attempts. Please try again later.');
                return;
            }
            
            // Get user from database
            $user = $this->db->fetchOne(
                "SELECT id, first_name, last_name, email, password_hash, status FROM users WHERE email = ?",
                [$email]
            );
            
            // Log login attempt
            $this->logLoginAttempt($email, $user !== false);
            
            if (!$user) {
                $this->sendResponse(false, 'Invalid email or password.');
                return;
            }
            
            if ($user['status'] !== 'active') {
                $this->sendResponse(false, 'Account is not active. Please contact support.');
                return;
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                $this->sendResponse(false, 'Invalid email or password.');
                return;
            }
            
            // Update last login
            $this->db->update(
                "UPDATE users SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            // Create session
            $this->createUserSession($user['id'], $user['email'], $rememberMe);
            
            $this->sendResponse(true, 'Login successful! Welcome back, ' . $user['first_name'] . '.');
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'An error occurred during login: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle logout request
     */
    public function handleLogout() {
        try {
            // Destroy session from database if exists
            if (isset($_SESSION['session_token'])) {
                $this->db->delete(
                    "DELETE FROM user_sessions WHERE session_token = ?",
                    [$_SESSION['session_token']]
                );
            }
            
            // Destroy PHP session
            session_destroy();
            
            $this->sendResponse(true, 'Logged out successfully.');
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'An error occurred during logout.');
        }
    }
    
    /**
     * Create user session
     */
    private function createUserSession($userId, $email, $rememberMe = false) {
        // Generate session token
        $sessionToken = bin2hex(random_bytes(32));
        
        // Set session expiry
        $expiryTime = $rememberMe ? (time() + (30 * 24 * 60 * 60)) : (time() + (24 * 60 * 60)); // 30 days or 1 day
        
        // Store in database
        $this->db->insert(
            "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)",
            [
                $userId,
                $sessionToken,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                date('Y-m-d H:i:s', $expiryTime)
            ]
        );
        
        // Set PHP session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;
        $_SESSION['session_token'] = $sessionToken;
        $_SESSION['logged_in'] = true;
        
        // Set cookie if remember me is checked
        if ($rememberMe) {
            setcookie('remember_token', $sessionToken, $expiryTime, '/', '', false, true);
        }
    }
    
    /**
     * Check if IP/email is rate limited
     */
    private function isRateLimited($email) {
        $attempts = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM login_attempts WHERE (email = ? OR ip_address = ?) AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE) AND success = FALSE",
            [$email, $_SERVER['REMOTE_ADDR'] ?? 'unknown']
        );
        
        return $attempts['count'] >= 5; // Max 5 failed attempts in 15 minutes
    }
    
    /**
     * Log login attempt
     */
    private function logLoginAttempt($email, $success) {
        $this->db->insert(
            "INSERT INTO login_attempts (email, ip_address, success, user_agent) VALUES (?, ?, ?, ?)",
            [
                $email,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $success ? 1 : 0,
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]
        );
    }
    
    /**
     * Sanitize input
     */
    private function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Send JSON response
     */
    private function sendResponse($success, $message, $data = null) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new AuthHandler();
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'signup':
            $handler->handleSignup();
            break;
        case 'login':
            $handler->handleLogin();
            break;
        case 'logout':
            $handler->handleLogout();
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>