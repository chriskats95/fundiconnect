<?php
/**
 * Admin Verifications Page
 * Allows admin to approve or reject fundi verification requests
 * Shows pending fundis with their documents and information
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Require admin login
requireRole(ROLE_ADMIN);

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        $fundi_profile_id = (int)$_POST['fundi_profile_id'];
        $user_id = (int)$_POST['user_id'];
        $action = $_POST['action']; // 'approve' or 'reject'
        
        // Fetch fundi details for notification
        $stmt = $db->prepare("
            SELECT u.full_name, u.email, fp.service_category
            FROM users u
            INNER JOIN fundi_profiles fp ON u.id = fp.user_id
            WHERE fp.id = ? AND u.id = ?
        ");
        $stmt->execute([$fundi_profile_id, $user_id]);
        $fundi = $stmt->fetch();
        
        if ($fundi) {
            if ($action === 'approve') {
                // Approve verification
                $stmt = $db->prepare("
                    UPDATE fundi_profiles 
                    SET verification_status = 'approved', updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$fundi_profile_id]);
                
                // Create notification
                $stmt = $db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, created_at) 
                    VALUES (?, ?, ?, 'system', NOW())
                ");
                $stmt->execute([
                    $user_id,
                    'Verification Approved!',
                    'Congratulations! Your fundi profile has been verified. You can now receive job requests.'
                ]);
                
                // TODO: Send email notification
                
                setFlashMessage('success', 'Fundi verified successfully! ' . htmlspecialchars($fundi['full_name']) . ' has been notified.');
                
            } elseif ($action === 'reject') {
                $rejection_reason = sanitize($_POST['rejection_reason']);
                
                // Reject verification
                $stmt = $db->prepare("
                    UPDATE fundi_profiles 
                    SET verification_status = 'rejected', updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$fundi_profile_id]);
                
                // Create notification with reason
                $message = 'Your fundi verification request has been reviewed. ';
                if (!empty($rejection_reason)) {
                    $message .= 'Reason: ' . $rejection_reason;
                } else {
                    $message .= 'Please review your profile information and resubmit.';
                }
                
                $stmt = $db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, created_at) 
                    VALUES (?, ?, ?, 'system', NOW())
                ");
                $stmt->execute([
                    $user_id,
                    'Verification Update',
                    $message
                ]);
                
                // TODO: Send email notification
                
                setFlashMessage('success', 'Verification rejected. ' . htmlspecialchars($fundi['full_name']) . ' has been notified.');
            }
        } else {
            setFlashMessage('error', 'Fundi not found');
        }
    }
    redirect('admin-verifications.php');
}

// Fetch all pending verifications
$stmt = $db->query("
    SELECT fp.*, u.id as user_id, u.full_name, u.email, u.phone, u.profile_image, u.created_at as registered_at
    FROM fundi_profiles fp
    INNER JOIN users u ON fp.user_id = u.id
    WHERE fp.verification_status = 'pending'
    ORDER BY fp.created_at ASC
");
$pendingFundis = $stmt->fetchAll();

// Get statistics
$stmt = $db->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN verification_status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN verification_status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN verification_status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM fundi_profiles
");
$stats = $stmt->fetch();

$pageTitle = 'Fundi Verifications';
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
            <a href="admin-dashboard.php" class="sidebar-brand">
                Fundi<span>Connect</span>
            </a>
            
            <ul class="sidebar-menu">
                <li><a href="admin-dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                <li><a href="admin-users.php"><i class="bi bi-people"></i> Users</a></li>
                <li><a href="admin-users.php?role=fundi"><i class="bi bi-person-badge"></i> Fundis</a></li>
                <li><a href="admin-verifications.php" class="active"><i class="bi bi-patch-check"></i> Verifications</a></li>
                <li><a href="my-jobs.php"><i class="bi bi-briefcase"></i> Job Requests</a></li>
                <li><a href="gallery.php"><i class="bi bi-tags"></i> Categories</a></li>
            </ul>
            
            <div class="sidebar-divider"></div>
            
            <div class="sidebar-section-title">Reports</div>
            <ul class="sidebar-menu">
                <li><a href="notifications.php"><i class="bi bi-graph-up"></i> Analytics</a></li>
                <li><a href="contact.php"><i class="bi bi-envelope"></i> Contact Messages</a></li>
            </ul>
            
            <div class="sidebar-divider"></div>
            
            <ul class="sidebar-menu">
                <li><a href="edit-profile.php"><i class="bi bi-gear"></i> Settings</a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="dashboard-header">
                <div class="dashboard-title">
                    <h1><i class="bi bi-patch-check"></i> Fundi Verifications</h1>
                    <p>Review and approve fundi verification requests</p>
                </div>
                <div class="dashboard-actions">
                    <a href="admin-users.php?role=fundi" class="btn btn-dark">
                        <i class="bi bi-person-badge"></i> All Fundis
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3><?php echo $stats['total']; ?></h3>
                                <p>Total Fundis</p>
                            </div>
                            <div class="stat-card-icon">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3><?php echo $stats['pending']; ?></h3>
                                <p>Pending Review</p>
                            </div>
                            <div class="stat-card-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3><?php echo $stats['approved']; ?></h3>
                                <p>Verified</p>
                            </div>
                            <div class="stat-card-icon green">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3><?php echo $stats['rejected']; ?></h3>
                                <p>Rejected</p>
                            </div>
                            <div class="stat-card-icon red">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Verifications -->
            <div class="card-custom">
                <h4 class="mb-4">
                    Pending Verifications 
                    <span style="color: var(--gray-500); font-size: 0.9rem; font-weight: 400;">
                        (<?php echo count($pendingFundis); ?> <?php echo count($pendingFundis) == 1 ? 'request' : 'requests'; ?>)
                    </span>
                </h4>

                <?php if (empty($pendingFundis)): ?>
                    <div class="text-center py-5" style="color: var(--gray-400);">
                        <i class="bi bi-patch-check" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="mt-3" style="color: var(--gray-300);">No Pending Verifications</h5>
                        <p>All verification requests have been processed.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pendingFundis as $fundi): ?>
                        <div class="verification-card mb-3" style="background: var(--black-soft);">
                            <div class="row">
                                <!-- Left Column - Fundi Info -->
                                <div class="col-lg-8 mb-3 mb-lg-0">
                                    <!-- Profile Header -->
                                    <div class="d-flex gap-3 mb-3">
                                        <img src="<?php echo !empty($fundi['profile_image']) ? 'uploads/' . htmlspecialchars($fundi['profile_image']) : 'public/placeholder-user.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($fundi['full_name']); ?>"
                                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gray-700);">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($fundi['full_name']); ?></h5>
                                            <p class="mb-2" style="color: var(--gold); font-size: 1rem;">
                                                <i class="bi bi-tag"></i> <?php echo htmlspecialchars($fundi['service_category']); ?>
                                            </p>
                                            <div style="color: var(--gray-400); font-size: 0.9rem;">
                                                <span class="me-3">
                                                    <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($fundi['email']); ?>
                                                </span>
                                                <?php if (!empty($fundi['phone'])): ?>
                                                    <span>
                                                        <i class="bi bi-phone"></i> <?php echo htmlspecialchars($fundi['phone']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Profile Details -->
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <small style="color: var(--gray-400);">Location:</small>
                                            <p style="color: var(--white); margin: 0;">
                                                <?php echo !empty($fundi['location']) ? htmlspecialchars($fundi['location']) : 'Not specified'; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small style="color: var(--gray-400);">Experience:</small>
                                            <p style="color: var(--white); margin: 0;">
                                                <?php echo $fundi['experience_years']; ?> year<?php echo $fundi['experience_years'] != 1 ? 's' : ''; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small style="color: var(--gray-400);">Hourly Rate:</small>
                                            <p style="color: var(--gold); margin: 0;">
                                                <?php echo $fundi['hourly_rate'] ? 'UGX ' . number_format($fundi['hourly_rate'], 0) : 'Not set'; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small style="color: var(--gray-400);">Registered:</small>
                                            <p style="color: var(--white); margin: 0;">
                                                <?php echo timeAgo($fundi['registered_at']); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Bio -->
                                    <?php if (!empty($fundi['bio'])): ?>
                                        <div class="mt-3">
                                            <small style="color: var(--gray-400);">Bio:</small>
                                            <p style="color: var(--gray-300); line-height: 1.6; margin-top: 0.25rem;">
                                                <?php echo nl2br(htmlspecialchars($fundi['bio'])); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Verification Documents -->
                                    <?php if (!empty($fundi['verification_documents'])): ?>
                                        <?php 
                                        $documents = json_decode($fundi['verification_documents'], true);
                                        if ($documents && is_array($documents)):
                                        ?>
                                            <div class="mt-3">
                                                <small style="color: var(--gray-400);">Verification Documents:</small>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    <?php foreach ($documents as $doc): ?>
                                                        <a href="assets/uploads/verification/<?php echo htmlspecialchars($doc); ?>" 
                                                           target="_blank"
                                                           class="btn btn-dark btn-sm">
                                                            <i class="bi bi-file-earmark"></i> View Document
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Right Column - Actions -->
                                <div class="col-lg-4 d-flex flex-column justify-content-center">
                                    <div class="card-custom mb-2" style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success);">
                                        <h6 style="color: var(--success); margin-bottom: 1rem;">
                                            <i class="bi bi-check-circle"></i> Approve Verification
                                        </h6>
                                        <form method="POST" onsubmit="return confirm('Approve this fundi? They will be notified and can start receiving job requests.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="fundi_profile_id" value="<?php echo $fundi['id']; ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $fundi['user_id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-gold w-100">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        </form>
                                    </div>

                                    <div class="card-custom" style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger);">
                                        <h6 style="color: var(--danger); margin-bottom: 1rem;">
                                            <i class="bi bi-x-circle"></i> Reject Verification
                                        </h6>
                                        <button type="button" 
                                                class="btn btn-dark w-100"
                                                onclick="showRejectModal(<?php echo $fundi['id']; ?>, <?php echo $fundi['user_id']; ?>, '<?php echo htmlspecialchars($fundi['full_name'], ENT_QUOTES); ?>')">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </div>

                                    <div class="mt-3">
                                        <a href="fundi-profile.php?id=<?php echo $fundi['user_id']; ?>" 
                                           class="btn btn-dark w-100 btn-sm"
                                           target="_blank">
                                            <i class="bi bi-eye"></i> View Full Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--black-card); border: 1px solid var(--gray-800);">
                <div class="modal-header" style="border-bottom: 1px solid var(--gray-800);">
                    <h5 class="modal-title">
                        <i class="bi bi-x-circle" style="color: var(--danger);"></i> 
                        Reject Verification
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="rejectForm">
                    <div class="modal-body">
                        <p style="color: var(--gray-300);">
                            Are you sure you want to reject the verification for <strong id="fundiName" style="color: var(--white);"></strong>?
                        </p>
                        
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="fundi_profile_id" id="rejectFundiProfileId">
                        <input type="hidden" name="user_id" id="rejectUserId">
                        <input type="hidden" name="action" value="reject">
                        
                        <div class="mb-3">
                            <label class="form-label-custom">Rejection Reason (Optional but recommended)</label>
                            <textarea name="rejection_reason" 
                                      class="form-control-custom" 
                                      rows="4" 
                                      placeholder="Provide a reason for rejection to help the fundi improve their application..."></textarea>
                            <small style="color: var(--gray-400);">
                                This message will be included in the notification sent to the fundi.
                            </small>
                        </div>

                        <div class="alert" style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); margin: 0;">
                            <i class="bi bi-exclamation-triangle"></i> 
                            The fundi will be notified of this rejection.
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--gray-800);">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: var(--danger); color: var(--white); border: none;">
                            <i class="bi bi-x-circle"></i> Confirm Rejection
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
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

        function showRejectModal(fundiProfileId, userId, fundiName) {
            document.getElementById('rejectFundiProfileId').value = fundiProfileId;
            document.getElementById('rejectUserId').value = userId;
            document.getElementById('fundiName').textContent = fundiName;
            rejectModal.show();
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
