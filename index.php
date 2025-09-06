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
                        <a href="includes/auth.php" class="btn btn-primary btn-lg me-3">Book Now</a>
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
                    <h2 class="section-title">Luxurious Accommodations</h2>
                    <p class="section-subtitle">Choose from our carefully curated selection of premium rooms and suites</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="room-card">
                        <div class="room-image">
                            <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Deluxe Room" class="img-fluid">
                            <div class="room-overlay">
                                <a href="#" class="btn btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="room-content">
                            <h4>Deluxe Room</h4>
                            <p>Elegant comfort with modern amenities and stunning city views.</p>
                            <div class="room-features">
                                <span><i class="fas fa-bed"></i> King Bed</span>
                                <span><i class="fas fa-wifi"></i> Free WiFi</span>
                                <span><i class="fas fa-tv"></i> Smart TV</span>
                            </div>
                            <div class="room-price">
                                <span class="price">$299</span>
                                <span class="period">/night</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="room-card">
                        <div class="room-image">
                            <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Executive Suite" class="img-fluid">
                            <div class="room-overlay">
                                <a href="#" class="btn btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="room-content">
                            <h4>Executive Suite</h4>
                            <p>Spacious luxury with separate living area and premium amenities.</p>
                            <div class="room-features">
                                <span><i class="fas fa-bed"></i> King Bed</span>
                                <span><i class="fas fa-couch"></i> Living Area</span>
                                <span><i class="fas fa-concierge-bell"></i> Butler Service</span>
                            </div>
                            <div class="room-price">
                                <span class="price">$599</span>
                                <span class="period">/night</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="room-card">
                        <div class="room-image">
                            <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Presidential Suite" class="img-fluid">
                            <div class="room-overlay">
                                <a href="#" class="btn btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="room-content">
                            <h4>Presidential Suite</h4>
                            <p>Ultimate luxury with panoramic views and exclusive privileges.</p>
                            <div class="room-features">
                                <span><i class="fas fa-crown"></i> Premium</span>
                                <span><i class="fas fa-hot-tub"></i> Private Jacuzzi</span>
                                <span><i class="fas fa-glass-cheers"></i> Champagne</span>
                            </div>
                            <div class="room-price">
                                <span class="price">$1,299</span>
                                <span class="period">/night</span>
                            </div>
                        </div>
                    </div>
                </div>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>