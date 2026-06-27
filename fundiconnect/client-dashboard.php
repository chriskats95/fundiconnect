<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Security: Ensure only clients can access this
requireRole(ROLE_CLIENT);

$currentUser = getCurrentUser();
$client_id = $currentUser['id'];

// ==================== FETCH REAL DATA ====================

// 1. Stats
$stmt = $db->prepare("SELECT COUNT(*) as total FROM job_requests WHERE client_id = ?");
$stmt->execute([$client_id]);
$totalJobs = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM job_requests WHERE client_id = ? AND status IN ('pending', 'accepted')");
$stmt->execute([$client_id]);
$pendingJobs = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM job_requests WHERE client_id = ? AND status = 'completed'");
$stmt->execute([$client_id]);
$completedJobs = $stmt->fetch()['total'];

// Saved Fundis (assuming you have a saved_fundis table - create if not exists)
$stmt = $db->prepare("SELECT COUNT(*) as total FROM saved_fundis WHERE client_id = ?");
$stmt->execute([$client_id]);
$savedFundis = $stmt->fetch()['total'] ?? 0;

// 2. Recent Job Requests
$stmt = $db->prepare("
    SELECT jr.*, 
           COALESCE(fp.service_category, 'General') as fundi_category,
           u.full_name as fundi_name,
           u.profile_image as fundi_image
    FROM job_requests jr
    LEFT JOIN users u ON jr.fundi_id = u.id
    LEFT JOIN fundi_profiles fp ON u.id = fp.user_id
    WHERE jr.client_id = ?
    ORDER BY jr.created_at DESC 
    LIMIT 5
");
$stmt->execute([$client_id]);
$recentJobs = $stmt->fetchAll();

// 3. Recent Notifications
$stmt = $db->prepare("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 3
");
$stmt->execute([$client_id]);
$recentNotifications = $stmt->fetchAll();

// Set page title
$pageTitle = 'Client Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <main class="main-content">
        <div class="dashboard-header">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="dashboard-title">
                    <h1>Welcome back, <?= explode(' ', htmlspecialchars($currentUser['full_name']))[0] ?>!</h1>
                    <p>Manage your job requests and find skilled workers</p>
                </div>
            </div>
            
            <div class="dashboard-actions">
                <button class="btn btn-dark position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= count(array_filter($recentNotifications, fn($n) => !$n['is_read'])) ?>
                    </span>
                </button>
                
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <?php 
                        $userImg = getProfileImage($currentUser);
                        ?>
                        <img src="<?= $userImg ?>" alt="User" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                        <span class="d-none d-md-inline"><?= htmlspecialchars($currentUser['full_name']) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="background: var(--black-card); border-color: var(--gray-800);">
                        <li><a class="dropdown-item text-white" href="client-dashboard.php"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider" style="border-color: var(--gray-800);"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon"><i class="bi bi-briefcase"></i></div>
                    </div>
                    <h3><?= number_format($totalJobs) ?></h3>
                    <p>Total Jobs Posted</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon blue"><i class="bi bi-clock-history"></i></div>
                    </div>
                    <h3><?= $pendingJobs ?></h3>
                    <p>Pending Requests</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon green"><i class="bi bi-check-circle"></i></div>
                    </div>
                    <h3><?= $completedJobs ?></h3>
                    <p>Completed Jobs</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon"><i class="bi bi-heart"></i></div>
                    </div>
                    <h3><?= $savedFundis ?></h3>
                    <p>Saved Fundis</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Job Requests -->
            <div class="col-xl-8">
                <div class="table-custom">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Job Requests</h5>
                        <a href="my-jobs.php" class="btn btn-outline-gold btn-sm">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Job Details</th>
                                    <th>Fundi</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentJobs)): ?>
                                    <tr><td colspan="5" class="text-center py-4">No jobs yet. <a href="post-job.php">Post your first job</a></td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentJobs as $job): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($job['title']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars(substr($job['description'], 0, 80)) ?>...</small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($job['fundi_name']): ?>
                                                <div class="user-info">
                                                    <img src="<?= !empty($job['fundi_image']) ? 'assets/uploads/profiles/' . htmlspecialchars($job['fundi_image']) : 'assets/uploads/profiles/default.png' ?>" 
                                                         alt="Fundi" class="user-avatar">
                                                    <div class="user-info-text">
                                                        <h6><?= htmlspecialchars($job['fundi_name']) ?></h6>
                                                        <small><?= htmlspecialchars($job['fundi_category'] ?? 'Fundi') ?></small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted">Not yet assigned</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= timeAgo($job['created_at']) ?></td>
                                        <td>
                                            <span class="badge-status badge-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="job-detail.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-gold">View</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Recent Notifications + Saved Fundis -->
            <div class="col-xl-4">
                <!-- Recent Notifications -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Notifications</h5>
                        <a href="notifications.php" class="text-muted small">View All</a>
                    </div>
                    
                    <?php if (empty($recentNotifications)): ?>
                        <div class="card-custom p-4 text-center">
                            <small class="text-muted">No notifications yet</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentNotifications as $notif): ?>
                            <div class="notification-item <?= !$notif['is_read'] ? 'unread' : '' ?>">
                                <div class="notification-icon job">
                                    <i class="bi bi-briefcase"></i>
                                </div>
                                <div class="notification-content">
                                    <h6><?= htmlspecialchars($notif['title']) ?></h6>
                                    <p><?= htmlspecialchars($notif['message']) ?></p>
                                </div>
                                <span class="notification-time"><?= timeAgo($notif['created_at']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Saved Fundis -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Saved Fundis</h5>
                        <a href="#" class="text-muted small">View All</a>
                    </div>
                    <!-- We'll make this dynamic later once saved_fundis table is ready -->
                    <div class="text-muted small text-center py-4">
                        Saved fundis will appear here
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }
    });
</script>
</body>
</html>