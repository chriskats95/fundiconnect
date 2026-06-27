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
    <meta name="description" content="Create your FundiConnect account - Join as a client or skilled worker today.">
    <title>Register | FundiConnect</title>
    
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
    
    <style>
        .role-card {
            background: var(--black-card);
            border: 2px solid var(--gray-800);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            cursor: pointer;
            transition: var(--transition-normal);
            text-align: center;
        }
        
        .role-card:hover {
            border-color: var(--gold);
        }
        
        .role-card.selected {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.1);
        }
        
        .role-card i {
            font-size: 2.5rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }
        
        .role-card h5 {
            margin-bottom: 0.5rem;
        }
        
        .role-card p {
            color: var(--gray-400);
            font-size: 0.875rem;
            margin: 0;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gray-700);
            transition: var(--transition-normal);
        }
        
        .step-dot.active {
            background: var(--gold);
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
        }
        
        .step-dot.completed {
            background: var(--success);
        }
        
        .form-step {
            display: none;
        }
        
        .form-step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <!-- Left Side - Info Panel -->
        <div class="auth-sidebar">
            <div class="auth-sidebar-content">
                <a href="index.php" class="navbar-brand mb-5 d-block">Fundi<span>Connect</span></a>
                
                <h2>Join FundiConnect</h2>
                <p>Create your account and become part of Uganda's largest network of verified skilled workers and satisfied clients.</p>
                
                <ul class="auth-features">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Free registration for all users</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Access to 5,000+ verified fundis</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Secure payments and job tracking</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>24/7 customer support</span>
                    </li>
                </ul>
                
                <div class="mt-5 pt-4 border-top border-secondary">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&facepad=2" 
                                 alt="User" class="rounded-circle" style="width: 40px; height: 40px; margin-right: -10px; border: 2px solid var(--black-soft);">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&facepad=2" 
                                 alt="User" class="rounded-circle" style="width: 40px; height: 40px; margin-right: -10px; border: 2px solid var(--black-soft);">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&facepad=2" 
                                 alt="User" class="rounded-circle" style="width: 40px; height: 40px; border: 2px solid var(--black-soft);">
                        </div>
                        <div>
                            <p class="mb-0 text-white small">Join 10,000+ users</p>
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-star-fill text-warning small"></i>
                                <span class="text-muted small">4.9/5 rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Registration Form -->
        <div class="auth-main">
            <div class="auth-box" style="max-width: 500px;">
                <div class="step-indicator">
                    <div class="step-dot active" data-step="1"></div>
                    <div class="step-dot" data-step="2"></div>
                    <div class="step-dot" data-step="3"></div>
                </div>
                
                <form id="registerForm">
                    <!-- Step 1: Choose Role -->
                    <div class="form-step active" id="step1">
                        <h3 class="text-center">Choose Your Role</h3>
                        <p class="text-center text-muted mb-4">How would you like to use FundiConnect?</p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="role-card" data-role="client" onclick="selectRole('client')">
                                    <i class="bi bi-person"></i>
                                    <h5>Client</h5>
                                    <p>I need to hire skilled workers for my projects</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="role-card" data-role="fundi" onclick="selectRole('fundi')">
                                    <i class="bi bi-tools"></i>
                                    <h5>Fundi</h5>
                                    <p>I am a skilled worker looking for jobs</p>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="role" id="selectedRole" required>
                        
                        <button type="button" class="btn btn-gold w-100" onclick="nextStep(2)" id="step1Btn" disabled>
                            Continue <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                    
                    <!-- Step 2: Personal Information -->
                    <div class="form-step" id="step2">
                        <h3>Personal Information</h3>
                        <p class="text-muted mb-4">Tell us a bit about yourself</p>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label-custom">First Name</label>
                                <input type="text" class="form-control form-control-custom" name="first_name" placeholder="John" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Last Name</label>
                                <input type="text" class="form-control form-control-custom" name="last_name" placeholder="Mukasa" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 mt-3">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" class="form-control form-control-custom" name="email" placeholder="john@example.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-custom">Phone Number</label>
                            <input type="tel" class="form-control form-control-custom" name="phone" placeholder="+256 700 000 000" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-custom">Location</label>
                            <select class="form-control form-control-custom form-select" name="location" required>
                                <option value="">Select your location</option>
                                <option value="kampala">Kampala</option>
                                <option value="entebbe">Entebbe</option>
                                <option value="jinja">Jinja</option>
                                <option value="mukono">Mukono</option>
                                <option value="wakiso">Wakiso</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <!-- Fundi-specific fields -->
                        <div id="fundiFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label-custom">Primary Service</label>
                                <select class="form-control form-control-custom form-select" name="service_category">
                                    <option value="">Select your primary skill</option>
                                    <option value="plumber">Plumber</option>
                                    <option value="electrician">Electrician</option>
                                    <option value="carpenter">Carpenter</option>
                                    <option value="painter">Painter</option>
                                    <option value="cleaner">Cleaner</option>
                                    <option value="mason">Mason</option>
                                    <option value="welder">Welder</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label-custom">Years of Experience</label>
                                <input type="number" class="form-control form-control-custom" name="experience" placeholder="5" min="0">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-dark flex-fill" onclick="prevStep(1)">
                                <i class="bi bi-arrow-left me-2"></i> Back
                            </button>
                            <button type="button" class="btn btn-gold flex-fill" onclick="nextStep(3)">
                                Continue <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Account Security -->
                    <div class="form-step" id="step3">
                        <h3>Secure Your Account</h3>
                        <p class="text-muted mb-4">Create a strong password for your account</p>
                        
                        <div class="mb-3">
                            <label class="form-label-custom">Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control form-control-custom" name="password" id="password" placeholder="Create a password" required>
                                <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted" onclick="togglePassword('password', 'toggleIcon1')">
                                    <i class="bi bi-eye" id="toggleIcon1"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="progress-custom">
                                    <div class="progress-bar" role="progressbar" id="passwordStrength" style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="passwordStrengthText">Password strength</small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label-custom">Confirm Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control form-control-custom" name="confirm_password" id="confirmPassword" placeholder="Confirm your password" required>
                                <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                                    <i class="bi bi-eye" id="toggleIcon2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label text-muted" for="terms">
                                I agree to the <a href="#" style="color: var(--gold);">Terms of Service</a> and <a href="#" style="color: var(--gold);">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="newsletter">
                            <label class="form-check-label text-muted" for="newsletter">
                                Send me tips and updates about FundiConnect
                            </label>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-dark flex-fill" onclick="prevStep(2)">
                                <i class="bi bi-arrow-left me-2"></i> Back
                            </button>
                            <button type="submit" class="btn btn-gold flex-fill">
                                Create Account <i class="bi bi-check2 ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="auth-divider">
                    <span>or sign up with</span>
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
                    Already have an account? <a href="login.php" style="color: var(--gold);">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentStep = 1;
        let selectedRole = '';

        // Check URL params for role
        const urlParams = new URLSearchParams(window.location.search);
        const roleParam = urlParams.get('role');
        if (roleParam === 'fundi') {
            selectRole('fundi');
        }

        function selectRole(role) {
            selectedRole = role;
            document.getElementById('selectedRole').value = role;
            
            // Update UI
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelector(`[data-role="${role}"]`).classList.add('selected');
            
            // Enable continue button
            document.getElementById('step1Btn').disabled = false;
            
            // Show/hide fundi fields
            if (role === 'fundi') {
                document.getElementById('fundiFields').style.display = 'block';
            } else {
                document.getElementById('fundiFields').style.display = 'none';
            }
        }

        function nextStep(step) {
            document.querySelector(`#step${currentStep}`).classList.remove('active');
            document.querySelector(`#step${step}`).classList.add('active');
            
            // Update step indicators
            document.querySelector(`.step-dot[data-step="${currentStep}"]`).classList.remove('active');
            document.querySelector(`.step-dot[data-step="${currentStep}"]`).classList.add('completed');
            document.querySelector(`.step-dot[data-step="${step}"]`).classList.add('active');
            
            currentStep = step;
        }

        function prevStep(step) {
            document.querySelector(`#step${currentStep}`).classList.remove('active');
            document.querySelector(`#step${step}`).classList.add('active');
            
            // Update step indicators
            document.querySelector(`.step-dot[data-step="${currentStep}"]`).classList.remove('active');
            document.querySelector(`.step-dot[data-step="${step}"]`).classList.remove('completed');
            document.querySelector(`.step-dot[data-step="${step}"]`).classList.add('active');
            
            currentStep = step;
        }

        function togglePassword(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(iconId);
            
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

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let text = '';
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/) || password.match(/[^a-zA-Z0-9]/)) strength += 25;
            
            const progressBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('passwordStrengthText');
            
            progressBar.style.width = strength + '%';
            
            if (strength <= 25) {
                progressBar.style.background = '#EF4444';
                text = 'Weak password';
            } else if (strength <= 50) {
                progressBar.style.background = '#F59E0B';
                text = 'Fair password';
            } else if (strength <= 75) {
                progressBar.style.background = '#3B82F6';
                text = 'Good password';
            } else {
                progressBar.style.background = '#10B981';
                text = 'Strong password';
            }
            
            strengthText.textContent = text;
        });

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            const form = e.target;
            const formData = new FormData(form);
            
            // Combine first and last name
            const firstName = formData.get('first_name');
            const lastName = formData.get('last_name');
            formData.append('full_name', `${firstName} ${lastName}`);
            
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating Account...';
            
            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Registration successful! Redirecting...');
                    window.location.href = result.redirect;
                } else {
                    alert(result.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Create Account <i class="bi bi-check2 ms-2"></i>';
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create Account <i class="bi bi-check2 ms-2"></i>';
            }
        });
    </script>
</body>
</html>

