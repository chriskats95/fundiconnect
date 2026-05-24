<?php
/**
 * Application Configuration
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application Settings
define('APP_NAME', 'FundiConnect');
define('APP_URL', 'http://localhost/fundi-connect-ui-design');
define('APP_VERSION', '1.0.0');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Security
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 12);

// Pagination
define('ITEMS_PER_PAGE', 10);

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CLIENT', 'client');
define('ROLE_FUNDI', 'fundi');

// Job Status
define('JOB_STATUS_PENDING', 'pending');
define('JOB_STATUS_ACCEPTED', 'accepted');
define('JOB_STATUS_IN_PROGRESS', 'in_progress');
define('JOB_STATUS_COMPLETED', 'completed');
define('JOB_STATUS_CANCELLED', 'cancelled');

// Verification Status
define('VERIFICATION_PENDING', 'pending');
define('VERIFICATION_APPROVED', 'approved');
define('VERIFICATION_REJECTED', 'rejected');

// Timezone
date_default_timezone_set('Africa/Kampala');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/database.php';
?>
