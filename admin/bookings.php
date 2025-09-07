<?php
// Simple session check
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Include database configuration
require_once 'config.php';

// Fetch bookings from database
try {
    $stmt = $pdo->prepare("
        SELECT 
            b.id,
            b.customer_name,
            b.customer_email,
            b.customer_phone,
            rt.type_name as room_type,
            r.room_number,
            b.check_in_datetime,
            b.check_out_datetime,
            b.total_price,
            b.booking_status,
            b.payment_status,
            b.created_at
        FROM bookings b
        LEFT JOIN room_types rt ON b.room_type_id = rt.id
        LEFT JOIN rooms r ON b.room_id = r.id
        ORDER BY b.created_at DESC
        LIMIT 20
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    $bookings = [];
    error_log("Error fetching bookings: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - Richard's Hotel & Resort</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
            padding-top: 70px;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1050;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            padding-top: 90px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        
        .sidebar-title {
            color: white;
            font-size: 1.2rem;
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 15px 25px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .nav-link:hover {
            color: white;
            text-decoration: underline;
            text-decoration-color: #c41e3a;
            text-underline-offset: 4px;
        }
        
        .nav-link.active {
            color: white;
            background: rgba(52, 152, 219, 0.2);
            border-left-color: #3498db;
        }
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: calc(100vh - 70px);
        }
        
        /* Navbar Header Styles */
        .navbar {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            height: 70px;
            padding: 0 20px;
            z-index: 1040;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
        }
        
        .navbar-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .profile-picture {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .dropdown-item {
            color: #333;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #c41e3a 0%, #e74c3c 100%);
            color: white;
        }
        
        .navbar-text {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
        }
        
        .navbar .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .navbar .nav-link:hover {
            color: white !important;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .welcome-header {
            background: linear-gradient(135deg, #c41e3a 0%, #e74c3c 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .bookings-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .bookings-table thead th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px 12px;
        }
        
        .bookings-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .bookings-table tbody tr:hover {
            background-color: rgba(196, 30, 58, 0.05);
            transform: translateY(-1px);
        }
        
        .bookings-table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.05);
        }
        
        .btn-group .btn {
            margin: 0 1px;
        }
        
        .badge {
            font-size: 0.75em;
            padding: 6px 10px;
        }
        
        .pagination .page-link {
            color: #c41e3a;
            border-color: #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #c41e3a;
            border-color: #c41e3a;
        }
        
        .pagination .page-link:hover {
            color: #a01729;
            background-color: rgba(196, 30, 58, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../logo/logo.png" alt="Richard's Hotel Logo" class="navbar-logo me-2">
            </a>
            <div class="navbar-nav ms-auto">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../logo/logo.png" alt="Profile" class="profile-picture me-2">
                        <span class="d-none d-md-inline"><?php echo htmlspecialchars($admin_username); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../logo/logo.png" alt="Richard's Hotel Logo" class="sidebar-logo">
                <h4 class="sidebar-title">Admin Panel</h4>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="bookings.php" class="nav-link active">
                        <i class="fas fa-calendar-check"></i>
                        Bookings
                    </a>
                </div>
                <div class="nav-item">
                    <a href="customers.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        Customers
                    </a>
                </div>
                <div class="nav-item">
                    <a href="reports.php" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Reports
                    </a>
                </div>
                <div class="nav-item">
                    <a href="settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </div>
                <div class="nav-item" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="mb-4">
                <h2 class="text-white mb-0">
                    <i class="fas fa-calendar-check me-2"></i>
                    Bookings Management
                </h2>
            </div>
            
            <!-- Bookings Management Section -->
            <div class="dashboard-card mb-4">
                <div class="welcome-header p-4">
                    <h1 class="mb-0">
                        <i class="fas fa-calendar-check me-3"></i>
                        Hotel Bookings Management
                    </h1>
                    <p class="mb-0 mt-2">View and manage all hotel reservations</p>
                </div>
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Recent Bookings</h4>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Booking
                        </button>
                    </div>
                    
                    <!-- Bookings Table -->
                    <div class="table-responsive">
                        <table class="table table-hover bookings-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest Name</th>
                                    <th>Room Type</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No bookings found</p>
                                        <small class="text-muted">Bookings will appear here once customers make reservations</small>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($bookings as $booking): ?>
                                <?php
                                    // Determine status badge class
                                    $statusClass = 'secondary';
                                    switch ($booking['booking_status']) {
                                        case 'confirmed':
                                            $statusClass = 'success';
                                            break;
                                        case 'pending':
                                            $statusClass = 'warning';
                                            break;
                                        case 'checked_in':
                                            $statusClass = 'info';
                                            break;
                                        case 'checked_out':
                                            $statusClass = 'secondary';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'danger';
                                            break;
                                    }
                                ?>
                                <tr>
                                    <td><strong>#BK<?php echo str_pad($booking['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['room_type'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($booking['check_in_datetime'])); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($booking['check_out_datetime'])); ?></td>
                                    <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo ucfirst($booking['booking_status']); ?></span></td>
                                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" title="View Details" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" title="Edit Booking" onclick="editBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" title="Cancel Booking" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Bookings pagination">
                        <ul class="pagination justify-content-center mt-4">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Previous</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Booking action functions
        function viewBooking(bookingId) {
            alert('View booking #' + bookingId + ' - This will redirect to booking details page');
            // TODO: Implement redirect to booking details page
            // window.location.href = 'booking-details.php?id=' + bookingId;
        }

        function editBooking(bookingId) {
            alert('Edit booking #' + bookingId + ' - This will redirect to booking edit page');
            // TODO: Implement redirect to booking edit page
            // window.location.href = 'edit-booking.php?id=' + bookingId;
        }

        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel booking #' + bookingId + '?')) {
                alert('Cancel booking #' + bookingId + ' - This will update booking status');
                // TODO: Implement AJAX call to cancel booking
                // fetch('cancel-booking.php', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify({ booking_id: bookingId })
                // }).then(response => response.json())
                //   .then(data => {
                //       if (data.success) {
                //           location.reload();
                //       } else {
                //           alert('Error cancelling booking');
                //       }
                //   });
            }
        }
    </script>
</body>
</html>