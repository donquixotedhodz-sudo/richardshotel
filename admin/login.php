<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Richard's Hotel & Resort</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/auth.css">
    <style>
        .admin-login-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 50%, #1a1a1a 100%);
        }
        
        .hotel-image-side {
            background: linear-gradient(rgba(196, 30, 58, 0.8), rgba(196, 30, 58, 0.8)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 600"><rect width="400" height="600" fill="%23c41e3a"/><rect x="50" y="100" width="300" height="200" rx="10" fill="%23ffffff" opacity="0.1"/><rect x="80" y="130" width="240" height="20" fill="%23ffffff" opacity="0.3"/><rect x="80" y="160" width="180" height="15" fill="%23ffffff" opacity="0.2"/><rect x="80" y="180" width="200" height="15" fill="%23ffffff" opacity="0.2"/><circle cx="200" cy="400" r="80" fill="%23ffffff" opacity="0.1"/><rect x="150" y="350" width="100" height="100" rx="50" fill="%23ffffff" opacity="0.2"/></svg>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
        }
        
        .hotel-content {
            text-align: center;
            z-index: 2;
            padding: 2rem;
        }
        
        .hotel-content h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .hotel-content p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 400px;
        }
        
        .admin-form-side {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .admin-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
    </style>
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

    <!-- Admin Login Section -->
    <section class="admin-login-section">
        <div class="container-fluid h-100">
            <div class="row h-100" style="min-height: 100vh; padding-top: 80px;">
                <!-- Left Side - Hotel Image -->
                <div class="col-lg-6 hotel-image-side">
                    <div class="hotel-content">
                        <h1>Richard's Hotel</h1>
                        <p>Administrative Portal</p>
                        <p>Manage your hotel operations with ease and efficiency</p>
                    </div>
                </div>
                
                <!-- Right Side - Admin Login Form -->
                <div class="col-lg-6 admin-form-side">
                    <div class="form-container">
                        <div class="text-center">
                            <span class="admin-badge">
                                <i class="fas fa-user-shield me-2"></i>Admin Access
                            </span>
                        </div>
                        
                        <h2 class="form-title">Admin Login</h2>
                        <p class="form-subtitle">Access the administrative dashboard</p>
                        
                        <!-- Alert Messages -->
                        <div id="alertMessage" class="alert" style="display: none;"></div>
                        
                        <form id="adminLoginForm">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="adminUsername" placeholder="Username" required>
                                <label for="adminUsername">Username</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="adminPassword" placeholder="Password" required>
                                <label for="adminPassword">Password</label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberAdmin">
                                    <label class="form-check-label" for="rememberAdmin">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="text-primary text-decoration-none">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In to Dashboard
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure admin access only
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin Login JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('adminLoginForm');
        const alertMessage = document.getElementById('alertMessage');
        
        function showAlert(message, type = 'danger') {
            alertMessage.className = `alert alert-${type}`;
            alertMessage.textContent = message;
            alertMessage.style.display = 'block';
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                alertMessage.style.display = 'none';
            }, 5000);
        }
        
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('adminUsername').value;
            const password = document.getElementById('adminPassword').value;
            const rememberMe = document.getElementById('rememberAdmin').checked;
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
            submitBtn.disabled = true;
            
            // Send AJAX request to auth handler
            fetch('auth_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=admin_login&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&rememberMe=${rememberMe}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Login successful! Redirecting to dashboard...', 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1500);
                } else {
                    showAlert(data.message || 'Invalid username or password');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred. Please try again.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    });
    </script>
</body>
</html>