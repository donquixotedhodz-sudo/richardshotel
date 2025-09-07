<?php
require_once 'config.php';
require_once 'session.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in and get current user
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to make a booking']);
    exit;
}

$current_user = getCurrentUser();
if (!$current_user) {
    echo json_encode(['success' => false, 'message' => 'Unable to retrieve user information']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Log received data for debugging
error_log('POST data: ' . print_r($_POST, true));
error_log('FILES data: ' . print_r($_FILES, true));

try {
    // Get form data
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');
    $room_type_id = intval($_POST['room_type_id'] ?? 0);
    $room_id = intval($_POST['room_id'] ?? 0);
    $duration_hours = intval($_POST['duration_hours'] ?? 0);
    $check_in_datetime = $_POST['check_in_datetime'] ?? '';
    $check_out_datetime = $_POST['check_out_datetime'] ?? '';
    $special_requests = trim($_POST['special_requests'] ?? '');
    
    // Get the actual price from database instead of trusting frontend calculation
    $price_query = "SELECT price FROM booking_rates WHERE room_type_id = ? AND duration_hours = ?";
    $price_stmt = $pdo->prepare($price_query);
    $price_stmt->execute([$room_type_id, $duration_hours]);
    $total_price = $price_stmt->fetchColumn();
    
    if (!$total_price) {
        echo json_encode(['success' => false, 'message' => 'Invalid duration selected for this room type']);
        exit;
    }
    
    // Validate required fields
    if (empty($customer_name) || empty($customer_email) || empty($customer_phone) || 
        empty($customer_address) || $room_type_id <= 0 || $room_id <= 0 || $duration_hours <= 0 || 
        empty($check_in_datetime) || empty($check_out_datetime)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
    
    // Validate email format
    if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        exit;
    }
    
    // Validate datetime format
    $check_in = DateTime::createFromFormat('Y-m-d\TH:i', $check_in_datetime);
    $check_out = DateTime::createFromFormat('Y-m-d\TH:i', $check_out_datetime);
    
    if (!$check_in || !$check_out) {
        echo json_encode(['success' => false, 'message' => 'Invalid date/time format']);
        exit;
    }
    
    // Check if check-in is in the future
    $now = new DateTime();
    if ($check_in <= $now) {
        echo json_encode(['success' => false, 'message' => 'Check-in date must be in the future']);
        exit;
    }
    
    // Verify that the selected room exists and belongs to the correct room type
    $room_check_query = "SELECT id, room_number, status FROM rooms WHERE id = ? AND room_type_id = ?";
    $room_check_stmt = $pdo->prepare($room_check_query);
    $room_check_stmt->execute([$room_id, $room_type_id]);
    $selected_room = $room_check_stmt->fetch();
    
    if (!$selected_room) {
        echo json_encode(['success' => false, 'message' => 'Invalid room selection']);
        exit;
    }
    
    // Check if the room is available for the requested time period
    $availability_query = "
        SELECT COUNT(*) as conflicting_bookings
        FROM bookings 
        WHERE room_id = ? 
        AND booking_status IN ('confirmed', 'checked_in')
        AND (
            (check_in_datetime < ? AND check_out_datetime > ?) OR
            (check_in_datetime < ? AND check_out_datetime > ?) OR
            (check_in_datetime >= ? AND check_in_datetime < ?)
        )
    ";
    
    $availability_stmt = $pdo->prepare($availability_query);
    $availability_stmt->execute([
        $room_id,
        $check_out->format('Y-m-d H:i:s'), // check_in_datetime < check_out
        $check_in->format('Y-m-d H:i:s'),  // check_out_datetime > check_in
        $check_out->format('Y-m-d H:i:s'), // check_in_datetime < check_out
        $check_in->format('Y-m-d H:i:s'),  // check_out_datetime > check_in
        $check_in->format('Y-m-d H:i:s'),  // check_in_datetime >= check_in
        $check_out->format('Y-m-d H:i:s')  // check_in_datetime < check_out
    ]);
    
    $conflicting_bookings = $availability_stmt->fetchColumn();
    
    if ($conflicting_bookings > 0) {
        echo json_encode(['success' => false, 'message' => 'Selected room is not available for the requested time period']);
        exit;
    }
    
    // Handle file upload for proof of payment
    $proof_of_payment = null;
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/payments/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['proof_of_payment']['name']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (in_array(strtolower($file_info['extension']), $allowed_extensions)) {
            $filename = 'payment_' . uniqid() . '.' . $file_info['extension'];
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $upload_path)) {
                $proof_of_payment = $filename;
            }
        }
    }
    
    // Verify room type exists
    $room_type_query = "SELECT type_name FROM room_types WHERE id = ?";
    $room_type_stmt = $pdo->prepare($room_type_query);
    $room_type_stmt->execute([$room_type_id]);
    $room_type = $room_type_stmt->fetchColumn();
    
    if (!$room_type) {
        echo json_encode(['success' => false, 'message' => 'Invalid room type selected']);
        exit;
    }
    
    // Insert booking into database
    $insert_query = "
        INSERT INTO bookings (
            user_id, customer_name, customer_email, customer_phone, customer_address,
            room_type_id, room_id, duration_hours, check_in_datetime, check_out_datetime,
            total_price, proof_of_payment, special_requests, booking_status, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW()
        )
    ";
    
    $insert_stmt = $pdo->prepare($insert_query);
    $result = $insert_stmt->execute([
        $current_user['id'],
        $customer_name,
        $customer_email,
        $customer_phone,
        $customer_address,
        $room_type_id,
        $room_id,
        $duration_hours,
        $check_in->format('Y-m-d H:i:s'),
        $check_out->format('Y-m-d H:i:s'),
        $total_price,
        $proof_of_payment,
        $special_requests
    ]);
    
    if ($result) {
        $booking_id = $pdo->lastInsertId();
        echo json_encode([
            'success' => true, 
            'message' => 'Booking submitted successfully! Your booking ID is #' . $booking_id . ' for Room ' . $selected_room['room_number'],
            'booking_id' => $booking_id,
            'room_number' => $selected_room['room_number']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save booking. Please try again.']);
    }
    
} catch (Exception $e) {
    error_log('Booking error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while processing your booking']);
}
?>