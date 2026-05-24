<?php
/**
 * Session Checker
 * Shows current session information for debugging
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Info - FundiConnect</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #2563eb; margin-top: 0; }
        .status {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .logged-in {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }
        .logged-out {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }
        pre {
            background: #1f2937;
            color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
        }
        .btn:hover { background: #1d4ed8; }
        .btn-danger {
            background: #dc2626;
        }
        .btn-danger:hover { background: #b91c1c; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Session Information</h1>
        
        <?php if (isLoggedIn()): ?>
            <div class="status logged-in">
                <strong>✓ You are currently logged in</strong>
            </div>
            
            <h3>Current User Information:</h3>
            <table>
                <tr>
                    <th>Property</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td><strong>User ID</strong></td>
                    <td><?php echo htmlspecialchars($_SESSION['user_id'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>Name</strong></td>
                    <td><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>Role</strong></td>
                    <td><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'N/A'); ?></td>
                </tr>
            </table>
            
            <h3>Full Session Data:</h3>
            <pre><?php print_r($_SESSION); ?></pre>
            
            <div style="margin-top: 30px;">
                <a href="logout.php" class="btn btn-danger">Logout</a>
                <?php if (hasRole(ROLE_ADMIN)): ?>
                    <a href="admin-dashboard.php" class="btn">Go to Admin Dashboard</a>
                <?php elseif (hasRole(ROLE_FUNDI)): ?>
                    <a href="fundi-dashboard.php" class="btn">Go to Fundi Dashboard</a>
                <?php else: ?>
                    <a href="client-dashboard.php" class="btn">Go to Client Dashboard</a>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <div class="status logged-out">
                <strong>✗ You are not logged in</strong>
            </div>
            
            <h3>Session Data:</h3>
            <pre><?php print_r($_SESSION); ?></pre>
            
            <div style="margin-top: 30px;">
                <a href="login.php" class="btn">Go to Login</a>
                <a href="register.php" class="btn">Go to Register</a>
            </div>
        <?php endif; ?>
        
        <hr style="margin: 30px 0;">
        
        <h3>Quick Actions:</h3>
        <a href="index.php" class="btn">Home Page</a>
        <a href="test_login.php" class="btn">Test Login API</a>
        <a href="create_admin.php" class="btn">Create Admin</a>
    </div>
</body>
</html>
