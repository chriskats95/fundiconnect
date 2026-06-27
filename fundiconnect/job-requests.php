<?php
/**
 * Job Requests Page - Complete & Clean Version
 * For Fundi Users
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireRole(ROLE_FUNDI);

$currentUser = getCurrentUser();
$user_id = $currentUser['id'];
global $db;

// Fetch Fundi Profile for category matching
$stmt = $db->prepare("SELECT service_category FROM fundi_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$fundiProfile = $stmt->fetch();
$service_category = $fundiProfile['service_category'] ?? '';

// Handle Accept / Decline Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = (int)$_POST['job_id'];
    $action = $_POST['action'];

    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        if ($action === 'accept') {
            $stmt = $db->prepare("UPDATE job_requests SET fundi_id = ?, status = 'accepted', updated_at = NOW() WHERE id = ? AND status = 'pending'");
            $stmt->execute([$user_id, $job_id]);
            setFlashMessage('success', 'Job accepted successfully!');
        } 
        elseif ($action === 'decline') {
            $stmt = $db->prepare("UPDATE job_requests SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND status = 'pending'");
            $stmt->execute([$job_id]);
            setFlashMessage('success', 'Job declined.');
        }
        redirect('job-requests.php');
    }
}

// Fetch All Relevant Pending Jobs
$stmt = $db->prepare("
    SELECT jr.*, u.full_name as client_name, u.profile_image as client_image
    FROM job_requests jr
    JOIN users u ON jr.client_id = u.id
    WHERE jr.status = 'pending'
      AND (
            jr.fundi_id = ? 
         OR (jr.fundi_id IS NULL AND jr.service_category = ?)
         OR (jr.fundi_id IS NULL AND LOWER(jr.service_category) LIKE LOWER(CONCAT('%', ?, '%')))
      )
    ORDER BY jr.is_emergency DESC, jr.created_at DESC
");
$stmt->execute([$user_id, $service_category, $service_category]);
$jobRequests = $stmt->fetchAll();

// Separate Emergency and Regular
$emergencyJobs = [];
$regularJobs = [];

foreach ($jobRequests as $job) {
    if ($job['is_emergency']) {
        $emergencyJobs[] = $job;
    } else {
        $regularJobs[] = $job;
    }
}

$pageTitle = 'Job Requests';
require_once 'includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php displayFlashMessage(); ?>

        <div class="dashboard-header">
            <h1><i class="bi bi-list-check"></i> Job Requests</h1>
            <p>Review and respond to available jobs</p>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h3><?= count($jobRequests) ?></h3>
                    <p>Total Pending Requests</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h3><?= count($emergencyJobs) ?></h3>
                    <p>Emergency Jobs</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h3><?= count($regularJobs) ?></h3>
                    <p>Regular Jobs</p>
                </div>
            </div>
        </div>

        <!-- Emergency Jobs -->
        <?php if (!empty($emergencyJobs)): ?>
        <h5 class="mb-3 text-warning"><i class="bi bi-exclamation-triangle"></i> Emergency Jobs</h5>
        <div class="row g-4 mb-5">
            <?php foreach ($emergencyJobs as $job): ?>
                <div class="col-lg-6">
                    <div class="card-custom border-warning">
                        <div class="d-flex justify-content-between mb-3">
                            <h5><?= htmlspecialchars($job['title']) ?></h5>
                            <span class="badge bg-danger">URGENT</span>
                        </div>
                        <p class="text-muted small"><?= htmlspecialchars(substr($job['description'], 0, 120)) ?>...</p>
                        
                        <div class="d-flex gap-3 mb-3">
                            <small><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['location']) ?></small>
                            <small><i class="bi bi-clock"></i> <?= timeAgo($job['created_at']) ?></small>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="user-info">
                                <img src="<?= getProfileImage(['profile_image' => $job['client_image']]) ?>" class="user-avatar">
                                <div>
                                    <strong><?= htmlspecialchars($job['client_name']) ?></strong>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="job-detail.php?id=<?= $job['id'] ?>" class="btn btn-dark btn-sm">View Details</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                    <button type="submit" name="action" value="accept" class="btn btn-gold btn-sm">Accept</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Regular Jobs -->
        <h5 class="mb-3">All Job Requests</h5>
        <?php if (empty($jobRequests)): ?>
            <div class="card-custom text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                <p>No pending job requests matching your services at the moment.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($regularJobs as $job): ?>
                    <div class="col-lg-6">
                        <div class="card-custom">
                            <h5><?= htmlspecialchars($job['title']) ?></h5>
                            <p class="text-muted small"><?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...</p>
                            
                            <div class="d-flex gap-3 mb-3">
                                <small><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['location']) ?></small>
                                <small><i class="bi bi-clock"></i> <?= timeAgo($job['created_at']) ?></small>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="user-info">
                                    <img src="<?= getProfileImage(['profile_image' => $job['client_image']]) ?>" class="user-avatar">
                                    <div>
                                        <strong><?= htmlspecialchars($job['client_name']) ?></strong>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="job-detail.php?id=<?= $job['id'] ?>" class="btn btn-dark btn-sm">View Details</a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-gold btn-sm">Accept</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>