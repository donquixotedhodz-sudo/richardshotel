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
    <title>Admin Dashboard - Richard's Hotel & Resort</title>
    
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
            width: 100px;
            height: 100px;
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
        
        /* Remove duplicate styles - already defined above */
        
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
        
        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #c41e3a 0%, #e74c3c 100%);
            border: none;
            color: white;
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, #a01729 0%, #c0392b 100%);
            color: white;
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
        
        /* Dashboard Stats Cards */
        .stat-card {
            border-left: 4px solid #4e73df;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .text-xs {
            font-size: 0.7rem;
        }
        
        .text-gray-800 {
            color: #5a5c69;
        }
        
        .text-gray-300 {
            color: #dddfeb;
        }
        
        /* Chart Containers */
        .chart-area {
            position: relative;
            height: 10rem;
            width: 100%;
        }
        
        .chart-pie {
            position: relative;
            height: 15rem;
            width: 100%;
        }
        
        /* Card Headers */
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
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
                    <a href="dashboard.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="bookings.php" class="nav-link">
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
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard Overview
                </h2>
            </div>
            <!-- Dashboard Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Bookings
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($bookings); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Revenue (Monthly)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php 
                                        $totalRevenue = 0;
                                        foreach($bookings as $booking) {
                                            if($booking['booking_status'] === 'confirmed' || $booking['booking_status'] === 'checked_out') {
                                                $totalRevenue += $booking['total_price'];
                                            }
                                        }
                                        echo number_format($totalRevenue, 2);
                                    ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Occupancy Rate
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">75%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bed fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card stat-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending Bookings
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php 
                                        $pendingCount = 0;
                                        foreach($bookings as $booking) {
                                            if($booking['booking_status'] === 'pending') {
                                                $pendingCount++;
                                            }
                                        }
                                        echo $pendingCount;
                                    ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="dashboard-card">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="revenueChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-lg-5">
                    <div class="dashboard-card">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Booking Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="statusChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="dashboard-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bookings)): ?>
                                    <?php foreach(array_slice($bookings, 0, 5) as $booking): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></td>
                                        <td>New booking by <?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                        <td><span class="badge bg-<?php 
                                            switch($booking['booking_status']) {
                                                case 'confirmed': echo 'success'; break;
                                                case 'pending': echo 'warning'; break;
                                                case 'cancelled': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?>"><?php echo ucfirst($booking['booking_status']); ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No recent activity</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Status Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusData = {
                confirmed: <?php 
                    $confirmed = 0;
                    foreach($bookings as $booking) {
                        if($booking['booking_status'] === 'confirmed') $confirmed++;
                    }
                    echo $confirmed;
                ?>,
                pending: <?php 
                    $pending = 0;
                    foreach($bookings as $booking) {
                        if($booking['booking_status'] === 'pending') $pending++;
                    }
                    echo $pending;
                ?>,
                cancelled: <?php 
                    $cancelled = 0;
                    foreach($bookings as $booking) {
                        if($booking['booking_status'] === 'cancelled') $cancelled++;
                    }
                    echo $cancelled;
                ?>
            };
            
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Confirmed', 'Pending', 'Cancelled'],
                    datasets: [{
                        data: [statusData.confirmed, statusData.pending, statusData.cancelled],
                        backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        });
        
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