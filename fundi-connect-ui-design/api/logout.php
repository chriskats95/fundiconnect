<?php
/**
 * Logout API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$result = logoutUser();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => $result['message'],
    'redirect' => 'index.php'
]);
?>
