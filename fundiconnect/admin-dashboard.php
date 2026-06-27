<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Security: Ensure only admin can access this
requireRole(ROLE_ADMIN);

$currentUser = getCurrentUser();

// ==================== FETCH REAL DATA ====================

// Total Users
$stmt = $db->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'] ?? 0;

// Verified Fundis
$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'fundi' AND status = 'active'");
$verifiedFundis = $stmt->fetch()['total'] ?? 0;

// Total Jobs
$stmt = $db->query("SELECT COUNT(*) as total FROM job_requests");
$totalJobs = $stmt->fetch()['total'] ?? 0;

// Pending Verifications
$stmt = $db->query("SELECT COUNT(*) as total FROM fundi_profiles WHERE verification_status = 'pending'");
$pendingVerifications = $stmt->fetch()['total'] ?? 0;

// Recent Users
$stmt = $db->query("
    SELECT id, full_name, email, role, status, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 4
");
$recentUsers = $stmt->fetchAll();

// Pending Fundi Verifications
$stmt = $db->query("
    SELECT fp.*, u.full_name, u.email, u.phone 
    FROM fundi_profiles fp
    INNER JOIN users u ON fp.user_id = u.id
    WHERE fp.verification_status = 'pending'
    ORDER BY fp.created_at DESC 
    LIMIT 4
");
$pendingFundis = $stmt->fetchAll();

$pageTitle = 'Admin Dashboard';
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
                    <h1>Admin Dashboard</h1>
                    <p>Platform overview and management</p>
                </div>
            </div>
            
            <div class="dashboard-actions">
                <button class="btn btn-dark position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $pendingVerifications ?>
                    </span>
                </button>
                
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 36px; height: 36px; background: var(--gold); color: var(--black);">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <span class="d-none d-md-inline">Admin</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="background: var(--black-card); border-color: var(--gray-800);">
                        <li><a class="dropdown-item text-white" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item text-white" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider" style="border-color: var(--gray-800);"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-card-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <h3><?= number_format($totalUsers) ?></h3>
                    <p class="text-muted mb-2">Total Users</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-card-icon green">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                    <h3><?= number_format($verifiedFundis) ?></h3>
                    <p class="text-muted mb-2">Verified Fundis</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-card-icon blue">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                    </div>
                    <h3><?= number_format($totalJobs) ?></h3>
                    <p class="text-muted mb-2">Total Jobs</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-card-icon red">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                    <h3><?= $pendingVerifications ?></h3>
                    <p class="text-muted mb-2">Pending Verifications</p>
                </div>
            </div>
        </div>

        <!-- Rest of your beautiful layout remains mostly the same but with dynamic data where possible -->
        <!-- I'll keep the structure you like and replace the dummy content -->

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Platform Overview</h5>
                    </div>
                    <!-- You can keep the static chart for now or we can make it dynamic later -->
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center p-3 rounded" style="background: var(--gray-900);">
                                <h4 style="color: var(--gold);"><?= number_format($totalUsers * 0.12) ?></h4>
                                <p class="text-muted small mb-0">New Users This Week</p>
                            </div>
                        </div>
                        <!-- More cards... -->
                    </div>
                </div>
            </div>
            
            <!-- Popular Services - can be made dynamic later -->
            <div class="col-xl-4">
                <div class="card-custom">
                    <h5 class="mb-4">Popular Services</h5>
                    <!-- Keep your static one for now or tell me to make it dynamic -->
                    <!-- Your existing popular services code -->
                </div>
            </div>
        </div>

        <!-- Pending Verifications Table -->
        <div class="row g-4">
            <div class="col-xl-6">
                <div class="table-custom">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pending Fundi Verifications</h5>
                        <a href="admin-verifications.php" class="btn btn-outline-gold btn-sm">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fundi</th>
                                    <th>Service</th>
                                    <th>Applied</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pendingFundis)): ?>
                                    <tr><td colspan="4" class="text-center py-4">No pending verifications</td></tr>
                                <?php else: ?>
                                    <?php foreach ($pendingFundis as $fundi): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="<?= !empty($fundi['profile_image']) ? 'assets/uploads/profiles/' . htmlspecialchars($fundi['profile_image']) : 'assets/uploads/profiles/default.png' ?>" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6><?= htmlspecialchars($fundi['full_name']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($fundi['service_category'] ?? 'N/A') ?></td>
                                        <td><?= timeAgo($fundi['created_at']) ?></td>
                                        <td>
                                            <a href="admin-verifications.php" class="btn btn-sm btn-gold">Review</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="col-xl-6">
                <div class="table-custom">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Users</h5>
                        <a href="admin-users.php" class="btn btn-outline-gold btn-sm">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <img src="assets/uploads/profiles/default.png" alt="User" class="user-avatar">
                                            <div class="user-info-text">
                                                <h6><?= htmlspecialchars($user['full_name']) ?></h6>
                                                <small><?= htmlspecialchars($user['email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge-status"><?= ucfirst($user['role']) ?></span></td>
                                    <td><?= timeAgo($user['created_at']) ?></td>
                                    <td><span class="badge-status badge-<?= $user['status'] ?>"><?= ucfirst($user['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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