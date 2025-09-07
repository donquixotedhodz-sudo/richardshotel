<?php
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get the action from POST data
$action = $_POST['action'] ?? '';

try {
    if ($action === 'register') {
        // Get and validate input data
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($firstName)) {
            $errors[] = 'First name is required';
        } elseif (strlen($firstName) < 2 || strlen($firstName) > 50) {
            $errors[] = 'First name must be between 2 and 50 characters';
        }
        
        if (empty($lastName)) {
            $errors[] = 'Last name is required';
        } elseif (strlen($lastName) < 2 || strlen($lastName) > 50) {
            $errors[] = 'Last name must be between 2 and 50 characters';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        }
        
        if (empty($phone)) {
            $errors[] = 'Phone number is required';
        } elseif (!preg_match('/^[+]?[0-9\s\-\(\)]{10,20}$/', $phone)) {
            $errors[] = 'Please enter a valid phone number';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            throw new Exception(implode('. ', $errors));
        }
        
        // Check if email already exists
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            throw new Exception('An account with this email already exists');
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password_hash) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([$firstName, $lastName, $email, $phone, $passwordHash]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Account created successfully!'
            ]);
        } else {
            throw new Exception('Failed to create account. Please try again.');
        }
        
    } elseif ($action === 'login') {
        // Get and validate input data
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['rememberMe']) && $_POST['rememberMe'] === 'true';
        $role = $_POST['role'] ?? 'customer';
        
        // Validation
        if (empty($email)) {
            throw new Exception($role === 'admin' ? 'Username is required' : 'Email is required');
        }
        
        if (empty($password)) {
            throw new Exception('Password is required');
        }
        
        // Validate email format only for customers
        if ($role === 'customer' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address');
        }
        
        global $pdo;
        
        // Check for account lockout based on role
        if ($role === 'admin') {
            $stmt = $pdo->prepare("
                SELECT id, full_name, email, username, password_hash, is_active, 
                       failed_login_attempts, locked_until
                FROM admins 
                WHERE username = ?
            ");
            $stmt->execute([$email]); // $email contains username for admin
        } else {
            $stmt = $pdo->prepare("
                SELECT id, first_name, last_name, email, password_hash, is_active, 
                       failed_login_attempts, locked_until
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
        }
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception('Invalid email or password');
        }
        
        // Check if account is active
        if (!$user['is_active']) {
            throw new Exception('Your account has been deactivated. Please contact support.');
        }
        
        // Check if account is locked
        if ($user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
            throw new Exception('Account is temporarily locked due to multiple failed login attempts. Please try again later.');
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Increment failed login attempts
            $failedAttempts = $user['failed_login_attempts'] + 1;
            $lockedUntil = null;
            
            // Lock account after 5 failed attempts for 30 minutes
            if ($failedAttempts >= 5) {
                $lockedUntil = (new DateTime())->add(new DateInterval('PT30M'))->format('Y-m-d H:i:s');
            }
            
            $tableName = $role === 'admin' ? 'admins' : 'users';
            $stmt = $pdo->prepare("
                UPDATE {$tableName} 
                SET failed_login_attempts = ?, locked_until = ? 
                WHERE id = ?
            ");
            $stmt->execute([$failedAttempts, $lockedUntil, $user['id']]);
            
            throw new Exception('Invalid email or password');
        }
        
        // Reset failed login attempts on successful login
        $tableName = $role === 'admin' ? 'admins' : 'users';
        $stmt = $pdo->prepare("
            UPDATE {$tableName} 
            SET failed_login_attempts = 0, locked_until = NULL, last_login = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$user['id']]);
        
        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $role;
        $_SESSION['logged_in'] = true;
        
        if ($role === 'admin') {
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['admin_username'] = $user['username'];
        } else {
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        }
        
        // Set session timeout
        $sessionTimeout = $rememberMe ? (30 * 24 * 60 * 60) : (2 * 60 * 60); // 30 days or 2 hours
        $_SESSION['expires_at'] = time() + $sessionTimeout;
        
        // Store session in database
        $sessionId = session_id();
        $expiresAt = (new DateTime())->add(new DateInterval('P' . ($rememberMe ? '30' : '0') . 'DT' . ($rememberMe ? '0' : '2') . 'H'))->format('Y-m-d H:i:s');
        
        $stmt = $pdo->prepare("
            INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, expires_at) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            ip_address = VALUES(ip_address),
            user_agent = VALUES(user_agent),
            expires_at = VALUES(expires_at),
            is_active = TRUE
        ");
        
        $stmt->execute([
            $user['id'],
            $sessionId,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $expiresAt
        ]);
        
        $userName = $role === 'admin' ? $user['full_name'] : $user['first_name'] . ' ' . $user['last_name'];
        $welcomeName = $role === 'admin' ? $user['full_name'] : $user['first_name'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful! Welcome back, ' . $welcomeName . '!',
            'user' => [
                'id' => $user['id'],
                'name' => $userName,
                'email' => $user['email'],
                'role' => $role
            ]
        ]);
        
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>