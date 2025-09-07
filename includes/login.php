<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Richard's Hotel & Resort</title>
    
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

    <!-- Role Selection Section -->
    <section class="auth-section">
        <div class="container-fluid h-100">
            <div class="row h-100" style="min-height: 100vh;">
                <!-- Left Side - Hotel Image -->
                <div class="col-lg-6 hotel-image-side">
                    <div class="hotel-content">
                        <h1>Richard's Hotel</h1>
                        <p>Welcome Portal</p>
                        <p>Choose your login type to access our services</p>
                    </div>
                </div>
                
                <!-- Right Side - Login Selection Form -->
                <div class="col-lg-6 login-form-side">
                    <div class="form-container text-center">
                        <div class="hotel-logo mb-4">
                            <img src="../logo/logo.png" alt="Richard's Hotel Logo" style="height: 80px; width: auto;">
                        </div>
                        
                        <h2 class="form-title mb-2">Welcome!</h2>
                        <p class="form-subtitle mb-5">Please select your login type to continue</p>
                        
                        <div class="role-selection">
                            <a href="auth.php?role=customer" class="btn btn-primary mb-3 role-btn customer-btn">
                                <i class="fas fa-user me-2"></i>
                                <span>Customer</span>
                            </a>
                            
                            <a href="../admin/login.php" class="btn btn-danger mb-3 role-btn admin-btn">
                                <i class="fas fa-user-shield me-2"></i>
                                <span>Admin</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .role-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 200px;
            margin: 0 10px;
        }
        
        .role-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .role-btn:hover::before {
            left: 100%;
        }
        
        .customer-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white !important;
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }
        
        .customer-btn:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(52, 152, 219, 0.4);
            color: white !important;
        }
        
        .admin-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white !important;
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
        }
        
        .admin-btn:hover {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(231, 76, 60, 0.4);
            color: white !important;
        }
        
        .auth-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 50%, #1a1a1a 100%);
        }
        
        .hotel-image-side {
            background: linear-gradient(rgba(52, 152, 219, 0.8), rgba(41, 128, 185, 0.8)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 600"><rect width="400" height="600" fill="%233498db"/><rect x="50" y="100" width="300" height="200" rx="10" fill="%23ffffff" opacity="0.1"/><rect x="80" y="130" width="240" height="20" fill="%23ffffff" opacity="0.3"/><rect x="80" y="160" width="180" height="15" fill="%23ffffff" opacity="0.2"/><rect x="80" y="180" width="200" height="15" fill="%23ffffff" opacity="0.2"/><circle cx="200" cy="400" r="80" fill="%23ffffff" opacity="0.1"/><rect x="150" y="350" width="100" height="100" rx="50" fill="%23ffffff" opacity="0.2"/></svg>');
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
        
        .login-form-side {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .form-container {
            max-width: 500px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .hotel-logo {
            margin-bottom: 2rem;
        }
        
        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .form-subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
            font-weight: 300;
        }
        
        .role-selection {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        @media (max-width: 576px) {
            .form-title {
                font-size: 2rem;
            }
            
            .role-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
                width: 180px;
            }
        }
    </style>
</body>
</html>