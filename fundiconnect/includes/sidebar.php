<?php
// includes/sidebar.php
// Ensure this is only loaded when a user is logged in
if (!isLoggedIn()) return;

$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['user_role'] ?? 'client'; // Fallback to client
?>

<aside class="sidebar" id="sidebar">
    <a href="index.php" class="sidebar-brand">Fundi<span>Connect</span></a>
    
    <ul class="sidebar-menu">
        <?php if ($role === ROLE_CLIENT): ?>
            <li><a href="client-dashboard.php" class="<?= $current_page == 'client-dashboard.php' ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
            <li><a href="find-fundis.php" class="<?= $current_page == 'find-fundis.php' ? 'active' : '' ?>"><i class="bi bi-search"></i> Find Fundis</a></li>
            <li><a href="my-jobs.php" class="<?= $current_page == 'my-jobs.php' ? 'active' : '' ?>"><i class="bi bi-briefcase"></i> My Job Requests</a></li>
            <li><a href="post-job.php" class="<?= $current_page == 'post-job.php' ? 'active' : '' ?>"><i class="bi bi-plus-circle"></i> Post a Job</a></li>

        <?php elseif ($role === ROLE_FUNDI): ?>
            <li><a href="fundi-dashboard.php" class="<?= $current_page == 'fundi-dashboard.php' ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
            <li><a href="job-requests.php" class="<?= $current_page == 'job-requests.php' ? 'active' : '' ?>"><i class="bi bi-inbox"></i> Job Requests</a></li>
            <li><a href="my-jobs.php" class="<?= $current_page == 'my-jobs.php' ? 'active' : '' ?>"><i class="bi bi-briefcase"></i> My Jobs</a></li>
            <li><a href="portfolio.php" class="<?= $current_page == 'portfolio.php' ? 'active' : '' ?>"><i class="bi bi-images"></i> Portfolio</a></li>

        <?php elseif ($role === ROLE_ADMIN): ?>
            <div class="d-flex align-items-center gap-2 mb-4 px-3 py-2 rounded" style="background: rgba(212, 175, 55, 0.1);">
                <i class="bi bi-shield-check" style="color: var(--gold);"></i>
                <small style="color: var(--gold);">Admin Panel</small>
            </div>
            <li><a href="admin-dashboard.php" class="<?= $current_page == 'admin-dashboard.php' ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
            <li><a href="admin-users.php" class="<?= $current_page == 'admin-users.php' ? 'active' : '' ?>"><i class="bi bi-people"></i> Users</a></li>
            <li><a href="admin-verifications.php" class="<?= $current_page == 'admin-verifications.php' ? 'active' : '' ?>"><i class="bi bi-patch-check"></i> Verifications</a></li>
        <?php endif; ?>
        
        <li><a href="job-detail.php" class="<?= $current_page == 'job-detail.php' ? 'active' : '' ?>"><i class="bi bi-chat-dots"></i> Messages</a></li>
    </ul>
    
    <div class="sidebar-divider"></div>
    <p class="sidebar-section-title">Account</p>
    
    <ul class="sidebar-menu">
        <?php if ($role === ROLE_FUNDI): ?>
            <li><a href="fundi-profile.php?id=<?= $_SESSION['user_id'] ?>"><i class="bi bi-person"></i> My Public Profile</a></li>
        <?php endif; ?>
        <li><a href="notifications.php" class="<?= $current_page == 'notifications.php' ? 'active' : '' ?>"><i class="bi bi-bell"></i> Notifications</a></li>
        <li><a href="edit-profile.php" class="<?= $current_page == 'edit-profile.php' ? 'active' : '' ?>"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>
    
    <div class="sidebar-divider"></div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="logout.php" class="text-danger">
                <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>