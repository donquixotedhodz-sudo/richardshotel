// Authentication Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const authLeft = document.getElementById('authLeft');
    const authRight = document.getElementById('authRight');
    const signupToggle = document.getElementById('signupToggle');
    const loginToggle = document.getElementById('loginToggle');
    const signupForm = document.getElementById('signupForm');
    const loginForm = document.getElementById('loginForm');
    const showSignupBtn = document.getElementById('showSignupBtn');
    const showLoginBtn = document.getElementById('showLoginBtn');

    // Show signup form and hide login form
    function showSignupForm() {
        // Hide signup toggle, show signup form
        signupToggle.style.display = 'none';
        signupForm.style.display = 'block';
        signupForm.classList.add('fade-in');
        
        // Hide login form, show login toggle
        loginForm.style.display = 'none';
        loginToggle.style.display = 'block';
        loginToggle.classList.add('fade-in');
        
        // Update background colors
        authLeft.style.background = 'rgba(255, 255, 255, 0.98)';
        authRight.style.background = 'var(--gradient-primary)';
    }

    // Show login form and hide signup form
    function showLoginForm() {
        // Hide login toggle, show login form
        loginToggle.style.display = 'none';
        loginForm.style.display = 'block';
        loginForm.classList.add('fade-in');
        
        // Hide signup form, show signup toggle
        signupForm.style.display = 'none';
        signupToggle.style.display = 'block';
        signupToggle.classList.add('fade-in');
        
        // Reset background colors
        authLeft.style.background = 'var(--gradient-primary)';
        authRight.style.background = 'rgba(255, 255, 255, 0.98)';
    }

    // Event listeners for toggle buttons
    if (showSignupBtn) {
        showSignupBtn.addEventListener('click', showSignupForm);
    }

    if (showLoginBtn) {
        showLoginBtn.addEventListener('click', showLoginForm);
    }

    // Form validation functions
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validatePassword(password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;
        return passwordRegex.test(password);
    }

    function validatePhone(phone) {
        // Basic phone validation (10-15 digits)
        const phoneRegex = /^[\d\s\-\+\(\)]{10,15}$/;
        return phoneRegex.test(phone.replace(/\s/g, ''));
    }

    // Real-time form validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    const phoneInput = document.getElementById('phone');

    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !validateEmail(this.value)) {
                this.classList.add('is-invalid');
                showFieldError(this, 'Please enter a valid email address');
            } else {
                this.classList.remove('is-invalid');
                hideFieldError(this);
            }
        });
    });

    passwordInputs.forEach(input => {
        if (input.id === 'signupPassword') {
            input.addEventListener('blur', function() {
                if (this.value && !validatePassword(this.value)) {
                    this.classList.add('is-invalid');
                    showFieldError(this, 'Password must be at least 8 characters with uppercase, lowercase, and number');
                } else {
                    this.classList.remove('is-invalid');
                    hideFieldError(this);
                }
            });
        }
    });

    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            if (this.value && !validatePhone(this.value)) {
                this.classList.add('is-invalid');
                showFieldError(this, 'Please enter a valid phone number');
            } else {
                this.classList.remove('is-invalid');
                hideFieldError(this);
            }
        });
    }

    // Password confirmation validation
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const signupPasswordInput = document.getElementById('signupPassword');

    if (confirmPasswordInput && signupPasswordInput) {
        confirmPasswordInput.addEventListener('blur', function() {
            if (this.value && this.value !== signupPasswordInput.value) {
                this.classList.add('is-invalid');
                showFieldError(this, 'Passwords do not match');
            } else {
                this.classList.remove('is-invalid');
                hideFieldError(this);
            }
        });
    }

    // Helper functions for field validation
    function showFieldError(field, message) {
        hideFieldError(field); // Remove existing error first
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.marginTop = '0.25rem';
        
        field.parentNode.appendChild(errorDiv);
    }

    function hideFieldError(field) {
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    // Enhanced form submission with client-side validation
    const signupFormElement = document.getElementById('signupFormElement');
    const loginFormElement = document.getElementById('loginFormElement');

    if (signupFormElement) {
        signupFormElement.addEventListener('submit', function(e) {
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('signupEmail').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;

            let isValid = true;

            // Validate all fields
            if (!firstName) {
                showFieldError(document.getElementById('firstName'), 'First name is required');
                isValid = false;
            }

            if (!lastName) {
                showFieldError(document.getElementById('lastName'), 'Last name is required');
                isValid = false;
            }

            if (!email || !validateEmail(email)) {
                showFieldError(document.getElementById('signupEmail'), 'Please enter a valid email address');
                isValid = false;
            }

            if (!phone || !validatePhone(phone)) {
                showFieldError(document.getElementById('phone'), 'Please enter a valid phone number');
                isValid = false;
            }

            if (!password || !validatePassword(password)) {
                showFieldError(document.getElementById('signupPassword'), 'Password must be at least 8 characters with uppercase, lowercase, and number');
                isValid = false;
            }

            if (password !== confirmPassword) {
                showFieldError(document.getElementById('confirmPassword'), 'Passwords do not match');
                isValid = false;
            }

            if (!agreeTerms) {
                showAlert('danger', 'Please agree to the Terms & Conditions and Privacy Policy');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
    }

    if (loginFormElement) {
        loginFormElement.addEventListener('submit', function(e) {
            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;

            let isValid = true;

            if (!email || !validateEmail(email)) {
                showFieldError(document.getElementById('loginEmail'), 'Please enter a valid email address');
                isValid = false;
            }

            if (!password) {
                showFieldError(document.getElementById('loginPassword'), 'Password is required');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Enhanced alert function (used by inline JavaScript)
    window.showAlert = function(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 100px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.classList.remove('show');
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 150);
            }
        }, 5000);
    };

    // Social login button handlers (placeholder functionality)
    const socialButtons = document.querySelectorAll('.social-btn');
    socialButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.querySelector('i').classList.contains('fa-google') ? 'Google' : 
                           this.querySelector('i').classList.contains('fa-facebook-f') ? 'Facebook' : 'Twitter';
            showAlert('info', `${platform} login is coming soon!`);
        });
    });

    // Add smooth scrolling and animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe form containers for animation
    const formContainers = document.querySelectorAll('.form-container, .toggle-content');
    formContainers.forEach(container => {
        observer.observe(container);
    });

    // Add loading states to forms
    function setFormLoading(form, isLoading) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const inputs = form.querySelectorAll('input, button');
        
        if (isLoading) {
            inputs.forEach(input => input.disabled = true);
            submitBtn.classList.add('loading');
        } else {
            inputs.forEach(input => input.disabled = false);
            submitBtn.classList.remove('loading');
        }
    }

    // Expose setFormLoading for use by inline scripts
    window.setFormLoading = setFormLoading;

    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close any open alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) closeBtn.click();
            });
        }
    });

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    console.log('Authentication page initialized successfully');
});

// Utility functions
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

// Export for potential module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showAlert: window.showAlert,
        setFormLoading: window.setFormLoading
    };
}