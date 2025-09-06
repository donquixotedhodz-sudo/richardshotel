// Authentication Page JavaScript

// DOM Elements
const signupToggleBtn = document.getElementById('signupToggle');
const loginToggleBtn = document.getElementById('loginToggle');
const signupToggleContent = document.querySelector('.signup-toggle');
const loginToggleContent = document.querySelector('.login-toggle');
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');
const authLeft = document.querySelector('.auth-left');
const authRight = document.querySelector('.auth-right');

// State Management
let isLoginView = true;
let isTransitioning = false;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeAuthPage();
    setupEventListeners();
    setupFormValidation();
});

// Initialize Authentication Page
function initializeAuthPage() {
    // Set initial state
    showLoginView();
    
    // Add fade-in animation to initial content
    setTimeout(() => {
        document.querySelector('.auth-section').style.opacity = '1';
    }, 100);
}

// Setup Event Listeners
function setupEventListeners() {
    // Toggle buttons
    if (signupToggleBtn) {
        signupToggleBtn.addEventListener('click', () => {
            if (!isTransitioning && isLoginView) {
                switchToSignup();
            }
        });
    }
    
    if (loginToggleBtn) {
        loginToggleBtn.addEventListener('click', () => {
            if (!isTransitioning && !isLoginView) {
                switchToLogin();
            }
        });
    }
    
    // Form submissions
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
    if (signupForm) {
        signupForm.addEventListener('submit', handleSignupSubmit);
    }
    
    // Social login buttons
    document.querySelectorAll('.social-btn').forEach(btn => {
        btn.addEventListener('click', handleSocialLogin);
    });
    
    // Input focus effects
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', handleInputFocus);
        input.addEventListener('blur', handleInputBlur);
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', handleKeyboardNavigation);
}

// Switch to Signup View
function switchToSignup() {
    if (isTransitioning) return;
    
    isTransitioning = true;
    isLoginView = false;
    
    // Add transition classes
    signupToggleContent.classList.add('slide-out');
    loginForm.classList.add('slide-out-right');
    
    // Change background colors with transition
    authLeft.style.background = 'rgba(255, 255, 255, 0.95)';
    authRight.style.background = 'linear-gradient(135deg, rgba(196, 30, 58, 0.9) 0%, rgba(196, 30, 58, 0.7) 100%)';
    
    setTimeout(() => {
        // Hide login elements
        signupToggleContent.style.display = 'none';
        loginForm.style.display = 'none';
        
        // Show signup elements
        loginToggleContent.style.display = 'block';
        signupForm.style.display = 'block';
        
        // Add slide-in classes
        setTimeout(() => {
            loginToggleContent.classList.add('slide-in');
            signupForm.classList.add('slide-in-left');
            
            setTimeout(() => {
                isTransitioning = false;
                // Clean up classes
                cleanupTransitionClasses();
            }, 400);
        }, 30);
    }, 200);
}

// Switch to Login View
function switchToLogin() {
    if (isTransitioning) return;
    
    isTransitioning = true;
    isLoginView = true;
    
    // Add transition classes
    loginToggleContent.classList.add('slide-out');
    signupForm.classList.add('slide-out-left');
    
    // Change background colors with transition
    authLeft.style.background = 'linear-gradient(135deg, rgba(196, 30, 58, 0.9) 0%, rgba(196, 30, 58, 0.7) 100%)';
    authRight.style.background = 'rgba(255, 255, 255, 0.95)';
    
    setTimeout(() => {
        // Hide signup elements
        loginToggleContent.style.display = 'none';
        signupForm.style.display = 'none';
        
        // Show login elements
        signupToggleContent.style.display = 'block';
        loginForm.style.display = 'block';
        
        // Add slide-in classes
        setTimeout(() => {
            signupToggleContent.classList.remove('slide-out');
            loginForm.classList.add('slide-in-right');
            
            setTimeout(() => {
                isTransitioning = false;
                // Clean up classes
                cleanupTransitionClasses();
            }, 400);
        }, 30);
    }, 200);
}

// Show Login View (Initial)
function showLoginView() {
    // Hide signup elements
    if (loginToggleContent) loginToggleContent.style.display = 'none';
    if (signupForm) signupForm.style.display = 'none';
    
    // Show login elements
    if (signupToggleContent) signupToggleContent.style.display = 'block';
    if (loginForm) loginForm.style.display = 'block';
    
    // Set initial background colors
    if (authLeft) authLeft.style.background = 'linear-gradient(135deg, rgba(196, 30, 58, 0.9) 0%, rgba(196, 30, 58, 0.7) 100%)';
    if (authRight) authRight.style.background = 'rgba(255, 255, 255, 0.95)';
}

// Clean up transition classes
function cleanupTransitionClasses() {
    const elements = [
        signupToggleContent,
        loginToggleContent,
        loginForm,
        signupForm
    ];
    
    elements.forEach(element => {
        if (element) {
            element.classList.remove(
                'slide-out',
                'slide-in',
                'slide-out-left',
                'slide-out-right',
                'slide-in-left',
                'slide-in-right'
            );
        }
    });
}

// Handle Login Form Submission
function handleLoginSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const email = formData.get('email');
    const password = formData.get('password');
    const remember = formData.get('remember');
    
    // Add loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Signing In...';
    
    // Simulate API call
    setTimeout(() => {
        // Remove loading state
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        // Show success message
        showNotification('Login successful! Redirecting...', 'success');
        
        // Redirect after delay
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 1500);
    }, 2000);
}

// Handle Signup Form Submission
function handleSignupSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const firstName = formData.get('firstName');
    const lastName = formData.get('lastName');
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirmPassword');
    const terms = formData.get('terms');
    
    // Validate passwords match
    if (password !== confirmPassword) {
        showNotification('Passwords do not match!', 'error');
        return;
    }
    
    // Validate terms acceptance
    if (!terms) {
        showNotification('Please accept the terms and conditions!', 'error');
        return;
    }
    
    // Add loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating Account...';
    
    // Simulate API call
    setTimeout(() => {
        // Remove loading state
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        // Show success message
        showNotification('Account created successfully! Please login.', 'success');
        
        // Switch to login after delay
        setTimeout(() => {
            switchToLogin();
        }, 1500);
    }, 2000);
}

// Handle Social Login
function handleSocialLogin(e) {
    e.preventDefault();
    const provider = e.target.closest('.social-btn').dataset.provider;
    
    showNotification(`Redirecting to ${provider}...`, 'info');
    
    // Simulate social login redirect
    setTimeout(() => {
        // In a real app, this would redirect to the social provider
        console.log(`Social login with ${provider}`);
    }, 1000);
}

// Handle Input Focus Effects
function handleInputFocus(e) {
    const formGroup = e.target.closest('.form-floating');
    if (formGroup) {
        formGroup.classList.add('focused');
    }
}

function handleInputBlur(e) {
    const formGroup = e.target.closest('.form-floating');
    if (formGroup && !e.target.value) {
        formGroup.classList.remove('focused');
    }
}

// Handle Keyboard Navigation
function handleKeyboardNavigation(e) {
    // ESC key to close any modals or reset forms
    if (e.key === 'Escape') {
        // Reset any error states
        document.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
    }
    
    // Enter key on toggle buttons
    if (e.key === 'Enter' && e.target.classList.contains('btn-outline-light')) {
        e.target.click();
    }
}

// Form Validation
function setupFormValidation() {
    // Real-time validation for email fields
    document.querySelectorAll('input[type="email"]').forEach(input => {
        input.addEventListener('blur', validateEmail);
        input.addEventListener('input', clearValidationError);
    });
    
    // Real-time validation for password fields
    document.querySelectorAll('input[type="password"]').forEach(input => {
        input.addEventListener('blur', validatePassword);
        input.addEventListener('input', clearValidationError);
    });
    
    // Confirm password validation
    const confirmPasswordInput = document.querySelector('input[name="confirmPassword"]');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('blur', validateConfirmPassword);
        confirmPasswordInput.addEventListener('input', clearValidationError);
    }
}

// Validate Email
function validateEmail(e) {
    const email = e.target.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        showFieldError(e.target, 'Please enter a valid email address');
    }
}

// Validate Password
function validatePassword(e) {
    const password = e.target.value;
    
    if (password && password.length < 6) {
        showFieldError(e.target, 'Password must be at least 6 characters long');
    }
}

// Validate Confirm Password
function validateConfirmPassword(e) {
    const confirmPassword = e.target.value;
    const password = document.querySelector('input[name="password"]').value;
    
    if (confirmPassword && password !== confirmPassword) {
        showFieldError(e.target, 'Passwords do not match');
    }
}

// Show Field Error
function showFieldError(input, message) {
    input.classList.add('is-invalid');
    
    // Remove existing error message
    const existingError = input.parentNode.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    input.parentNode.appendChild(errorDiv);
}

// Clear Validation Error
function clearValidationError(e) {
    e.target.classList.remove('is-invalid');
    const errorMessage = e.target.parentNode.querySelector('.invalid-feedback');
    if (errorMessage) {
        errorMessage.remove();
    }
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.auth-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `auth-notification alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        animation: slideInRight 0.3s ease;
    `;
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${getNotificationIcon(type)} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('fade');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
}

// Get Notification Icon
function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        default: return 'info-circle';
    }
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add CSS for slideInRight animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
`;
document.head.appendChild(style);

// Export functions for potential external use
window.authFunctions = {
    switchToSignup,
    switchToLogin,
    showNotification
};