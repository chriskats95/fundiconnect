<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Dashboard - Manage users, verify fundis, and view platform statistics.">
    <title>Admin Dashboard | FundiConnect</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <a href="index.php" class="sidebar-brand">Fundi<span>Connect</span></a>
            
            <div class="d-flex align-items-center gap-2 mb-4 px-3 py-2 rounded" style="background: rgba(212, 175, 55, 0.1);">
                <i class="bi bi-shield-check" style="color: var(--gold);"></i>
                <small style="color: var(--gold);">Admin Panel</small>
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="admin-dashboard.php" class="active">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-tools"></i>
                        <span>Fundis</span>
                        <span class="badge bg-warning ms-auto">12</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-patch-check"></i>
                        <span>Verifications</span>
                        <span class="badge bg-danger ms-auto">8</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-briefcase"></i>
                        <span>Job Requests</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-divider"></div>
            <p class="sidebar-section-title">Reports</p>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <i class="bi bi-graph-up"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-envelope"></i>
                        <span>Contact Messages</span>
                        <span class="badge bg-info ms-auto">5</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-divider"></div>
            <p class="sidebar-section-title">System</p>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-clock-history"></i>
                        <span>Activity Logs</span>
                    </a>
                </li>
                <li>
                    <a href="login.php" class="text-danger">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
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
                            15
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
                            <li><a class="dropdown-item text-danger" href="login.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
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
                            <span class="stat-change positive">
                                <i class="bi bi-arrow-up"></i> 12%
                            </span>
                        </div>
                        <h3>10,458</h3>
                        <p class="text-muted mb-2">Total Users</p>
                        <div class="progress-custom">
                            <div class="progress-bar" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="admin-stat-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-card-icon green">
                                <i class="bi bi-tools"></i>
                            </div>
                            <span class="stat-change positive">
                                <i class="bi bi-arrow-up"></i> 8%
                            </span>
                        </div>
                        <h3>5,234</h3>
                        <p class="text-muted mb-2">Verified Fundis</p>
                        <div class="progress-custom">
                            <div class="progress-bar" style="width: 60%; background: linear-gradient(90deg, var(--success), #34D399);"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="admin-stat-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-card-icon blue">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                            <span class="stat-change positive">
                                <i class="bi bi-arrow-up"></i> 24%
                            </span>
                        </div>
                        <h3>25,890</h3>
                        <p class="text-muted mb-2">Total Jobs</p>
                        <div class="progress-custom">
                            <div class="progress-bar" style="width: 85%; background: linear-gradient(90deg, var(--info), #60A5FA);"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="admin-stat-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-card-icon red">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                        </div>
                        <h3>8</h3>
                        <p class="text-muted mb-2">Pending Verifications</p>
                        <div class="progress-custom">
                            <div class="progress-bar" style="width: 15%; background: linear-gradient(90deg, var(--danger), #F87171);"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <!-- Recent Activity Chart Area -->
                <div class="col-xl-8">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5>Platform Overview</h5>
                            <select class="form-select form-select-sm" style="width: auto; background: var(--gray-800); border-color: var(--gray-700); color: var(--white);">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 90 days</option>
                            </select>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded" style="background: var(--gray-900);">
                                    <h4 style="color: var(--gold);">1,245</h4>
                                    <p class="text-muted small mb-0">New Users This Week</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded" style="background: var(--gray-900);">
                                    <h4 style="color: var(--success);">892</h4>
                                    <p class="text-muted small mb-0">Jobs Completed</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded" style="background: var(--gray-900);">
                                    <h4 style="color: var(--info);">UGX 45.2M</h4>
                                    <p class="text-muted small mb-0">Total Transactions</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="mb-3">User Growth</h6>
                            <div class="d-flex align-items-end gap-2" style="height: 150px;">
                                <div class="flex-fill text-center">
                                    <div style="height: 60%; background: linear-gradient(to top, var(--gold), var(--gold-light)); border-radius: 4px 4px 0 0;"></div>
                                    <small class="text-muted">Mon</small>
                                </div>
                                <div class="flex-fill text-center">
                                    <div style="height: 80%; background: linear-gradient(to top, var(--gold), var(--gold-light)); border-radius: 4px 4px 0 0;"></div>
                                    <small class="text-muted">Tue</small>
                                </div>
                                <div class="flex-fill text-center">
                                    <div style="height: 45%; background: linear-gradient(to top, var(--gold), var(--gold-light)); border-radius: 4px 4px 0 0;"></div>
                                    <small class="text-muted">Wed</small>
                                </div>
                                <div class="flex-fill text-center">
                                    <div style="height: 90%; background: linear-gradient(to top, var(--gold), var(--gold-light)); border-radius: 4px 4px 0 0;"></div>
                                    <small class="text-muted">Thu</small>
                                </div>
                                <div class="flex-fill text-center">
                                    <div style="height: 70%; background: linear-gradient(to top, var(--gold), var(--gold-light)); border-radius: 4px 4px 0 0;"></div>
                                    <small class="text-muted">Fri</small>
                                </div>
                                <div class="flex-fill text-center">
                                    <div style="height: 55%; background: linear-gradient(to top, var(--gold), var(--gold-light)); border-radius: 4px 4px 0 0;"></div>
                                    <small class="text-muted">Sat</small>
                                </div>
                                <div class="flex-fill text-center">
                                    <div style="height: 40%; background: linear-gradient(to top, var(--gold), var(--gold-light)); border-radius: 4px 4px 0 0;"></div>
                                    <small class="text-muted">Sun</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Service Categories -->
                <div class="col-xl-4">
                    <div class="card-custom">
                        <h5 class="mb-4">Popular Services</h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Electricians</span>
                                <span style="color: var(--gold);">32%</span>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar" style="width: 32%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Plumbers</span>
                                <span style="color: var(--gold);">28%</span>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar" style="width: 28%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Carpenters</span>
                                <span style="color: var(--gold);">18%</span>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar" style="width: 18%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Painters</span>
                                <span style="color: var(--gold);">12%</span>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar" style="width: 12%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Cleaners</span>
                                <span style="color: var(--gold);">10%</span>
                            </div>
                            <div class="progress-custom">
                                <div class="progress-bar" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Pending Verifications -->
                <div class="col-xl-6">
                    <div class="table-custom">
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pending Fundi Verifications</h5>
                            <a href="#" class="btn btn-outline-gold btn-sm">View All</a>
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
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Samuel Ochieng</h6>
                                                    <small>Jinja, Uganda</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Electrician</td>
                                        <td>2 days ago</td>
                                        <td>
                                            <button class="btn btn-sm btn-gold">Review</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>James Mugisha</h6>
                                                    <small>Kampala, Uganda</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Plumber</td>
                                        <td>3 days ago</td>
                                        <td>
                                            <button class="btn btn-sm btn-gold">Review</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Rose Nakato</h6>
                                                    <small>Entebbe, Uganda</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Cleaner</td>
                                        <td>5 days ago</td>
                                        <td>
                                            <button class="btn btn-sm btn-gold">Review</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Patrick Byaruhanga</h6>
                                                    <small>Mukono, Uganda</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Carpenter</td>
                                        <td>1 week ago</td>
                                        <td>
                                            <button class="btn btn-sm btn-gold">Review</button>
                                        </td>
                                    </tr>
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
                            <a href="#" class="btn btn-outline-gold btn-sm">View All</a>
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
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Grace Nalwanga</h6>
                                                    <small>grace@email.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge-status badge-active">Client</span></td>
                                        <td>Today</td>
                                        <td><span class="badge-status badge-completed">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>John Mukasa</h6>
                                                    <small>john@email.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge-status badge-verified">Fundi</span></td>
                                        <td>Yesterday</td>
                                        <td><span class="badge-status badge-completed">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Faith Achieng</h6>
                                                    <small>faith@email.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge-status badge-active">Client</span></td>
                                        <td>2 days ago</td>
                                        <td><span class="badge-status badge-completed">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="User" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Robert Kiggundu</h6>
                                                    <small>robert@email.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge-status badge-active">Client</span></td>
                                        <td>3 days ago</td>
                                        <td><span class="badge-status badge-pending">Pending</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Log -->
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Recent Activity Log</h5>
                            <a href="#" class="text-muted small">View Full Log</a>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>User</th>
                                        <th>Details</th>
                                        <th>IP Address</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge-status badge-completed">Verification</span></td>
                                        <td>Admin</td>
                                        <td>Verified fundi: Peter Okello (Plumber)</td>
                                        <td>192.168.1.1</td>
                                        <td>10 mins ago</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge-status badge-active">User Action</span></td>
                                        <td>Grace Nalwanga</td>
                                        <td>Created new job request: Bathroom Plumbing</td>
                                        <td>192.168.1.45</td>
                                        <td>25 mins ago</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge-status badge-pending">Registration</span></td>
                                        <td>New User</td>
                                        <td>New fundi registration: Samuel Ochieng</td>
                                        <td>192.168.1.78</td>
                                        <td>1 hour ago</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge-status badge-verified">Payment</span></td>
                                        <td>System</td>
                                        <td>Job payment processed: UGX 250,000</td>
                                        <td>-</td>
                                        <td>2 hours ago</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge-status badge-cancelled">Report</span></td>
                                        <td>Faith Achieng</td>
                                        <td>User reported issue with job #1234</td>
                                        <td>192.168.1.92</td>
                                        <td>3 hours ago</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('sidebarOverlay').classList.remove('show');
            }
        });
    </script>
</body>
</html>
