<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Security: Ensure only fundis can access this
requireRole(ROLE_FUNDI);

$currentUser = getCurrentUser();
$user_id = $currentUser['id'];
global $db;

// 1. Fetch Fundi Profile
$stmt = $db->prepare("SELECT * FROM fundi_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$fundiProfile = $stmt->fetch();

$fundi_profile_id = $fundiProfile['id'] ?? null;
$service_category = $fundiProfile['service_category'] ?? 'Uncategorized';

// ==================== FETCH DYNAMIC DATA ====================

// Pending Requests Count (Assigned to this fundi OR unassigned matching their category)
$stmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM job_requests 
    WHERE status = 'pending' 
    AND (fundi_id = ? OR (fundi_id IS NULL AND service_category = ?))
");
$stmt->execute([$user_id, $service_category]);
$pendingRequestsCount = $stmt->fetch()['total'];

// This Month Earnings
$stmt = $db->prepare("
    SELECT SUM(budget) as total 
    FROM job_requests 
    WHERE fundi_id = ? 
    AND status = 'completed' 
    AND MONTH(updated_at) = MONTH(CURRENT_DATE()) 
    AND YEAR(updated_at) = YEAR(CURRENT_DATE())
");
$stmt->execute([$user_id]);
$monthlyEarnings = $stmt->fetch()['total'] ?? 0;

// Success Rate Calculation
$stmt = $db->prepare("
    SELECT 
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status IN ('completed', 'cancelled') THEN 1 ELSE 0 END) as total_finished
    FROM job_requests 
    WHERE fundi_id = ?
");
$stmt->execute([$user_id]);
$rateStats = $stmt->fetch();
$successRate = ($rateStats['total_finished'] > 0) ? round(($rateStats['completed'] / $rateStats['total_finished']) * 100) : 100;

// Recent Job Requests (Limit 3)
$stmt = $db->prepare("
    SELECT jr.*, u.full_name as client_name, u.profile_image as client_image
    FROM job_requests jr
    JOIN users u ON jr.client_id = u.id
    WHERE jr.status = 'pending' 
    AND (jr.fundi_id = ? OR (jr.fundi_id IS NULL AND jr.service_category = ?))
    ORDER BY jr.is_emergency DESC, jr.created_at DESC 
    LIMIT 3
");
$stmt->execute([$user_id, $service_category]);
$newRequests = $stmt->fetchAll();

// Active Jobs (Limit 3)
$stmt = $db->prepare("
    SELECT jr.*, u.full_name as client_name
    FROM job_requests jr
    JOIN users u ON jr.client_id = u.id
    WHERE jr.status IN ('accepted', 'in_progress') AND jr.fundi_id = ?
    ORDER BY jr.updated_at DESC 
    LIMIT 3
");
$stmt->execute([$user_id]);
$activeJobs = $stmt->fetchAll();

// Recent Reviews (Limit 2)
$stmt = $db->prepare("
    SELECT r.*, u.full_name as client_name, u.profile_image as client_image
    FROM reviews r
    JOIN users u ON r.client_id = u.id
    WHERE r.fundi_id = ?
    ORDER BY r.created_at DESC 
    LIMIT 2
");
$stmt->execute([$user_id]);
$recentReviews = $stmt->fetchAll();

// Unread Notifications Count
$stmt = $db->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = FALSE");
$stmt->execute([$user_id]);
$unreadNotifs = $stmt->fetch()['unread'];

// Portfolio Preview (Limit 5)
$portfolioImages = [];
if ($fundi_profile_id) {
    $stmt = $db->prepare("SELECT * FROM portfolio_images WHERE fundi_id = ? ORDER BY uploaded_at DESC LIMIT 5");
    $stmt->execute([$fundi_profile_id]);
    $portfolioImages = $stmt->fetchAll();
}

$pageTitle = 'Fundi Dashboard';
require_once __DIR__. '/includes/header.php';
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
                    <p>Manage your jobs and grow your business</p>
                </div>
            </div>
            
            <div class="dashboard-actions">
                <a href="notifications.php" class="btn btn-dark position-relative">
                    <i class="bi bi-bell"></i>
                    <?php if ($unreadNotifs > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $unreadNotifs ?>
                    </span>
                    <?php endif; ?>
                </a>
                
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <img src="<?= getProfileImage($currentUser) ?>" alt="User" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                        <span class="d-none d-md-inline"><?= htmlspecialchars($currentUser['full_name']) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="background: var(--black-card); border-color: var(--gray-800);">
                        <li><a class="dropdown-item text-white" href="fundi-profile.php?id=<?= $user_id ?>"><i class="bi bi-person me-2"></i> Public Profile</a></li>
                        <li><a class="dropdown-item text-white" href="edit-profile.php"><i class="bi bi-gear me-2"></i> Edit Profile</a></li>
                        <li><hr class="dropdown-divider" style="border-color: var(--gray-800);"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <?php displayFlashMessage(); ?>

        <!-- Profile Header - Fixed -->
        <div class="profile-header mb-4">
            <div class="row align-items-center g-4">
                <div class="col-auto">
                    <img src="<?= getProfileImage($currentUser) ?>" alt="User" class="profile-avatar-large">
                </div>
                <div class="col">
                    <div class="profile-info">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h2 class="mb-0"><?= htmlspecialchars($currentUser['full_name']) ?></h2>
                            <?php if (($fundiProfile['verification_status'] ?? '') === 'approved'): ?>
                                <span class="badge-status badge-verified pulse"><i class="bi bi-patch-check-fill"></i> Verified</span>
                            <?php else: ?>
                                <span class="badge-status badge-pending">Pending Verification</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="profession mb-1">
                            <?= !empty($fundiProfile['service_category']) 
                                ? htmlspecialchars($fundiProfile['service_category']) 
                                : '<span class="text-warning">Please update your service category</span>' ?>
                        </p>
                        
                        <p class="location mb-2">
                            <i class="bi bi-geo-alt"></i> 
                            <?= !empty($fundiProfile['location']) 
                                ? htmlspecialchars($fundiProfile['location']) 
                                : '<span class="text-warning">Location not set - Update your profile</span>' ?>
                        </p>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="edit-profile.php" class="btn btn-gold">
                        <i class="bi bi-pencil-square me-2"></i> Complete Profile
                    </a>
                </div>
            </div>
        </div>

        <?php if (empty($fundiProfile['service_category']) || empty($fundiProfile['location'])): ?>
            <div class="alert alert-warning d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div>
                    <strong>Your profile is incomplete!</strong><br>
                    Please update your service category and location so clients can find you easily.
                </div>
                <a href="edit-profile.php" class="btn btn-gold ms-auto">Update Profile Now</a>
            </div>
        <?php endif; ?>
        
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon"><i class="bi bi-briefcase"></i></div>
                    </div>
                    <h3><?= (int)($fundiProfile['completed_jobs'] ?? 0) ?></h3>
                    <p>Total Jobs Completed</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon blue"><i class="bi bi-clock-history"></i></div>
                    </div>
                    <h3><?= $pendingRequestsCount ?></h3>
                    <p>Pending Requests</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon green"><i class="bi bi-wallet2"></i></div>
                    </div>
                    <h3>UGX <?= number_format($monthlyEarnings) ?></h3>
                    <p>This Month Earnings</p>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    </div>
                    <h3><?= $successRate ?>%</h3>
                    <p>Success Rate</p>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- New Job Requests - Fixed & Reliable Query -->
            <div class="col-xl-8">
                <div class="card-custom p-0 h-100">
                    <div class="p-3 d-flex justify-content-between align-items-center border-bottom" style="border-color: var(--gray-800) !important;">
                        <h5 class="mb-0">New Job Requests</h5>
                        <a href="job-requests.php" class="btn btn-sm btn-outline-gold">View All</a>
                    </div>
                    
                    <div class="p-3">
                        <?php
                        // Reliable Query
                        $stmt = $db->prepare("
                            SELECT jr.*, u.full_name as client_name, u.profile_image as client_image
                            FROM job_requests jr
                            JOIN users u ON jr.client_id = u.id
                            WHERE jr.status = 'pending'
                            AND (
                                    jr.fundi_id = ? 
                                OR (jr.fundi_id IS NULL AND jr.service_category = ?)
                            )
                            ORDER BY jr.is_emergency DESC, jr.created_at DESC 
                            LIMIT 6
                        ");
                        $stmt->execute([$user_id, $service_category]);
                        $newRequests = $stmt->fetchAll();
                        ?>

                        <?php if (empty($newRequests)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 mb-3"></i>
                                <p>No new job requests matching your services yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($newRequests as $job): ?>
                                <div class="card-custom p-3 mb-3" style="background: var(--gray-900); <?= $job['is_emergency'] ? 'border: 1px solid #ffc107;' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($job['title']) ?></h6>
                                            <?php if ($job['is_emergency']): ?>
                                                <span class="badge bg-danger">URGENT</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($job['budget']): ?>
                                            <span style="color: var(--gold);">UGX <?= number_format($job['budget']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="text-muted small mb-2"><?= htmlspecialchars(substr($job['description'], 0, 90)) ?>...</p>
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="user-info">
                                            <img src="<?= getProfileImage(['profile_image' => $job['client_image']]) ?>" class="user-avatar" alt="">
                                            <span><?= htmlspecialchars($job['client_name']) ?></span>
                                        </div>
                                        <a href="job-detail.php?id=<?= $job['id'] ?>" class="btn btn-gold btn-sm">View & Respond</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="card-custom p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Active Jobs</h5>
                        <a href="my-jobs.php" class="text-muted small">View All</a>
                    </div>
                    
                    <?php if (empty($activeJobs)): ?>
                        <p class="text-muted text-center small py-2">No active jobs right now.</p>
                    <?php else: ?>
                        <?php foreach ($activeJobs as $job): ?>
                            <a href="job-detail.php?id=<?= $job['id'] ?>" class="text-decoration-none">
                                <div class="d-flex align-items-center gap-3 p-3 rounded mb-2" style="background: var(--gray-900); transition: 0.2s;">
                                    <div class="stat-card-icon blue" style="width: 40px; height: 40px; font-size: 1rem;">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <div class="flex-grow-1 text-white">
                                        <h6 class="mb-0"><?= htmlspecialchars($job['title']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($job['client_name']) ?></small>
                                    </div>
                                    <span class="badge-status badge-<?= $job['status'] ?>"><?= ucfirst(str_replace('_', ' ', $job['status'])) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="card-custom p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Reviews</h5>
                        <a href="fundi-profile.php?id=<?= $user_id ?>" class="text-muted small">View All</a>
                    </div>
                    
                    <?php if (empty($recentReviews)): ?>
                        <p class="text-muted text-center small py-2">No reviews yet.</p>
                    <?php else: ?>
                        <?php foreach ($recentReviews as $review): ?>
                            <div class="mb-3 pb-3 border-bottom" style="border-color: var(--gray-800) !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="user-info">
                                        <img src="<?= getProfileImage(['profile_image' => $review['client_image']]) ?>" alt="Client" class="user-avatar" style="width: 36px; height: 36px;">
                                        <div class="user-info-text">
                                            <h6 class="mb-0" style="font-size: 0.9rem;"><?= htmlspecialchars($review['client_name']) ?></h6>
                                        </div>
                                    </div>
                                    <span class="text-warning small">
                                        <?php for($i=1; $i<=5; $i++) echo $i <= $review['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                                    </span>
                                </div>
                                <p class="text-muted small mb-0">"<?= htmlspecialchars($review['comment']) ?>"</p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="card-custom p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">My Portfolio</h5>
                        <a href="portfolio.php" class="btn btn-outline-gold btn-sm">Manage</a>
                    </div>
                    
                    <div class="row g-2">
                        <?php if (empty($portfolioImages)): ?>
                            <div class="col-12 text-center text-muted small py-2">No portfolio images uploaded.</div>
                        <?php else: ?>
                            <?php foreach ($portfolioImages as $img): ?>
                                <div class="col-4">
                                    <div class="portfolio-item">
                                        <img src="assets/uploads/portfolio/<?= htmlspecialchars($img['image_path']) ?>" alt="Work">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="col-4">
                            <a href="portfolio.php" class="text-decoration-none">
                                <div class="portfolio-item" style="background: var(--gray-800); display: flex; align-items: center; justify-content: center; height: 80px; border-radius: var(--radius-md); cursor: pointer;">
                                    <div class="text-center">
                                        <i class="bi bi-plus-lg text-muted"></i>
                                        <small class="d-block text-muted">Add</small>
                                    </div>
                                </div>
                            </a>
                        </div>
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