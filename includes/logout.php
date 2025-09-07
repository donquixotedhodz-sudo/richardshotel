<?php
require_once 'session.php';

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    logout();
    
    // If it's an AJAX request, return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
        exit;
    }
    
    // For regular requests, redirect to home page
    header('Location: ../index.php?logged_out=1');
    exit;
}

// If accessed directly without POST/GET, redirect to home
header('Location: ../index.php');
exit;
?>