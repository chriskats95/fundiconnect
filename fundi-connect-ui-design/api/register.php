<?php
/**
 * Registration API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$fullName = sanitize($_POST['full_name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$role = sanitize($_POST['role'] ?? ROLE_CLIENT);

// Validate role
if (!in_array($role, [ROLE_CLIENT, ROLE_FUNDI])) {
    $role = ROLE_CLIENT;
}

// Attempt registration
$result = registerUser($email, $password, $fullName, $phone, $role);

if ($result['success']) {
    http_response_code(201);
    
    // Auto-login after registration
    loginUser($email, $password);
    
    // Determine redirect URL based on role
    $redirectUrl = 'index.php';
    if ($role === ROLE_FUNDI) {
        $redirectUrl = 'fundi-dashboard.php';
    } elseif ($role === ROLE_CLIENT) {
        $redirectUrl = 'client-dashboard.php';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $result['message'],
        'redirect' => $redirectUrl
    ]);
} else {
    http_response_code(400);
    echo json_encode($result);
}
?>
