<?php
/**
 * Change Password Page
 * Allows logged-in users to change their password
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Require login
requireLogin();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters';
    } else {
        // Attempt to change password
        $result = changePassword($_SESSION['user_id'], $currentPassword, $newPassword);
        
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - FundiConnect</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .password-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .password-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        .password-card h2 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        .user-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="password-page">
        <div class="password-card">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock" style="font-size: 48px; color: #667eea;"></i>
                <h2 class="mt-3">Change Password</h2>
                <p class="text-muted">Update your account password</p>
            </div>
            
            <div class="user-info">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle" style="font-size: 40px; color: #667eea;"></i>
                    <div class="ms-3">
                        <strong><?php echo htmlspecialchars($currentUser['full_name']); ?></strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($currentUser['email']); ?></small><br>
                        <span class="badge bg-primary"><?php echo ucfirst($currentUser['role']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <div class="position-relative">
                        <input type="password" name="current_password" class="form-control" id="currentPassword" required>
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('currentPassword', this)"></i>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="position-relative">
                        <input type="password" name="new_password" class="form-control" id="newPassword" required minlength="6">
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('newPassword', this)"></i>
                    </div>
                    <small class="text-muted">Must be at least 6 characters</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Confirm New Password</label>
                    <div class="position-relative">
                        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required minlength="6">
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('confirmPassword', this)"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-shield-check me-2"></i>Change Password
                </button>
                
                <div class="text-center">
                    <?php if (hasRole(ROLE_ADMIN)): ?>
                        <a href="admin-dashboard.php" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    <?php elseif (hasRole(ROLE_FUNDI)): ?>
                        <a href="fundi-dashboard.php" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="client-dashboard.php" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
        
        // Validate passwords match
        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
            }
        });
    </script>
</body>
</html>
