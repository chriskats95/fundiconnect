<?php
/**
 * Login API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log the request method for debugging
error_log("Login API - Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Login API - POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Method not allowed',
        'debug' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'request_uri' => $_SERVER['REQUEST_URI']
        ]
    ]);
    exit;
}

// Get POST data
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Attempt login
$result = loginUser($email, $password);

if ($result['success']) {
    http_response_code(200);
    
    // Determine redirect URL based on role
    $redirectUrl = 'index.php';
    if (hasRole(ROLE_ADMIN)) {
        $redirectUrl = 'admin-dashboard.php';
    } elseif (hasRole(ROLE_FUNDI)) {
        $redirectUrl = 'fundi-dashboard.php';
    } elseif (hasRole(ROLE_CLIENT)) {
        $redirectUrl = 'client-dashboard.php';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $result['message'],
        'redirect' => $redirectUrl
    ]);
} else {
    http_response_code(401);
    echo json_encode($result);
}
?>
