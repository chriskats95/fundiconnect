<?php
/**
 * Admin Users Page
 * Allows admin to view, search, filter, and manage all users
 * Actions: View Profile, Suspend, Activate, Delete
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Require admin login
requireRole(ROLE_ADMIN);

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        $user_id = (int)$_POST['user_id'];
        $action = $_POST['action'];
        
        // Prevent admin from acting on themselves
        if ($user_id == $_SESSION['user_id']) {
            setFlashMessage('error', 'You cannot perform this action on your own account');
            redirect('admin-users.php');
        }
        
        // Fetch user details
        $stmt = $db->prepare("SELECT full_name, email, role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            if ($action === 'suspend') {
                // Suspend user
                $stmt = $db->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
                $stmt->execute([$user_id]);
                
                // Create notification
                $stmt = $db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, created_at) 
                    VALUES (?, ?, ?, 'system', NOW())
                ");
                $stmt->execute([
                    $user_id,
                    'Account Suspended',
                    'Your account has been suspended. Please contact support for more information.'
                ]);
                
                setFlashMessage('success', 'User ' . htmlspecialchars($user['full_name']) . ' has been suspended.');
                
            } elseif ($action === 'activate') {
                // Activate user
                $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $stmt->execute([$user_id]);
                
                // Create notification
                $stmt = $db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, created_at) 
                    VALUES (?, ?, ?, 'system', NOW())
                ");
                $stmt->execute([
                    $user_id,
                    'Account Activated',
                    'Your account has been reactivated. You can now access all features.'
                ]);
                
                setFlashMessage('success', 'User ' . htmlspecialchars($user['full_name']) . ' has been activated.');
                
            } elseif ($action === 'delete') {
                // Delete user (this will cascade to related records)
                $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                
                setFlashMessage('success', 'User ' . htmlspecialchars($user['full_name']) . ' has been deleted.');
            }
        } else {
            setFlashMessage('error', 'User not found');
        }
    }
    redirect('admin-users.php' . (isset($_GET['role']) ? '?role=' . $_GET['role'] : '') . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['search']) ? '&search=' . $_GET['search'] : ''));
}

// Get filters
$filterRole = isset($_GET['role']) ? $_GET['role'] : 'all';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchQuery = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($filterRole !== 'all') {
    $query .= " AND role = ?";
    $params[] = $filterRole;
}

if ($filterStatus !== 'all') {
    $query .= " AND status = ?";
    $params[] = $filterStatus;
}

if (!empty($searchQuery)) {
    $query .= " AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchTerm = '%' . $searchQuery . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get statistics
$stmt = $db->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN role = 'client' THEN 1 ELSE 0 END) as clients,
        SUM(CASE WHEN role = 'fundi' THEN 1 ELSE 0 END) as fundis,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended
    FROM users
");
$stats = $stmt->fetch();

$pageTitle = 'User Management';
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
                <li><a href="admin-users.php" class="active"><i class="bi bi-people"></i> Users</a></li>
                <li><a href="admin-users.php?role=fundi"><i class="bi bi-person-badge"></i> Fundis</a></li>
                <li><a href="admin-verifications.php"><i class="bi bi-patch-check"></i> Verifications</a></li>
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
                    <h1><i class="bi bi-people"></i> User Management</h1>
                    <p>Manage all platform users</p>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <a href="admin-users.php" style="text-decoration: none;">
                        <div class="stat-card" style="<?php echo ($filterRole === 'all' && $filterStatus === 'all') ? 'border-color: var(--gold);' : ''; ?>">
                            <div class="stat-card-header">
                                <div>
                                    <h3><?php echo $stats['total']; ?></h3>
                                    <p>Total Users</p>
                                </div>
                                <div class="stat-card-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <a href="admin-users.php?role=client" style="text-decoration: none;">
                        <div class="stat-card" style="<?php echo $filterRole === 'client' ? 'border-color: var(--gold);' : ''; ?>">
                            <div class="stat-card-header">
                                <div>
                                    <h3><?php echo $stats['clients']; ?></h3>
                                    <p>Clients</p>
                                </div>
                                <div class="stat-card-icon blue">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <a href="admin-users.php?role=fundi" style="text-decoration: none;">
                        <div class="stat-card" style="<?php echo $filterRole === 'fundi' ? 'border-color: var(--gold);' : ''; ?>">
                            <div class="stat-card-header">
                                <div>
                                    <h3><?php echo $stats['fundis']; ?></h3>
                                    <p>Fundis</p>
                                </div>
                                <div class="stat-card-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--gold);">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <a href="admin-users.php?role=admin" style="text-decoration: none;">
                        <div class="stat-card" style="<?php echo $filterRole === 'admin' ? 'border-color: var(--gold);' : ''; ?>">
                            <div class="stat-card-header">
                                <div>
                                    <h3><?php echo $stats['admins']; ?></h3>
                                    <p>Admins</p>
                                </div>
                                <div class="stat-card-icon red">
                                    <i class="bi bi-shield"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <a href="admin-users.php?status=active" style="text-decoration: none;">
                        <div class="stat-card" style="<?php echo $filterStatus === 'active' ? 'border-color: var(--gold);' : ''; ?>">
                            <div class="stat-card-header">
                                <div>
                                    <h3><?php echo $stats['active']; ?></h3>
                                    <p>Active</p>
                                </div>
                                <div class="stat-card-icon green">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <a href="admin-users.php?status=suspended" style="text-decoration: none;">
                        <div class="stat-card" style="<?php echo $filterStatus === 'suspended' ? 'border-color: var(--gold);' : ''; ?>">
                            <div class="stat-card-header">
                                <div>
                                    <h3><?php echo $stats['suspended']; ?></h3>
                                    <p>Suspended</p>
                                </div>
                                <div class="stat-card-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card-search mb-4">
                <form method="GET" action="admin-users.php" class="search-box">
                    <input type="text" 
                           name="search" 
                           class="form-control-custom" 
                           placeholder="Search by name, email, or phone..." 
                           value="<?php echo htmlspecialchars($searchQuery); ?>"
                           style="flex: 2;">
                    
                    <select name="role" class="form-control-custom">
                        <option value="all" <?php echo $filterRole === 'all' ? 'selected' : ''; ?>>All Roles</option>
                        <option value="client" <?php echo $filterRole === 'client' ? 'selected' : ''; ?>>Clients</option>
                        <option value="fundi" <?php echo $filterRole === 'fundi' ? 'selected' : ''; ?>>Fundis</option>
                        <option value="admin" <?php echo $filterRole === 'admin' ? 'selected' : ''; ?>>Admins</option>
                    </select>
                    
                    <select name="status" class="form-control-custom">
                        <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="active" <?php echo $filterStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="suspended" <?php echo $filterStatus === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        <option value="inactive" <?php echo $filterStatus === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                    
                    <button type="submit" class="btn btn-gold">
                        <i class="bi bi-search"></i> Search
                    </button>
                    
                    <?php if (!empty($searchQuery) || $filterRole !== 'all' || $filterStatus !== 'all'): ?>
                        <a href="admin-users.php" class="btn btn-dark">
                            <i class="bi bi-x"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Users Table -->
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        Users List
                        <span style="color: var(--gray-500); font-size: 0.9rem; font-weight: 400;">
                            (<?php echo count($users); ?> <?php echo count($users) == 1 ? 'user' : 'users'; ?>)
                        </span>
                    </h4>
                </div>

                <?php if (empty($users)): ?>
                    <div class="text-center py-5" style="color: var(--gray-400);">
                        <i class="bi bi-people" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="mt-3" style="color: var(--gray-300);">No Users Found</h5>
                        <p>No users match your search criteria.</p>
                        <a href="admin-users.php" class="btn btn-gold mt-3">
                            <i class="bi bi-arrow-left"></i> View All Users
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <!-- User Info -->
                                        <td>
                                            <div class="user-info">
                                                <img src="<?php echo !empty($user['profile_image']) ? 'uploads/' . htmlspecialchars($user['profile_image']) : 'public/placeholder-user.jpg'; ?>" 
                                                     alt="<?php echo htmlspecialchars($user['full_name']); ?>"
                                                     class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6><?php echo htmlspecialchars($user['full_name']); ?></h6>
                                                    <small><?php echo htmlspecialchars($user['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Role -->
                                        <td>
                                            <span class="badge-status <?php 
                                                echo $user['role'] === 'admin' ? 'badge-cancelled' : 
                                                     ($user['role'] === 'fundi' ? 'badge-pending' : 'badge-active'); 
                                            ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>

                                        <!-- Status -->
                                        <td>
                                            <span class="badge-status <?php 
                                                echo $user['status'] === 'active' ? 'badge-completed' : 
                                                     ($user['status'] === 'suspended' ? 'badge-cancelled' : 'badge-pending'); 
                                            ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>

                                        <!-- Joined Date -->
                                        <td>
                                            <span style="color: var(--black);"><?php echo formatDate($user['created_at'], 'M d, Y'); ?></span>
                                            <br>
                                            <small style="color: var(--gray-500);"><?php echo timeAgo($user['created_at']); ?></small>
                                        </td>

                                        <!-- Last Login -->
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <span style="color: var(--black);"><?php echo formatDate($user['last_login'], 'M d, Y'); ?></span>
                                                <br>
                                                <small style="color: var(--gray-500);"><?php echo timeAgo($user['last_login']); ?></small>
                                            <?php else: ?>
                                                <span style="color: var(--gray-500); font-style: italic;">Never</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Actions -->
                                        <td>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <div class="d-flex gap-1">
                                                    <!-- View Profile -->
                                                    <?php if ($user['role'] === 'fundi'): ?>
                                                        <a href="fundi-profile.php?id=<?php echo $user['id']; ?>" 
                                                           class="btn-icon" 
                                                           title="View Profile"
                                                           target="_blank">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <!-- Suspend/Activate -->
                                                    <?php if ($user['status'] === 'active'): ?>
                                                        <button type="button" 
                                                                class="btn-icon" 
                                                                onclick="showActionModal('suspend', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>')"
                                                                title="Suspend User">
                                                            <i class="bi bi-pause-circle"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" 
                                                                class="btn-icon" 
                                                                style="background: var(--success); border-color: var(--success);"
                                                                onclick="showActionModal('activate', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>')"
                                                                title="Activate User">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Delete -->
                                                    <button type="button" 
                                                            class="btn-icon" 
                                                            style="background: var(--danger); border-color: var(--danger);"
                                                            onclick="showActionModal('delete', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>')"
                                                            title="Delete User">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: var(--gray-500); font-size: 0.85rem; font-style: italic;">
                                                    Your Account
                                                </span>
                                            <?php endif; ?>
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

    <!-- Action Confirmation Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--black-card); border: 1px solid var(--gray-800);">
                <div class="modal-header" style="border-bottom: 1px solid var(--gray-800);">
                    <h5 class="modal-title" id="actionModalTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="actionForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="user_id" id="actionUserId">
                        <input type="hidden" name="action" id="actionType">
                        
                        <p style="color: var(--gray-300);" id="actionMessage"></p>
                        
                        <div class="alert" id="actionWarning" style="margin: 0;"></div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--gray-800);">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="actionButton"></button>
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
        const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));

        function showActionModal(action, userId, userName) {
            document.getElementById('actionUserId').value = userId;
            document.getElementById('actionType').value = action;
            
            const title = document.getElementById('actionModalTitle');
            const message = document.getElementById('actionMessage');
            const warning = document.getElementById('actionWarning');
            const button = document.getElementById('actionButton');
            
            if (action === 'suspend') {
                title.innerHTML = '<i class="bi bi-pause-circle" style="color: var(--warning);"></i> Suspend User';
                message.textContent = `Are you sure you want to suspend ${userName}?`;
                warning.style.cssText = 'background: rgba(245, 158, 11, 0.1); border: 1px solid var(--warning); color: var(--warning);';
                warning.innerHTML = '<i class="bi bi-exclamation-triangle"></i> The user will not be able to log in until reactivated.';
                button.style.cssText = 'background: var(--warning); color: var(--black); border: none;';
                button.innerHTML = '<i class="bi bi-pause-circle"></i> Suspend User';
            } else if (action === 'activate') {
                title.innerHTML = '<i class="bi bi-check-circle" style="color: var(--success);"></i> Activate User';
                message.textContent = `Are you sure you want to activate ${userName}?`;
                warning.style.cssText = 'background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); color: var(--success);';
                warning.innerHTML = '<i class="bi bi-info-circle"></i> The user will be able to log in and access the platform.';
                button.style.cssText = 'background: var(--success); color: var(--white); border: none;';
                button.innerHTML = '<i class="bi bi-check-circle"></i> Activate User';
            } else if (action === 'delete') {
                title.innerHTML = '<i class="bi bi-trash" style="color: var(--danger);"></i> Delete User';
                message.textContent = `Are you sure you want to delete ${userName}?`;
                warning.style.cssText = 'background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger);';
                warning.innerHTML = '<i class="bi bi-exclamation-triangle"></i> <strong>This action cannot be undone!</strong> All user data will be permanently deleted.';
                button.style.cssText = 'background: var(--danger); color: var(--white); border: none;';
                button.innerHTML = '<i class="bi bi-trash"></i> Delete User';
            }
            
            actionModal.show();
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
