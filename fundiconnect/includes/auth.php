<?php
/**
 * Authentication Functions
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Register new user
 */
function registerUser($email, $password, $fullName, $phone, $role = ROLE_CLIENT) {
    global $db;
    
    // Validate inputs
    if (empty($email) || empty($password) || empty($fullName)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if (!isValidEmail($email)) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, HASH_ALGO, ['cost' => HASH_COST]);
    
    try {
        $db->beginTransaction();

        // 1. Insert into users table
        $stmt = $db->prepare("
            INSERT INTO users (email, password, full_name, phone, role, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$email, $hashedPassword, $fullName, $phone, $role]);
        
        $newUserId = $db->lastInsertId();

        // 2. FIXED: Automatically generate an empty profile if the user is a fundi
        if ($role === ROLE_FUNDI) {
            $profileStmt = $db->prepare("
                INSERT INTO fundi_profiles (user_id, service_category, verification_status) 
                VALUES (?, 'Uncategorized', 'pending')
            ");
            $profileStmt->execute([$newUserId]);
        }

        $db->commit();
        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $newUserId];

    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Registration Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}
/**
 * Login user
 */
function loginUser($email, $password) {
    global $db;
    
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required'];
    }
    
    // Get user by email
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Check if account is active
    if ($user['status'] !== 'active') {
        return ['success' => false, 'message' => 'Your account is not active'];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];
    
    // Update last login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    return ['success' => true, 'message' => 'Login successful', 'user' => $user];
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        redirect('index.php');
    }
}

/**
 * Change password
 */
function changePassword($userId, $currentPassword, $newPassword) {
    global $db;
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'New password must be at least 6 characters'];
    }
    
    // Get current password hash
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, HASH_ALGO, ['cost' => HASH_COST]);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $userId]);
    
    return ['success' => true, 'message' => 'Password changed successfully'];
}

/**
 * Reset password request
 */
function requestPasswordReset($email) {
    global $db;
    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Don't reveal if email exists
        return ['success' => true, 'message' => 'If the email exists, a reset link has been sent'];
    }
    
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $stmt = $db->prepare("
        INSERT INTO password_resets (user_id, token, expires_at, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$user['id'], $token, $expiry]);
    
    // TODO: Send email with reset link
    // For now, just return the token (in production, send via email)
    
    return ['success' => true, 'message' => 'Password reset link sent', 'token' => $token];
}
?>
