<?php
require_once 'config.php';

/**
 * Check if user is logged in and session is valid
 */
function isLoggedIn() {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['expires_at']) && time() > $_SESSION['expires_at']) {
        logout();
        return false;
    }
    
    // Verify session in database
    try {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT us.id, u.is_active 
            FROM user_sessions us
            JOIN users u ON us.user_id = u.id
            WHERE us.session_id = ? AND us.is_active = TRUE AND us.expires_at > NOW()
        ");
        $stmt->execute([session_id()]);
        $session = $stmt->fetch();
        
        if (!$session || !$session['is_active']) {
            logout();
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Session validation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get current user information
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name, email, phone, created_at, last_login
            FROM users 
            WHERE id = ? AND is_active = TRUE
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Get current user error: " . $e->getMessage());
        return null;
    }
}

/**
 * Logout user and clean up session
 */
function logout() {
    try {
        // Deactivate session in database
        if (isset($_SESSION['user_id'])) {
            global $pdo;
            $stmt = $pdo->prepare("
                UPDATE user_sessions 
                SET is_active = FALSE 
                WHERE session_id = ?
            ");
            $stmt->execute([session_id()]);
        }
    } catch (Exception $e) {
        error_log("Logout database error: " . $e->getMessage());
    }
    
    // Clear session data
    $_SESSION = [];
    
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Require user to be logged in, redirect to login if not
 */
function requireLogin($redirectUrl = '/rhms/login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirectUrl);
        exit;
    }
}

/**
 * Clean up expired sessions (should be called periodically)
 */
function cleanupExpiredSessions() {
    try {
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE user_sessions 
            SET is_active = FALSE 
            WHERE expires_at < NOW() OR is_active = TRUE
        ");
        $stmt->execute();
        
        // Also clean up old password reset tokens
        $stmt = $pdo->prepare("
            DELETE FROM password_reset_tokens 
            WHERE expires_at < NOW() OR used = TRUE
        ");
        $stmt->execute();
        
        return true;
    } catch (Exception $e) {
        error_log("Session cleanup error: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get user's display name
 */
function getUserDisplayName() {
    return $_SESSION['user_name'] ?? 'Guest';
}

/**
 * Check if user has been inactive for too long
 */
function checkInactivity($maxInactiveTime = 1800) { // 30 minutes default
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $maxInactiveTime) {
            logout();
            return false;
        }
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Auto-cleanup expired sessions (1% chance on each request)
if (mt_rand(1, 100) === 1) {
    cleanupExpiredSessions();
}
?>