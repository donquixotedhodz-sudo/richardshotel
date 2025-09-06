<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up - Richard's Hotel & Resort</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
            <img src="../logo/logo.png" alt="Richard's Hotel Logo" class="navbar-logo me-2">
                Richard's Hotel
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </nav>

    <!-- Authentication Section -->
    <section class="auth-section">
        <div class="auth-overlay"></div>
        <div class="container-fluid h-100">
            <div class="row h-100">
                <!-- Left Side - Toggle Button / Signup Form -->
                <div class="col-lg-6 auth-left" id="authLeft">
                    <!-- Default: Signup Button -->
                    <div class="signup-toggle" id="signupToggle">
                        <div class="toggle-content">
                            <h2>New to Richard's Hotel?</h2>
                            <p>Create an account to enjoy exclusive benefits and seamless booking experience.</p>
                            <button class="btn btn-outline-light btn-lg" id="showSignupBtn">
                                <i class="fas fa-user-plus me-2"></i>Sign Up
                            </button>
                        </div>
                    </div>
                    
                    <!-- Signup Form (Hidden by default) -->
                    <div class="auth-form signup-form" id="signupForm" style="display: none;">
                        <div class="form-container">
                            <h2 class="form-title">Create Account</h2>
                            <p class="form-subtitle">Join Richard's Hotel & Resort</p>
                            
                            <form id="signupFormElement">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="firstName" placeholder="First Name" required>
                                            <label for="firstName">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="lastName" placeholder="Last Name" required>
                                            <label for="lastName">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="signupEmail" placeholder="Email Address" required>
                                            <label for="signupEmail">Email Address</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="phone" placeholder="Phone Number" required>
                                            <label for="phone">Phone Number</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="signupPassword" placeholder="Password" required>
                                            <label for="signupPassword">Password</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
                                            <label for="confirmPassword">Confirm Password</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                            <label class="form-check-label" for="agreeTerms">
                                                I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-user-plus me-2"></i>Create Account
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Login Form / Toggle Button -->
                <div class="col-lg-6 auth-right" id="authRight">
                    <!-- Default: Login Form -->
                    <div class="auth-form login-form" id="loginForm">
                        <div class="form-container">
                            <h2 class="form-title">Welcome Back</h2>
                            <p class="form-subtitle">Sign in to your account</p>
                            
                            <form id="loginFormElement">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="loginEmail" placeholder="Email Address" required>
                                    <label for="loginEmail">Email Address</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="loginPassword" placeholder="Password" required>
                                    <label for="loginPassword">Password</label>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="#" class="text-primary text-decoration-none">Forgot Password?</a>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                            </form>
                            
                            <div class="social-login">
                                <p class="text-center mb-3">Or sign in with</p>
                                <div class="d-flex gap-3 justify-content-center">
                                    <button class="btn btn-outline-primary social-btn">
                                        <i class="fab fa-google"></i>
                                    </button>
                                    <button class="btn btn-outline-primary social-btn">
                                        <i class="fab fa-facebook-f"></i>
                                    </button>
                                    <button class="btn btn-outline-primary social-btn">
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Login Toggle Button (Hidden by default) -->
                    <div class="login-toggle" id="loginToggle" style="display: none;">
                        <div class="toggle-content">
                            <h2>Already have an account?</h2>
                            <p>Sign in to access your bookings and enjoy personalized services.</p>
                            <button class="btn btn-outline-light btn-lg" id="showLoginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/auth.js"></script>
</body>
</html>