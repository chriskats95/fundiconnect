<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get current logged-in user
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FundiConnect - Uganda's premier platform connecting households and businesses to verified, rated, and nearby skilled workers.">
    <title>FundiConnect | Uganda's Verified Skilled Workers Platform</title>
    
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
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fundi<span>Connect</span></a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#fundis">Find Fundis</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <?php 
                            $userImg = getProfileImage($currentUser);
                            ?>
                            <img src="<?= $userImg ?>" alt="User" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                            <span class="d-none d-md-inline"><?= htmlspecialchars($currentUser['full_name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="background: var(--black-card); border-color: var(--gray-800);">
                            <li>
                                <?php
                                $dashLink = hasRole('admin') ? 'admin-dashboard.php' : (hasRole('fundi') ? 'fundi-dashboard.php' : 'client-dashboard.php');
                                ?>
                                <a class="dropdown-item text-white" href="<?= $dashLink ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                            </li>
                            <li>
                                <?php if (hasRole('fundi')): ?>
                                    <a class="dropdown-item text-white" href="fundi-profile.php?id=<?= $currentUser['id'] ?>"><i class="bi bi-person me-2"></i> Profile</a>
                                <?php else: ?>
                                    <a class="dropdown-item text-white" href="client-dashboard.php"><i class="bi bi-person me-2"></i> My Account</a>
                                <?php endif; ?>
                            </li>
                            <li><hr class="dropdown-divider" style="border-color: var(--gray-800);"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-2">
                        <a href="login.php" class="btn btn-outline-gold">Login</a>
                        <a href="register.php" class="btn btn-gold">Get Started</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <i class="bi bi-shield-check"></i>
                        <span>Trusted by 10,000+ Ugandans</span>
                    </div>
                    
                    <h1 class="hero-title">
                        Find <span class="highlight">Verified</span> Skilled Workers Near You
                    </h1>
                    
                    <p class="hero-description">
                        Connect with Uganda's finest verified fundis - plumbers, electricians, carpenters, painters, and cleaners. Quality service, guaranteed.
                    </p>
                    
                    <!-- Search Box -->
                    <div class="search-box mb-4">
                        <select class="form-control-custom form-select" id="serviceSelect">
                            <option value="">Select Service</option>
                            <option value="Plumbing">Plumbing</option>
                            <option value="Electrical">Electrical</option>
                            <option value="Carpentry">Carpentry</option>
                            <option value="Painting">Painting</option>
                            <option value="Cleaning">Cleaning</option>
                        </select>
                        <input type="text" class="form-control-custom" placeholder="Your Location (e.g., Kampala)" id="locationInput">
                        <a href="find-fundis.php" class="btn btn-gold" id="findFundiBtn">
                            <i class="bi bi-search me-2"></i>Find Fundi
                        </a>
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <h3>5,000+</h3>
                            <p>Verified Fundis</p>
                        </div>
                        <div class="stat-item">
                            <h3>25,000+</h3>
                            <p>Jobs Completed</p>
                        </div>
                        <div class="stat-item">
                            <h3>4.9</h3>
                            <p>Average Rating</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-image position-relative">
                        <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=600&h=700&fit=crop" 
                             alt="Skilled Worker" class="img-fluid rounded-4">
                        
                        <!-- Floating Cards -->
                        <div class="hero-card card-1">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-card-icon">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white">Verified Pro</h6>
                                    <small class="text-muted">Background Checked</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hero-card card-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-card-icon green">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white">4.9 Rating</h6>
                                    <small class="text-muted">500+ Reviews</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section section-darker" id="services">
        <div class="container">
            <div class="gold-line"></div>
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Browse through our wide range of skilled professionals ready to help with your home and business needs.</p>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-img">
                            <img src="https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=400&h=300&fit=crop" alt="Plumbing">
                        </div>
                        <div class="service-card-body">
                            <h4>Plumbing</h4>
                            <p>Expert plumbers for installations, repairs, and maintenance of water systems.</p>
                            <a href="find-fundis.php?category=Plumbing" class="btn btn-outline-gold btn-sm">Find Plumbers <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-img">
                            <img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?w=400&h=300&fit=crop" alt="Electrical">
                        </div>
                        <div class="service-card-body">
                            <h4>Electrical</h4>
                            <p>Licensed electricians for wiring, installations, and electrical repairs.</p>
                            <a href="find-fundis.php?category=Electrical" class="btn btn-outline-gold btn-sm">Find Electricians <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-img">
                            <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?w=400&h=300&fit=crop" alt="Carpentry">
                        </div>
                        <div class="service-card-body">
                            <h4>Carpentry</h4>
                            <p>Skilled carpenters for furniture, repairs, and custom woodwork projects.</p>
                            <a href="find-fundis.php?category=Carpentry" class="btn btn-outline-gold btn-sm">Find Carpenters <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-img">
                            <img src="https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=400&h=300&fit=crop" alt="Painting">
                        </div>
                        <div class="service-card-body">
                            <h4>Painting</h4>
                            <p>Professional painters for interior and exterior painting services.</p>
                            <a href="find-fundis.php?category=Painting" class="btn btn-outline-gold btn-sm">Find Painters <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-img">
                            <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=400&h=300&fit=crop" alt="Cleaning">
                        </div>
                        <div class="service-card-body">
                            <h4>Cleaning</h4>
                            <p>Reliable cleaners for residential and commercial cleaning services.</p>
                            <a href="find-fundis.php?category=Cleaning" class="btn btn-outline-gold btn-sm">Find Cleaners <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-img">
                            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=300&fit=crop" alt="More Services">
                        </div>
                        <div class="service-card-body">
                            <h4>More Services</h4>
                            <p>Masons, welders, AC technicians, and many more skilled professionals.</p>
                            <a href="find-fundis.php" class="btn btn-outline-gold btn-sm">View All <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section section-dark" id="how-it-works">
        <div class="container">
            <div class="gold-line"></div>
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Get your job done in three simple steps</p>
            
            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Search & Browse</h4>
                        <p>Enter your service need and location to find verified fundis near you. Filter by ratings, price, and availability.</p>
                        <div class="step-connector d-none d-md-block"></div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Send Job Request</h4>
                        <p>Select your preferred fundi and send a job request with details about your task, budget, and timeline.</p>
                        <div class="step-connector d-none d-md-block"></div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Get It Done</h4>
                        <p>Your fundi arrives on schedule, completes the job, and you can rate their service to help others.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Fundis -->
    <section class="section section-darker" id="fundis">
        <div class="container">
            <div class="gold-line"></div>
            <h2 class="section-title">Top Rated Fundis</h2>
            <p class="section-subtitle">Meet some of our most trusted and highly-rated skilled professionals</p>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="fundi-card">
                        <div class="fundi-card-header">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=250&fit=crop" alt="Work">
                            <span class="fundi-verified pulse"><i class="bi bi-patch-check-fill"></i> Verified</span>
                        </div>
                        <div class="fundi-card-body">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&facepad=2" 
                                 alt="John Mukasa" class="fundi-avatar">
                            <h5 class="fundi-name">John Mukasa</h5>
                            <p class="fundi-profession">Master Electrician</p>
                            <p class="fundi-location"><i class="bi bi-geo-alt"></i> Kampala, Uganda</p>
                            <div class="fundi-rating">
                                <span class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </span>
                                <span>5.0 (128 reviews)</span>
                            </div>
                            <div class="fundi-stats">
                                <div>
                                    <strong>350+</strong>
                                    <small>Jobs</small>
                                </div>
                                <div>
                                    <strong>98%</strong>
                                    <small>Success</small>
                                </div>
                                <div>
                                    <strong>5 yrs</strong>
                                    <small>Exp</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="fundi-card">
                        <div class="fundi-card-header">
                            <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=250&fit=crop" alt="Work">
                            <span class="fundi-verified pulse"><i class="bi bi-patch-check-fill"></i> Verified</span>
                        </div>
                        <div class="fundi-card-body">
                            <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=100&h=100&fit=crop&facepad=2" 
                                 alt="Sarah Nambi" class="fundi-avatar">
                            <h5 class="fundi-name">Sarah Nambi</h5>
                            <p class="fundi-profession">Interior Painter</p>
                            <p class="fundi-location"><i class="bi bi-geo-alt"></i> Entebbe, Uganda</p>
                            <div class="fundi-rating">
                                <span class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </span>
                                <span>4.8 (95 reviews)</span>
                            </div>
                            <div class="fundi-stats">
                                <div>
                                    <strong>220+</strong>
                                    <small>Jobs</small>
                                </div>
                                <div>
                                    <strong>96%</strong>
                                    <small>Success</small>
                                </div>
                                <div>
                                    <strong>3 yrs</strong>
                                    <small>Exp</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="fundi-card">
                        <div class="fundi-card-header">
                            <img src="https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=400&h=250&fit=crop" alt="Work">
                            <span class="fundi-verified pulse"><i class="bi bi-patch-check-fill"></i> Verified</span>
                        </div>
                        <div class="fundi-card-body">
                            <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&h=100&fit=crop&facepad=2" 
                                 alt="Peter Okello" class="fundi-avatar">
                            <h5 class="fundi-name">Peter Okello</h5>
                            <p class="fundi-profession">Expert Plumber</p>
                            <p class="fundi-location"><i class="bi bi-geo-alt"></i> Jinja, Uganda</p>
                            <div class="fundi-rating">
                                <span class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </span>
                                <span>4.9 (156 reviews)</span>
                            </div>
                            <div class="fundi-stats">
                                <div>
                                    <strong>400+</strong>
                                    <small>Jobs</small>
                                </div>
                                <div>
                                    <strong>99%</strong>
                                    <small>Success</small>
                                </div>
                                <div>
                                    <strong>7 yrs</strong>
                                    <small>Exp</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="fundi-card">
                        <div class="fundi-card-header">
                            <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?w=400&h=250&fit=crop" alt="Work">
                            <span class="fundi-verified pulse"><i class="bi bi-patch-check-fill"></i> Verified</span>
                        </div>
                        <div class="fundi-card-body">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&facepad=2" 
                                 alt="David Ssemwogerere" class="fundi-avatar">
                            <h5 class="fundi-name">David Ssemwogerere</h5>
                            <p class="fundi-profession">Master Carpenter</p>
                            <p class="fundi-location"><i class="bi bi-geo-alt"></i> Mukono, Uganda</p>
                            <div class="fundi-rating">
                                <span class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </span>
                                <span>5.0 (89 reviews)</span>
                            </div>
                            <div class="fundi-stats">
                                <div>
                                    <strong>180+</strong>
                                    <small>Jobs</small>
                                </div>
                                <div>
                                    <strong>100%</strong>
                                    <small>Success</small>
                                </div>
                                <div>
                                    <strong>10 yrs</strong>
                                    <small>Exp</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="find-fundis.php" class="btn btn-gold btn-lg">View All Fundis <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section section-dark">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="gold-line" style="margin: 0 0 1rem 0;"></div>
                    <h2 class="mb-4" style="text-align: left;">Why Choose <span style="color: var(--gold);">FundiConnect?</span></h2>
                    <p class="text-muted mb-4">We are committed to connecting you with the best skilled workers in Uganda. Our rigorous verification process ensures quality and trust.</p>
                    
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="card-custom">
                                <div class="card-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <h4>Verified Workers</h4>
                                <p>All fundis undergo thorough background checks and skill verification.</p>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="card-custom">
                                <div class="card-icon">
                                    <i class="bi bi-star"></i>
                                </div>
                                <h4>Rated & Reviewed</h4>
                                <p>Real reviews from real customers help you make informed decisions.</p>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="card-custom">
                                <div class="card-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <h4>Local Experts</h4>
                                <p>Find skilled workers right in your neighborhood for faster service.</p>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="card-custom">
                                <div class="card-icon">
                                    <i class="bi bi-headset"></i>
                                </div>
                                <h4>24/7 Support</h4>
                                <p>Our customer support team is always ready to assist you.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600&h=500&fit=crop" 
                         alt="Team" class="img-fluid rounded-4">
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section section-darker">
        <div class="container">
            <div class="gold-line"></div>
            <h2 class="section-title">What Our Clients Say</h2>
            <p class="section-subtitle">Trusted by thousands of satisfied customers across Uganda</p>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="testimonial-text">"FundiConnect saved me during an emergency plumbing situation. The fundi arrived within an hour and fixed everything professionally. Highly recommended!"</p>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&facepad=2" alt="Grace">
                            <div>
                                <h6>Grace Nalwanga</h6>
                                <small>Homeowner, Kampala</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="testimonial-text">"As a business owner, finding reliable workers was always a challenge. FundiConnect has become our go-to platform for all maintenance needs."</p>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&facepad=2" alt="Robert">
                            <div>
                                <h6>Robert Kiggundu</h6>
                                <small>Business Owner, Entebbe</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <p class="testimonial-text">"The carpenter I found through FundiConnect did an amazing job on my kitchen cabinets. Fair pricing and excellent craftsmanship!"</p>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&facepad=2" alt="Faith">
                            <div>
                                <h6>Faith Achieng</h6>
                                <small>Homeowner, Jinja</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section section-dark" style="background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, var(--black) 100%);">
        <div class="container text-center">
            <h2 class="mb-3">Ready to Get Started?</h2>
            <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Join thousands of Ugandans who trust FundiConnect for their skilled worker needs.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?= isLoggedIn() ? 'find-fundis.php' : 'register.php' ?>" class="btn btn-gold btn-lg">Find a Fundi</a>
                <a href="<?= isLoggedIn() && hasRole('client') ? 'post-job.php' : 'register.php?role=fundi' ?>" class="btn btn-outline-gold btn-lg"><?= isLoggedIn() && hasRole('client') ? 'Post a Job' : 'Join as a Fundi' ?></a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a href="index.php" class="footer-brand">Fundi<span>Connect</span></a>
                    <p class="footer-text">Uganda's premier platform connecting households and businesses to verified, rated, and nearby skilled workers.</p>
                    <div class="footer-social">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-6 col-lg-2">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#fundis">Find Fundis</a></li>
                    </ul>
                </div>
                
                <div class="col-6 col-lg-2">
                    <h5 class="footer-title">For Fundis</h5>
                    <ul class="footer-links">
                        <li><a href="register.php?role=fundi">Join as Fundi</a></li>
                        <li><a href="#">Success Stories</a></li>
                        <li><a href="#">Resources</a></li>
                        <li><a href="#">FAQs</a></li>
                    </ul>
                </div>
                
                <div class="col-6 col-lg-2">
                    <h5 class="footer-title">Support</h5>
                    <ul class="footer-links">
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Safety Tips</a></li>
                        <li><a href="#">Report Issue</a></li>
                    </ul>
                </div>
                
                <div class="col-6 col-lg-2">
                    <h5 class="footer-title">Legal</h5>
                    <ul class="footer-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 FundiConnect. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                    <a href="#">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Handle search box functionality
        document.getElementById('findFundiBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const service = document.getElementById('serviceSelect').value;
            const location = document.getElementById('locationInput').value;
            
            let url = 'find-fundis.php';
            const params = [];
            
            if (service) {
                params.push('category=' + encodeURIComponent(service));
            }
            if (location) {
                params.push('location=' + encodeURIComponent(location));
            }
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            window.location.href = url;
        });
    </script>
</body>
</html>
