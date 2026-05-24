<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fundi Dashboard - Manage your profile, job requests, and build your reputation.">
    <title>Fundi Dashboard | FundiConnect</title>
    
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
                    <a href="fundi-dashboard.php" class="active">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-briefcase"></i>
                        <span>Job Requests</span>
                        <span class="badge bg-warning ms-auto">4</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-calendar-check"></i>
                        <span>My Jobs</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-chat-dots"></i>
                        <span>Messages</span>
                        <span class="badge bg-danger ms-auto">2</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-images"></i>
                        <span>Portfolio</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-star"></i>
                        <span>Reviews</span>
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
                        <i class="bi bi-wallet2"></i>
                        <span>Earnings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        <span class="badge bg-warning ms-auto">7</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
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
                        <h1>Welcome back, John!</h1>
                        <p>Manage your jobs and grow your business</p>
                    </div>
                </div>
                
                <div class="dashboard-actions">
                    <button class="btn btn-dark position-relative">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            7
                        </span>
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&facepad=2" 
                                 alt="John" class="rounded-circle" style="width: 36px; height: 36px;">
                            <span class="d-none d-md-inline">John Mukasa</span>
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
            
            <!-- Profile Header -->
            <div class="profile-header mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-auto">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&facepad=2" 
                             alt="John Mukasa" class="profile-avatar-large">
                    </div>
                    <div class="col">
                        <div class="profile-info">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h2 class="mb-0">John Mukasa</h2>
                                <span class="badge-status badge-verified pulse"><i class="bi bi-patch-check-fill"></i> Verified</span>
                            </div>
                            <p class="profession mb-1">Master Electrician</p>
                            <p class="location mb-2"><i class="bi bi-geo-alt"></i> Kampala, Uganda</p>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <span class="text-white ms-1">5.0</span>
                                    <span class="text-muted">(128 reviews)</span>
                                </span>
                                <span class="text-muted">|</span>
                                <span class="text-muted">Member since Jan 2020</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="#" class="btn btn-gold">
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </a>
                    </div>
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
                                <i class="bi bi-arrow-up"></i> 18%
                            </span>
                        </div>
                        <h3>350+</h3>
                        <p>Total Jobs Completed</p>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon blue">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                        <h3>4</h3>
                        <p>Pending Requests</p>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon green">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <span class="stat-change positive">
                                <i class="bi bi-arrow-up"></i> 24%
                            </span>
                        </div>
                        <h3>UGX 4.2M</h3>
                        <p>This Month Earnings</p>
                    </div>
                </div>
                
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                        </div>
                        <h3>98%</h3>
                        <p>Success Rate</p>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- New Job Requests -->
                <div class="col-xl-8">
                    <div class="card-custom p-0">
                        <div class="p-3 d-flex justify-content-between align-items-center border-bottom" style="border-color: var(--gray-800) !important;">
                            <h5 class="mb-0">New Job Requests</h5>
                            <div class="filter-pills">
                                <span class="filter-pill active">All</span>
                                <span class="filter-pill">Urgent</span>
                                <span class="filter-pill">Nearby</span>
                            </div>
                        </div>
                        
                        <div class="p-3">
                            <!-- Job Request Item -->
                            <div class="card-custom p-3 mb-3" style="background: var(--gray-900);">
                                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h5 class="mb-0">Emergency Electrical Repair</h5>
                                            <span class="badge bg-danger">Urgent</span>
                                        </div>
                                        <p class="text-muted mb-0 small">Power outage in the entire house, need immediate assistance</p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0" style="color: var(--gold);">UGX 150,000</h5>
                                        <small class="text-muted">Budget</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-3 mb-3">
                                    <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i> Kololo, Kampala</span>
                                    <span class="text-muted small"><i class="bi bi-calendar me-1"></i> Today, ASAP</span>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> Posted 30 mins ago</span>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="user-info">
                                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&facepad=2" 
                                             alt="Grace" class="user-avatar">
                                        <div class="user-info-text">
                                            <h6>Grace Nalwanga</h6>
                                            <small>4 previous jobs with you</small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark btn-sm"><i class="bi bi-chat me-1"></i> Message</button>
                                        <button class="btn btn-outline-danger btn-sm">Decline</button>
                                        <button class="btn btn-gold btn-sm">Accept Job</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Job Request Item 2 -->
                            <div class="card-custom p-3 mb-3" style="background: var(--gray-900);">
                                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">New Building Wiring</h5>
                                        <p class="text-muted mb-0 small">Complete electrical wiring for a new 3-bedroom house</p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0" style="color: var(--gold);">UGX 2,500,000</h5>
                                        <small class="text-muted">Budget</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-3 mb-3">
                                    <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i> Ntinda, Kampala</span>
                                    <span class="text-muted small"><i class="bi bi-calendar me-1"></i> Start: May 25, 2024</span>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> Posted 2 hours ago</span>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="user-info">
                                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&facepad=2" 
                                             alt="Robert" class="user-avatar">
                                        <div class="user-info-text">
                                            <h6>Robert Kiggundu</h6>
                                            <small>New client</small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark btn-sm"><i class="bi bi-chat me-1"></i> Message</button>
                                        <button class="btn btn-outline-danger btn-sm">Decline</button>
                                        <button class="btn btn-gold btn-sm">Accept Job</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Job Request Item 3 -->
                            <div class="card-custom p-3" style="background: var(--gray-900);">
                                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">Install Security Lights</h5>
                                        <p class="text-muted mb-0 small">Install motion sensor lights around the compound</p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0" style="color: var(--gold);">UGX 300,000</h5>
                                        <small class="text-muted">Budget</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-3 mb-3">
                                    <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i> Bukoto, Kampala</span>
                                    <span class="text-muted small"><i class="bi bi-calendar me-1"></i> Flexible dates</span>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> Posted 5 hours ago</span>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="user-info">
                                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&facepad=2" 
                                             alt="Faith" class="user-avatar">
                                        <div class="user-info-text">
                                            <h6>Faith Achieng</h6>
                                            <small>2 previous jobs with you</small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark btn-sm"><i class="bi bi-chat me-1"></i> Message</button>
                                        <button class="btn btn-outline-danger btn-sm">Decline</button>
                                        <button class="btn btn-gold btn-sm">Accept Job</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 text-center border-top" style="border-color: var(--gray-800) !important;">
                            <a href="#" class="text-muted">View All Job Requests <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Sidebar -->
                <div class="col-xl-4">
                    <!-- Current Jobs -->
                    <div class="card-custom p-3 mb-4">
                        <h5 class="mb-3">Active Jobs</h5>
                        
                        <div class="d-flex align-items-center gap-3 p-3 rounded mb-2" style="background: var(--gray-900);">
                            <div class="stat-card-icon blue" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="bi bi-lightning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Bathroom Wiring</h6>
                                <small class="text-muted">Grace Nalwanga</small>
                            </div>
                            <span class="badge-status badge-active">In Progress</span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: var(--gray-900);">
                            <div class="stat-card-icon" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="bi bi-plug"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Socket Installation</h6>
                                <small class="text-muted">Robert Kiggundu</small>
                            </div>
                            <span class="badge-status badge-pending">Scheduled</span>
                        </div>
                    </div>
                    
                    <!-- Recent Reviews -->
                    <div class="card-custom p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Recent Reviews</h5>
                            <a href="#" class="text-muted small">View All</a>
                        </div>
                        
                        <div class="mb-3 pb-3 border-bottom" style="border-color: var(--gray-800) !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="user-info">
                                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=36&h=36&fit=crop&facepad=2" 
                                         alt="Grace" class="user-avatar" style="width: 36px; height: 36px;">
                                    <div class="user-info-text">
                                        <h6 class="mb-0" style="font-size: 0.9rem;">Grace Nalwanga</h6>
                                    </div>
                                </div>
                                <span class="text-warning small">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </span>
                            </div>
                            <p class="text-muted small mb-0">"Excellent work! John fixed my electrical issues quickly and professionally. Highly recommend!"</p>
                        </div>
                        
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="user-info">
                                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=36&h=36&fit=crop&facepad=2" 
                                         alt="Robert" class="user-avatar" style="width: 36px; height: 36px;">
                                    <div class="user-info-text">
                                        <h6 class="mb-0" style="font-size: 0.9rem;">Robert Kiggundu</h6>
                                    </div>
                                </div>
                                <span class="text-warning small">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </span>
                            </div>
                            <p class="text-muted small mb-0">"Very knowledgeable and punctual. Will definitely hire again for future projects."</p>
                        </div>
                    </div>
                    
                    <!-- Portfolio Preview -->
                    <div class="card-custom p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">My Portfolio</h5>
                            <a href="#" class="btn btn-outline-gold btn-sm">Add New</a>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="portfolio-item">
                                    <img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?w=150&h=150&fit=crop" alt="Work 1">
                                    <div class="portfolio-overlay">
                                        <small class="text-white">Wiring Project</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="portfolio-item">
                                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=150&h=150&fit=crop" alt="Work 2">
                                    <div class="portfolio-overlay">
                                        <small class="text-white">Panel Install</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="portfolio-item">
                                    <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=150&h=150&fit=crop" alt="Work 3">
                                    <div class="portfolio-overlay">
                                        <small class="text-white">Light Fixtures</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="portfolio-item">
                                    <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?w=150&h=150&fit=crop" alt="Work 4">
                                    <div class="portfolio-overlay">
                                        <small class="text-white">Commercial</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="portfolio-item">
                                    <img src="https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=150&h=150&fit=crop" alt="Work 5">
                                    <div class="portfolio-overlay">
                                        <small class="text-white">Repairs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="portfolio-item" style="background: var(--gray-800); display: flex; align-items: center; justify-content: center; height: 80px; border-radius: var(--radius-md); cursor: pointer;">
                                    <div class="text-center">
                                        <i class="bi bi-plus-lg text-muted"></i>
                                        <small class="d-block text-muted">Add</small>
                                    </div>
                                </div>
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

        // Filter pills functionality
        document.querySelectorAll('.filter-pill').forEach(pill => {
            pill.addEventListener('click', function() {
                document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
            });
        });

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
