-- Create database
CREATE DATABASE IF NOT EXISTS rhms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE rhms_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at),
    INDEX idx_users_active (is_active),
    INDEX idx_users_email_verified (email_verified)
);

-- Create user sessions table for better session management
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(128) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 1 HOUR),
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_sessions_active (is_active)
);

-- Create password reset tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- Create admin table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    INDEX idx_admin_email (email),
    INDEX idx_admin_username (username),
    INDEX idx_admin_active (is_active)
);

-- Insert default admin user (username: admin, password: admin123)
-- Note: In production, change this password immediately
INSERT INTO admins (full_name, email, username, password_hash) 
VALUES (
    'System Administrator',
    'admin@richardshotel.com',
    'admin',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj6hsxq9S/EG' -- admin123
) ON DUPLICATE KEY UPDATE id=id;

-- Insert a default customer user (password: admin123)
-- Note: In production, change this password immediately
INSERT INTO users (first_name, last_name, email, phone, password_hash, email_verified) 
VALUES (
    'Customer',
    'User',
    'customer@richardshotel.com',
    '+1234567890',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj6hsxq9S/EG', -- admin123
    TRUE
) ON DUPLICATE KEY UPDATE id=id;

-- Create room types table
CREATE TABLE IF NOT EXISTS room_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    room_type_id INT NOT NULL,
    status ENUM('available', 'occupied', 'maintenance', 'out_of_order') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id),
    INDEX idx_room_number (room_number),
    INDEX idx_room_type (room_type_id),
    INDEX idx_room_status (status)
);

-- Create booking duration rates table
CREATE TABLE IF NOT EXISTS booking_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    duration_hours INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id),
    UNIQUE KEY unique_rate (room_type_id, duration_hours),
    INDEX idx_room_type_duration (room_type_id, duration_hours)
);

-- Insert room types
INSERT INTO room_types (type_name, description) VALUES 
('Normal Room', 'Standard room with basic amenities'),
('Family Room', 'Spacious room designed for families');

-- Insert rooms (9 normal rooms + 2 family rooms)
INSERT INTO rooms (room_number, room_type_id) VALUES 
-- Normal rooms (room_type_id = 1)
('101', 1), ('102', 1), ('103', 1),
('201', 1), ('202', 1), ('203', 1),
('301', 1), ('302', 1), ('303', 1),
-- Family rooms (room_type_id = 2)
('401', 2), ('402', 2);

-- Insert booking rates
-- Normal room rates
INSERT INTO booking_rates (room_type_id, duration_hours, price) VALUES 
(1, 3, 500.00),   -- Normal room 3 hours
(1, 12, 1200.00), -- Normal room 12 hours
(1, 24, 1000.00), -- Normal room 24 hours
-- Family room rates
(2, 24, 2000.00); -- Family room 24 hours only

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_address TEXT NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    room_type_id INT NOT NULL,
    room_id INT,
    duration_hours INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    check_in_datetime DATETIME NOT NULL,
    check_out_datetime DATETIME NOT NULL,
    proof_of_payment VARCHAR(255),
    special_requests TEXT,
    booking_status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    INDEX idx_booking_status (booking_status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_check_in (check_in_datetime),
    INDEX idx_customer_email (customer_email)
);

-- All indexes are now defined inline with table creation