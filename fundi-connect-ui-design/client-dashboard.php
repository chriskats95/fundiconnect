<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Client Dashboard - Manage your job requests and find skilled workers.">
    <title>Client Dashboard | FundiConnect</title>
    
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
            
            <ul class="sidebar-menu">
                <li>
                    <a href="client-dashboard.php" class="active">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-search"></i>
                        <span>Find Fundis</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-briefcase"></i>
                        <span>My Job Requests</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-chat-dots"></i>
                        <span>Messages</span>
                        <span class="badge bg-danger ms-auto">3</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-heart"></i>
                        <span>Saved Fundis</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-divider"></div>
            <p class="sidebar-section-title">Account</p>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <i class="bi bi-person"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        <span class="badge bg-warning ms-auto">5</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-question-circle"></i>
                        <span>Help & Support</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-divider"></div>
            
            <ul class="sidebar-menu">
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
                        <h1>Welcome back, Grace!</h1>
                        <p>Manage your job requests and find skilled workers</p>
                    </div>
                </div>
                
                <div class="dashboard-actions">
                    <button class="btn btn-dark position-relative">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            5
                        </span>
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&facepad=2" 
                                 alt="Grace" class="rounded-circle" style="width: 36px; height: 36px;">
                            <span class="d-none d-md-inline">Grace Nalwanga</span>
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
            
            <!-- Quick Actions -->
            <div class="quick-actions mb-4">
                <div class="quick-action-btn" onclick="window.location.href='#'">
                    <i class="bi bi-plus-circle"></i>
                    <span>Post a Job</span>
                </div>
                <div class="quick-action-btn" onclick="window.location.href='#'">
                    <i class="bi bi-search"></i>
                    <span>Find Fundi</span>
                </div>
                <div class="quick-action-btn" onclick="window.location.href='#'">
                    <i class="bi bi-chat-dots"></i>
                    <span>Messages</span>
                </div>
                <div class="quick-action-btn" onclick="window.location.href='#'">
                    <i class="bi bi-star"></i>
                    <span>Rate Service</span>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <span class="stat-change positive">
                                <i class="bi bi-arrow-up"></i> 12%
                            </span>
                        </div>
                        <h3>8</h3>
                        <p>Total Jobs Posted</p>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon blue">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                        <h3>3</h3>
                        <p>Pending Requests</p>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon green">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                        <h3>5</h3>
                        <p>Completed Jobs</p>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="bi bi-heart"></i>
                            </div>
                        </div>
                        <h3>12</h3>
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
                            <a href="#" class="btn btn-outline-gold btn-sm">View All</a>
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
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">Bathroom Plumbing Repair</h6>
                                                <small class="text-muted">Leaking pipes and faucet replacement</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="Peter" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Peter Okello</h6>
                                                    <small>Plumber</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span>May 20, 2024</span>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-active">In Progress</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-dark"><i class="bi bi-eye"></i></button>
                                            <button class="btn btn-sm btn-dark"><i class="bi bi-chat"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">Living Room Painting</h6>
                                                <small class="text-muted">Interior painting for 2 rooms</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="Sarah" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>Sarah Nambi</h6>
                                                    <small>Painter</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span>May 18, 2024</span>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-completed">Completed</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-gold"><i class="bi bi-star"></i> Rate</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">Electrical Wiring</h6>
                                                <small class="text-muted">New socket installation in kitchen</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="John" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>John Mukasa</h6>
                                                    <small>Electrician</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span>May 15, 2024</span>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-pending">Pending</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-dark"><i class="bi bi-eye"></i></button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">Custom Bookshelf</h6>
                                                <small class="text-muted">Built-in bookshelf for home office</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&facepad=2" 
                                                     alt="David" class="user-avatar">
                                                <div class="user-info-text">
                                                    <h6>David Ssemwogerere</h6>
                                                    <small>Carpenter</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span>May 10, 2024</span>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-completed">Completed</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-dark"><i class="bi bi-eye"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications & Saved Fundis -->
                <div class="col-xl-4">
                    <!-- Notifications -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Recent Notifications</h5>
                            <a href="#" class="text-muted small">View All</a>
                        </div>
                        
                        <div class="notification-item unread">
                            <div class="notification-icon job">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <div class="notification-content">
                                <h6>Job Request Accepted</h6>
                                <p>Peter Okello accepted your plumbing job request</p>
                            </div>
                            <span class="notification-time">2h ago</span>
                        </div>
                        
                        <div class="notification-item unread">
                            <div class="notification-icon payment">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                <h6>Job Completed</h6>
                                <p>Sarah Nambi has marked the painting job as complete</p>
                            </div>
                            <span class="notification-time">5h ago</span>
                        </div>
                        
                        <div class="notification-item">
                            <div class="notification-icon alert">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <div class="notification-content">
                                <h6>New Message</h6>
                                <p>You have 3 unread messages from fundis</p>
                            </div>
                            <span class="notification-time">1d ago</span>
                        </div>
                    </div>
                    
                    <!-- Saved Fundis -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Saved Fundis</h5>
                            <a href="#" class="text-muted small">View All</a>
                        </div>
                        
                        <div class="card-custom p-3 mb-3">
                            <div class="d-flex gap-3">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&facepad=2" 
                                     alt="John" class="rounded-circle" style="width: 60px; height: 60px; border: 2px solid var(--gold);">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-0">John Mukasa</h6>
                                            <small class="text-muted">Master Electrician</small>
                                        </div>
                                        <span class="badge-status badge-verified"><i class="bi bi-patch-check-fill"></i></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <span class="text-warning small">
                                            <i class="bi bi-star-fill"></i> 5.0
                                        </span>
                                        <span class="text-muted small">|</span>
                                        <span class="text-muted small">350+ jobs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-gold btn-sm flex-fill">Hire Now</button>
                                <button class="btn btn-dark btn-sm"><i class="bi bi-chat"></i></button>
                            </div>
                        </div>
                        
                        <div class="card-custom p-3">
                            <div class="d-flex gap-3">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=60&h=60&fit=crop&facepad=2" 
                                     alt="David" class="rounded-circle" style="width: 60px; height: 60px; border: 2px solid var(--gold);">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-0">David Ssemwogerere</h6>
                                            <small class="text-muted">Master Carpenter</small>
                                        </div>
                                        <span class="badge-status badge-verified"><i class="bi bi-patch-check-fill"></i></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <span class="text-warning small">
                                            <i class="bi bi-star-fill"></i> 5.0
                                        </span>
                                        <span class="text-muted small">|</span>
                                        <span class="text-muted small">180+ jobs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-gold btn-sm flex-fill">Hire Now</button>
                                <button class="btn btn-dark btn-sm"><i class="bi bi-chat"></i></button>
                            </div>
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
