<?php
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if room_type_id is provided
if (!isset($_GET['room_type_id']) || empty($_GET['room_type_id'])) {
    echo json_encode(['success' => false, 'message' => 'Room type ID is required']);
    exit;
}

$roomTypeId = intval($_GET['room_type_id']);

try {
    // Fetch booking rates for the specified room type
    $stmt = $pdo->prepare("
        SELECT duration_hours, price 
        FROM booking_rates 
        WHERE room_type_id = ? 
        ORDER BY duration_hours
    ");
    $stmt->execute([$roomTypeId]);
    $rates = $stmt->fetchAll();
    
    if ($rates) {
        // Format rates for JavaScript consumption
        $formattedRates = [];
        foreach ($rates as $rate) {
            $hours = $rate['duration_hours'];
            $price = floatval($rate['price']);
            
            // Create label based on duration
            if ($hours < 24) {
                $label = $hours . ' Hour' . ($hours > 1 ? 's' : '');
            } else {
                $days = $hours / 24;
                $label = $days . ' Day' . ($days > 1 ? 's' : '');
            }
            
            $formattedRates[] = [
                'value' => $hours,
                'label' => $label,
                'price' => $price
            ];
        }
        
        echo json_encode([
            'success' => true,
            'rates' => $formattedRates
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No rates found for this room type'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>