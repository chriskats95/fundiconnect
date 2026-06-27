<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (hasRole(ROLE_ADMIN)) {
        redirect('admin-dashboard.php');
    } elseif (hasRole(ROLE_FUNDI)) {
        redirect('fundi-dashboard.php');
    } else {
        redirect('client-dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to FundiConnect - Access your account and connect with verified skilled workers.">
    <title>Login | FundiConnect</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <!-- Left Side - Info Panel -->
        <div class="auth-sidebar">
            <div class="auth-sidebar-content">
                <a href="index.php" class="navbar-brand mb-5 d-block">Fundi<span>Connect</span></a>
                
                <h2>Welcome Back!</h2>
                <p>Sign in to access your dashboard, manage job requests, and connect with skilled workers or clients.</p>
                
                <ul class="auth-features">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Track your job requests in real-time</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Message fundis directly through the platform</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Access your booking history and receipts</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Rate and review completed services</span>
                    </li>
                </ul>
                
                <div class="mt-5 pt-4 border-top border-secondary">
                    <p class="text-muted small mb-2">Trusted by</p>
                    <div class="d-flex align-items-center gap-4">
                        <div class="text-center">
                            <h4 class="mb-0" style="color: var(--gold);">10K+</h4>
                            <small class="text-muted">Users</small>
                        </div>
                        <div class="text-center">
                            <h4 class="mb-0" style="color: var(--gold);">5K+</h4>
                            <small class="text-muted">Fundis</small>
                        </div>
                        <div class="text-center">
                            <h4 class="mb-0" style="color: var(--gold);">25K+</h4>
                            <small class="text-muted">Jobs Done</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="auth-main">
            <div class="auth-box">
                <h3>Sign In</h3>
                <p>Enter your credentials to access your account</p>
                
                <?php displayFlashMessage(); ?>
                
                <div id="loginError" class="alert alert-danger d-none"></div>
                
                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label-custom">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-custom" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-custom">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control form-control-custom" id="password" placeholder="Enter your password" required>
                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label text-muted" for="remember">Remember me</label>
                        </div>
                        <a href="#" class="text-gold" style="color: var(--gold);">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-gold w-100 mb-3">
                        Sign In <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                    
                    <div class="auth-divider">
                        <span>or continue with</span>
                    </div>
                    
                    <div class="d-flex gap-3 mb-4">
                        <button type="button" class="btn btn-dark flex-fill">
                            <i class="bi bi-google me-2"></i> Google
                        </button>
                        <button type="button" class="btn btn-dark flex-fill">
                            <i class="bi bi-facebook me-2"></i> Facebook
                        </button>
                    </div>
                    
                    <p class="text-center text-muted mb-0">
                        Don't have an account? <a href="register.php" style="color: var(--gold);">Create Account</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const errorDiv = document.getElementById('loginError');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...';
            errorDiv.classList.add('d-none');
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    errorDiv.textContent = result.message;
                    errorDiv.classList.remove('d-none');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Sign In <i class="bi bi-arrow-right ms-2"></i>';
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Sign In <i class="bi bi-arrow-right ms-2"></i>';
            }
        });
    </script>
</body>
</html>
