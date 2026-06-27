<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$currentUser = getCurrentUser();
$user_id = $currentUser['id'];
$role = $currentUser['role'];

$pageTitle = 'My Jobs';

// Handle role-specific logic
if ($role === ROLE_CLIENT) {
    // === CLIENT VIEW ===
    $client_id = $user_id;
    
    // Cancel job logic (existing code)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
        // ... (keep your existing cancel logic here)
    }

    $filterStatus = $_GET['status'] ?? 'all';

    $query = "SELECT jr.*, u.full_name as fundi_name, u.profile_image as fundi_image 
              FROM job_requests jr LEFT JOIN users u ON jr.fundi_id = u.id 
              WHERE jr.client_id = ?";
    $params = [$client_id];

    if ($filterStatus !== 'all') {
        $query .= " AND jr.status = ?";
        $params[] = $filterStatus;
    }
    $query .= " ORDER BY jr.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll();

    // Counts for stats
    $stmt = $db->prepare("SELECT COUNT(*) as total, 
        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status='accepted' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled
        FROM job_requests WHERE client_id = ?");
    $stmt->execute([$client_id]);
    $counts = $stmt->fetch();

} else {
    // === FUNDI VIEW ===
    $fundi_id = $user_id;

    $stmt = $db->prepare("
        SELECT jr.*, u.full_name as client_name, u.profile_image as client_image
        FROM job_requests jr 
        LEFT JOIN users u ON jr.client_id = u.id 
        WHERE jr.fundi_id = ? 
        ORDER BY jr.created_at DESC
    ");
    $stmt->execute([$fundi_id]);
    $jobs = $stmt->fetchAll();

    // Simple counts for fundi
    $stmt = $db->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status IN ('accepted','in_progress') THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM job_requests WHERE fundi_id = ?");
    $stmt->execute([$fundi_id]);
    $counts = $stmt->fetch();
}

require_once 'includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="dashboard-header">
            <div class="dashboard-title">
                <h1><i class="bi bi-briefcase"></i> My Jobs</h1>
                <p><?= $role === ROLE_CLIENT ? 'Manage your job requests' : 'Jobs you are working on' ?></p>
            </div>
        </div>

        <?php displayFlashMessage(); ?>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <!-- Add appropriate stat cards based on role -->
            <?php if ($role === ROLE_CLIENT): ?>
                <!-- Client stats (keep your existing ones) -->
            <?php else: ?>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h3><?= $counts['total'] ?? 0 ?></h3>
                        <p>Total Jobs</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h3><?= $counts['active'] ?? 0 ?></h3>
                        <p>Active Jobs</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h3><?= $counts['completed'] ?? 0 ?></h3>
                        <p>Completed</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Jobs List -->
        <div class="card-custom">
            <h4 class="mb-4">All Jobs (<?= count($jobs) ?>)</h4>
            
            <?php if (empty($jobs)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-briefcase" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5>No jobs yet</h5>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th><?= $role === ROLE_CLIENT ? 'Fundi' : 'Client' ?></th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($role === ROLE_CLIENT ? ($job['fundi_name'] ?? 'Not Assigned') : ($job['client_name'] ?? 'Unknown')) ?>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span>
                                    </td>
                                    <td><?= timeAgo($job['created_at']) ?></td>
                                    <td>
                                        <a href="job-detail.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-dark">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>