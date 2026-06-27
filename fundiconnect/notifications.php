<?php
/**
 * Notifications Page
 * Shows all notifications for the logged-in user
 * Mark as read functionality and mark all as read
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    global $db;
    
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        if ($_POST['action'] === 'mark_read') {
            $notification_id = (int)$_POST['notification_id'];
            
            // Mark single notification as read
            $stmt = $db->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?");
            $stmt->execute([$notification_id, $user_id]);
            
            setFlashMessage('success', 'Notification marked as read');
            
        } elseif ($_POST['action'] === 'mark_all_read') {
            // Mark all notifications as read
            $stmt = $db->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE");
            $stmt->execute([$user_id]);
            
            setFlashMessage('success', 'All notifications marked as read');
        } elseif ($_POST['action'] === 'delete') {
            $notification_id = (int)$_POST['notification_id'];
            
            // Delete notification
            $stmt = $db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
            $stmt->execute([$notification_id, $user_id]);
            
            setFlashMessage('success', 'Notification deleted');
        }
    }
    redirect('notifications.php');
}

// Fetch all notifications for user
$stmt = $db->prepare("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

// Get counts
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN is_read = FALSE THEN 1 ELSE 0 END) as unread
    FROM notifications
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$counts = $stmt->fetch();

// Separate unread and read notifications
$unreadNotifications = array_filter($notifications, fn($n) => !$n['is_read']);
$readNotifications = array_filter($notifications, fn($n) => $n['is_read']);

$pageTitle = 'Notifications';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | FundiConnect</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="<?php echo $user_role === ROLE_CLIENT ? 'client-dashboard.php' : ($user_role === ROLE_FUNDI ? 'fundi-dashboard.php' : 'admin-dashboard.php'); ?>" class="sidebar-brand">
                Fundi<span>Connect</span>
            </a>
            
            <!-- Client Sidebar -->
            <?php if ($user_role === ROLE_CLIENT): ?>
                <ul class="sidebar-menu">
                    <li><a href="client-dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                    <li><a href="find-fundis.php"><i class="bi bi-search"></i> Find Fundis</a></li>
                    <li><a href="my-jobs.php"><i class="bi bi-briefcase"></i> My Jobs</a></li>
                    <li><a href="job-detail.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
                    <li><a href="saved-fundis.php"><i class="bi bi-bookmark"></i> Saved Fundis</a></li>
                </ul>
                
                <div class="sidebar-divider"></div>
                
                <ul class="sidebar-menu">
                    <li><a href="edit-profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                    <li><a href="notifications.php" class="active"><i class="bi bi-bell"></i> Notifications</a></li>
                    <li><a href="edit-profile.php"><i class="bi bi-gear"></i> Settings</a></li>
                </ul>
            
            <!-- Fundi Sidebar -->
            <?php elseif ($user_role === ROLE_FUNDI): ?>
                <ul class="sidebar-menu">
                    <li><a href="fundi-dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                    <li><a href="job-requests.php"><i class="bi bi-inbox"></i> Job Requests</a></li>
                    <li><a href="my-jobs.php"><i class="bi bi-briefcase"></i> My Jobs</a></li>
                    <li><a href="portfolio.php"><i class="bi bi-images"></i> Portfolio</a></li>
                    <li><a href="gallery.php"><i class="bi bi-star"></i> Reviews</a></li>
                    <li><a href="job-detail.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
                </ul>
                
                <div class="sidebar-divider"></div>
                
                <ul class="sidebar-menu">
                    <li><a href="edit-profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                    <li><a href="my-jobs.php"><i class="bi bi-wallet2"></i> Earnings</a></li>
                    <li><a href="notifications.php" class="active"><i class="bi bi-bell"></i> Notifications</a></li>
                    <li><a href="edit-profile.php"><i class="bi bi-gear"></i> Settings</a></li>
                </ul>
            
            <!-- Admin Sidebar -->
            <?php else: ?>
                <ul class="sidebar-menu">
                    <li><a href="admin-dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                    <li><a href="admin-users.php"><i class="bi bi-people"></i> Users</a></li>
                    <li><a href="admin-users.php"><i class="bi bi-person-badge"></i> Fundis</a></li>
                    <li><a href="admin-verifications.php"><i class="bi bi-patch-check"></i> Verifications</a></li>
                    <li><a href="my-jobs.php"><i class="bi bi-briefcase"></i> Job Requests</a></li>
                    <li><a href="gallery.php"><i class="bi bi-tags"></i> Categories</a></li>
                </ul>
                
                <div class="sidebar-divider"></div>
                
                <div class="sidebar-section-title">Reports</div>
                <ul class="sidebar-menu">
                    <li><a href="notifications.php"><i class="bi bi-graph-up"></i> Analytics</a></li>
                </ul>
                
                <div class="sidebar-divider"></div>
                
                <ul class="sidebar-menu">
                    <li><a href="notifications.php" class="active"><i class="bi bi-bell"></i> Notifications</a></li>
                    <li><a href="edit-profile.php"><i class="bi bi-gear"></i> Settings</a></li>
                </ul>
            <?php endif; ?>
            
            <div class="sidebar-divider"></div>
            
            <ul class="sidebar-menu">
                <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="dashboard-header">
                <div class="dashboard-title">
                    <h1><i class="bi bi-bell"></i> Notifications</h1>
                    <p>Stay updated with your latest activities</p>
                </div>
                <div class="dashboard-actions">
                    <?php if ($counts['unread'] > 0): ?>
                        <form method="POST" action="notifications.php" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="action" value="mark_all_read">
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-check-all"></i> Mark All as Read
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3><?php echo $counts['total']; ?></h3>
                                <p>Total Notifications</p>
                            </div>
                            <div class="stat-card-icon">
                                <i class="bi bi-bell"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3><?php echo $counts['unread']; ?></h3>
                                <p>Unread Notifications</p>
                            </div>
                            <div class="stat-card-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <?php if (empty($notifications)): ?>
                <div class="card-custom text-center py-5">
                    <i class="bi bi-bell-slash" style="font-size: 5rem; color: var(--gray-600); opacity: 0.3;"></i>
                    <h4 class="mt-4" style="color: var(--gray-300);">No Notifications Yet</h4>
                    <p style="color: var(--gray-400);">
                        You're all caught up! New notifications will appear here when you have activity.
                    </p>
                </div>
            <?php else: ?>
                
                <!-- Unread Notifications -->
                <?php if (!empty($unreadNotifications)): ?>
                    <div class="card-custom mb-4">
                        <h4 class="mb-3">
                            Unread Notifications
                            <span style="color: var(--gray-500); font-size: 0.9rem; font-weight: 400;">
                                (<?php echo count($unreadNotifications); ?>)
                            </span>
                        </h4>

                        <?php foreach ($unreadNotifications as $notification): ?>
                            <div class="notification-item unread">
                                <div class="notification-icon <?php echo $notification['type']; ?>">
                                    <?php
                                    $iconClass = 'bi-bell';
                                    switch ($notification['type']) {
                                        case 'job':
                                            $iconClass = 'bi-briefcase';
                                            break;
                                        case 'payment':
                                            $iconClass = 'bi-currency-dollar';
                                            break;
                                        case 'review':
                                            $iconClass = 'bi-star';
                                            break;
                                        case 'system':
                                            $iconClass = 'bi-info-circle';
                                            break;
                                        case 'alert':
                                            $iconClass = 'bi-exclamation-triangle';
                                            break;
                                    }
                                    ?>
                                    <i class="bi <?php echo $iconClass; ?>"></i>
                                </div>

                                <div class="notification-content flex-grow-1">
                                    <h6><?php echo htmlspecialchars($notification['title']); ?></h6>
                                    <p><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                </div>

                                <div class="notification-time">
                                    <?php echo timeAgo($notification['created_at']); ?>
                                </div>

                                <div class="d-flex gap-2 align-items-center">
                                    <form method="POST" action="notifications.php" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                        <button type="submit" 
                                                class="btn btn-dark btn-sm" 
                                                title="Mark as Read">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" 
                                            class="btn btn-sm" 
                                            style="background: var(--danger); color: var(--white); border: none;"
                                            onclick="confirmDelete(<?php echo $notification['id']; ?>, '<?php echo htmlspecialchars($notification['title'], ENT_QUOTES); ?>')"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Read Notifications -->
                <?php if (!empty($readNotifications)): ?>
                    <div class="card-custom">
                        <h4 class="mb-3">
                            Earlier Notifications
                            <span style="color: var(--gray-500); font-size: 0.9rem; font-weight: 400;">
                                (<?php echo count($readNotifications); ?>)
                            </span>
                        </h4>

                        <?php foreach ($readNotifications as $notification): ?>
                            <div class="notification-item">
                                <div class="notification-icon <?php echo $notification['type']; ?>">
                                    <?php
                                    $iconClass = 'bi-bell';
                                    switch ($notification['type']) {
                                        case 'job':
                                            $iconClass = 'bi-briefcase';
                                            break;
                                        case 'payment':
                                            $iconClass = 'bi-currency-dollar';
                                            break;
                                        case 'review':
                                            $iconClass = 'bi-star';
                                            break;
                                        case 'system':
                                            $iconClass = 'bi-info-circle';
                                            break;
                                        case 'alert':
                                            $iconClass = 'bi-exclamation-triangle';
                                            break;
                                    }
                                    ?>
                                    <i class="bi <?php echo $iconClass; ?>"></i>
                                </div>

                                <div class="notification-content flex-grow-1">
                                    <h6><?php echo htmlspecialchars($notification['title']); ?></h6>
                                    <p><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                </div>

                                <div class="notification-time">
                                    <?php echo timeAgo($notification['created_at']); ?>
                                </div>

                                <div>
                                    <button type="button" 
                                            class="btn btn-sm" 
                                            style="background: var(--danger); color: var(--white); border: none;"
                                            onclick="confirmDelete(<?php echo $notification['id']; ?>, '<?php echo htmlspecialchars($notification['title'], ENT_QUOTES); ?>')"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--black-card); border: 1px solid var(--gray-800);">
                <div class="modal-header" style="border-bottom: 1px solid var(--gray-800);">
                    <h5 class="modal-title">
                        <i class="bi bi-trash" style="color: var(--danger);"></i> Delete Notification
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="notification_id" id="deleteNotificationId">
                        
                        <p style="color: var(--gray-300);">
                            Are you sure you want to delete this notification?
                        </p>
                        <p style="color: var(--gray-400); font-size: 0.9rem;">
                            <strong id="deleteNotificationTitle" style="color: var(--white);"></strong>
                        </p>
                        <div class="alert" style="background: rgba(245, 158, 11, 0.1); border: 1px solid var(--warning); color: var(--warning); margin: 0;">
                            <i class="bi bi-info-circle"></i> 
                            This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--gray-800);">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: var(--danger); color: var(--white); border: none;">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        function confirmDelete(notificationId, notificationTitle) {
            document.getElementById('deleteNotificationId').value = notificationId;
            document.getElementById('deleteNotificationTitle').textContent = notificationTitle;
            deleteModal.show();
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
