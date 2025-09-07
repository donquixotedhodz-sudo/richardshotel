<?php
require_once 'includes/session.php';
$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Richards hotel & Resort - Luxury Accommodation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo/logo.png" alt="Richard's Hotel Logo" class="navbar-logo me-2">
                Richard's Hotel            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#rooms">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#amenities">Amenities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($currentUser['first_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar me-2"></i>My Bookings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="includes/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="includes/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-lg-8 mx-auto text-center text-white">
                    <h1 class="hero-title mb-4">Experience Luxury Like Never Before</h1>
                    <p class="hero-subtitle mb-5">Indulge in world-class hospitality at Richards hotel & Resort, where elegance meets comfort in perfect harmony.</p>
                    <div class="hero-buttons">
                        <a href="#rooms" class="btn btn-primary btn-lg me-3">View Rooms</a>
                        <a href="#contact" class="btn btn-outline-light btn-lg">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title">Our Room Types</h2>
                    <p class="section-subtitle">Choose from our available room types with flexible booking options</p>
                </div>
            </div>
            <div class="row g-4">
                <?php
                // Include database configuration
                require_once 'includes/config.php';
                
                try {
                    // Fetch room types with their rates
                    $stmt = $pdo->query("
                        SELECT rt.id, rt.type_name, rt.description,
                               COUNT(r.id) as available_rooms
                        FROM room_types rt 
                        LEFT JOIN rooms r ON rt.id = r.room_type_id AND r.status = 'available'
                        GROUP BY rt.id, rt.type_name, rt.description
                        ORDER BY rt.id
                    ");
                    
                    $roomTypes = $stmt->fetchAll();
                    
                    foreach ($roomTypes as $roomType) {
                        // Get rates for this room type
                        $rateStmt = $pdo->prepare("
                            SELECT duration_hours, price 
                            FROM booking_rates 
                            WHERE room_type_id = ? 
                            ORDER BY duration_hours
                        ");
                        $rateStmt->execute([$roomType['id']]);
                        $rates = $rateStmt->fetchAll();
                        
                        // Set image based on room type
                        $image = ($roomType['type_name'] == 'Family Room') 
                            ? 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                            : 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                        
                        // Set features based on room type
                        $features = ($roomType['type_name'] == 'Family Room') 
                            ? ['<i class="fas fa-users"></i> Family Size', '<i class="fas fa-wifi"></i> Free WiFi', '<i class="fas fa-tv"></i> Smart TV']
                            : ['<i class="fas fa-bed"></i> Comfortable Bed', '<i class="fas fa-wifi"></i> Free WiFi', '<i class="fas fa-tv"></i> Smart TV'];
                ?>
                <div class="col-lg-6 col-md-6">
                    <div class="room-card">
                        <div class="room-image">
                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($roomType['type_name']); ?>" class="img-fluid">
                            <div class="room-overlay">
                                <span class="btn btn-light">View Details</span>
                            </div>
                        </div>
                        <div class="room-content">
                            <h4><?php echo htmlspecialchars($roomType['type_name']); ?></h4>
                            <p><?php echo htmlspecialchars($roomType['description']); ?></p>
                            <div class="room-features">
                                <?php foreach ($features as $feature): ?>
                                <span><?php echo $feature; ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="room-availability mb-2">
                                <small class="text-muted">Available Rooms: <?php echo $roomType['available_rooms']; ?></small>
                            </div>
                            <div class="room-pricing">
                                <h6>Booking Options:</h6>
                                <?php foreach ($rates as $rate): ?>
                                <div class="price-option">
                                    <span class="duration"><?php echo $rate['duration_hours']; ?> hours</span>
                                    <span class="price">₱<?php echo number_format($rate['price'], 0); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="room-actions mt-3">
                                <?php if ($isLoggedIn): ?>
                                    <button type="button" class="btn btn-primary btn-sm w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#bookingModal"
                                            data-room-type="<?php echo htmlspecialchars($roomType['type_name']); ?>"
                                            data-room-type-id="<?php echo $roomType['id']; ?>">
                                        <i class="fas fa-calendar-plus me-1"></i>Book Now
                                    </button>
                                <?php else: ?>
                                    <a href="includes/login.php" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-sign-in-alt me-1"></i>Login to Book
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } catch (PDOException $e) {
                    echo '<div class="col-12"><div class="alert alert-danger">Error loading room types: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Amenities Section -->
    <section id="amenities" class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title">World-Class Amenities</h2>
                    <p class="section-subtitle">Discover exceptional facilities designed for your comfort and enjoyment</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="amenity-card text-center">
                        <div class="amenity-icon">
                            <i class="fas fa-swimming-pool"></i>
                        </div>
                        <h5>Infinity Pool</h5>
                        <p>Relax in our stunning rooftop infinity pool with panoramic city views.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="amenity-card text-center">
                        <div class="amenity-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <h5>Luxury Spa</h5>
                        <p>Rejuvenate your body and mind at our award-winning spa and wellness center.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="amenity-card text-center">
                        <div class="amenity-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h5>Fine Dining</h5>
                        <p>Experience culinary excellence at our Michelin-starred restaurant.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="amenity-card text-center">
                        <div class="amenity-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <h5>Fitness Center</h5>
                        <p>Stay active in our state-of-the-art fitness center with personal trainers.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title">Get In Touch</h2>
                    <p class="section-subtitle">Ready to experience luxury? Contact us or make a reservation today</p>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h4 class="mb-4">Contact Information</h4>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h6>Address</h6>
                                <p>123 Luxury Avenue, Downtown District<br>Metropolitan City, MC 12345</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h6>Phone</h6>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h6>Email</h6>
                                <p>reservations@richardshotel.com</p>
                            </div>
                        </div>
                        <div class="social-links mt-4">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <form id="contactForm" class="contact-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="Your Email" required>
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" placeholder="Subject">
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Richards hotel & Resort. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Designed with <i class="fas fa-heart text-danger"></i> for luxury hospitality</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">
                        <i class="fas fa-calendar-plus me-2"></i>Book Your Stay
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm">
                        <div class="row g-3">
                            <!-- Room Information -->
                            <div class="col-12">
                                <div class="alert alert-info" id="roomInfo">
                                    <h6 class="mb-2"><i class="fas fa-bed me-2"></i>Selected Room: <span id="selectedRoomType"></span></h6>
                                    <div id="availableDurations"></div>
                                </div>
                            </div>
                            
                            <!-- Customer Information -->
                            <div class="col-12">
                                <h6 class="mb-3"><i class="fas fa-user me-2"></i>Customer Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_address" class="form-label">Address *</label>
                                <input type="text" class="form-control" id="customer_address" name="customer_address" required>
                            </div>
                            
                            <!-- Booking Details -->
                            <div class="col-12 mt-4">
                                <h6 class="mb-3"><i class="fas fa-calendar me-2"></i>Booking Details</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="duration_hours" class="form-label">Duration *</label>
                                <select class="form-select" id="duration_hours" name="duration_hours" required>
                                    <option value="">Select Duration</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="room_id" class="form-label">Select Room *</label>
                                <select class="form-select" id="room_id" name="room_id" required>
                                    <option value="">Select a room</option>
                                </select>
                                <div class="form-text" id="roomAvailabilityText">Please select duration and check-in time first</div>
                            </div>
                            <div class="col-md-6">
                                <label for="total_price_display" class="form-label">Total Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="text" class="form-control" id="total_price_display" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="check_in_datetime" class="form-label">Check-in Date & Time *</label>
                                <input type="datetime-local" class="form-control" id="check_in_datetime" name="check_in_datetime" required>
                            </div>
                            <div class="col-md-6">
                                <label for="check_out_datetime" class="form-label">Check-out Date & Time *</label>
                                <input type="datetime-local" class="form-control" id="check_out_datetime" name="check_out_datetime" readonly>
                            </div>
                            <div class="col-12">
                                <label for="proof_of_payment" class="form-label">Proof of Payment *</label>
                                <input type="file" class="form-control" id="proof_of_payment" name="proof_of_payment" accept=".jpg,.jpeg,.png,.pdf" required>
                                <div class="form-text">Upload your payment receipt (JPG, PNG, or PDF format)</div>
                            </div>
                            <div class="col-12">
                                <label for="special_requests" class="form-label">Special Requests</label>
                                <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Any special requests or preferences..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Hidden Fields -->
                        <input type="hidden" id="room_type_id" name="room_type_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="submitBooking">
                        <i class="fas fa-check me-1"></i>Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <!-- Booking Modal JS -->
    <script src="assets/js/booking-modal.js"></script>
</body>
</html>