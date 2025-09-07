<?php
require_once 'config.php';
require_once 'session.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to check room availability']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get form data
    $room_type_id = intval($_POST['room_type_id'] ?? 0);
    $check_in_datetime = $_POST['check_in_datetime'] ?? '';
    $check_out_datetime = $_POST['check_out_datetime'] ?? '';
    
    // Validate required fields
    if ($room_type_id <= 0 || empty($check_in_datetime) || empty($check_out_datetime)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }
    
    // Validate datetime format
    $check_in = DateTime::createFromFormat('Y-m-d\TH:i', $check_in_datetime);
    $check_out = DateTime::createFromFormat('Y-m-d\TH:i', $check_out_datetime);
    
    if (!$check_in || !$check_out) {
        echo json_encode(['success' => false, 'message' => 'Invalid date/time format']);
        exit;
    }
    
    // Get all rooms of the specified type
    $rooms_query = "
        SELECT r.id, r.room_number, r.status
        FROM rooms r
        WHERE r.room_type_id = ? AND r.status IN ('available', 'occupied')
        ORDER BY r.room_number
    ";
    
    $rooms_stmt = $pdo->prepare($rooms_query);
    $rooms_stmt->execute([$room_type_id]);
    $all_rooms = $rooms_stmt->fetchAll();
    
    // Get rooms that are booked during the requested time period
    $booked_rooms_query = "
        SELECT DISTINCT b.room_id
        FROM bookings b
        WHERE b.room_type_id = ? 
        AND b.room_id IS NOT NULL
        AND b.booking_status IN ('confirmed', 'checked_in')
        AND (
            (b.check_in_datetime < ? AND b.check_out_datetime > ?) OR
            (b.check_in_datetime < ? AND b.check_out_datetime > ?) OR
            (b.check_in_datetime >= ? AND b.check_in_datetime < ?)
        )
    ";
    
    $booked_stmt = $pdo->prepare($booked_rooms_query);
    $booked_stmt->execute([
        $room_type_id,
        $check_out->format('Y-m-d H:i:s'), // check_in_datetime < check_out
        $check_in->format('Y-m-d H:i:s'),  // check_out_datetime > check_in
        $check_out->format('Y-m-d H:i:s'), // check_in_datetime < check_out
        $check_in->format('Y-m-d H:i:s'),  // check_out_datetime > check_in
        $check_in->format('Y-m-d H:i:s'),  // check_in_datetime >= check_in
        $check_out->format('Y-m-d H:i:s')  // check_in_datetime < check_out
    ]);
    
    $booked_room_ids = $booked_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Filter available rooms
    $available_rooms = [];
    foreach ($all_rooms as $room) {
        // Room is available if:
        // 1. Its status is 'available', OR
        // 2. Its status is 'occupied' but not booked during the requested time
        if ($room['status'] === 'available' || 
            ($room['status'] === 'occupied' && !in_array($room['id'], $booked_room_ids))) {
            
            // Double-check if room is not in the booked list
            if (!in_array($room['id'], $booked_room_ids)) {
                $available_rooms[] = [
                    'id' => $room['id'],
                    'room_number' => $room['room_number'],
                    'status' => $room['status']
                ];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'available_rooms' => $available_rooms,
        'total_available' => count($available_rooms)
    ]);
    
} catch (Exception $e) {
    error_log('Room availability error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while checking room availability']);
}
?>