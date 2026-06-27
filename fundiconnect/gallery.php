<?php
/**
 * Gallery Page
 * Public gallery showing portfolio images from all verified fundis
 * Filterable by category with lightbox view
 */

require_once 'config/config.php';
require_once 'includes/functions.php';

// Get filter category
$filterCategory = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';

// Build query to fetch portfolio images from verified fundis
$query = "
    SELECT pi.*, 
           u.id as fundi_user_id, u.full_name as fundi_name, u.profile_image as fundi_image,
           fp.service_category
    FROM portfolio_images pi
    INNER JOIN fundi_profiles fp ON pi.fundi_id = fp.id
    INNER JOIN users u ON fp.user_id = u.id
    WHERE fp.verification_status = 'approved' AND u.status = 'active'
";

$params = [];

if ($filterCategory !== 'all') {
    $query .= " AND fp.service_category = ?";
    $params[] = $filterCategory;
}

$query .= " ORDER BY pi.uploaded_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$portfolioImages = $stmt->fetchAll();

// Fetch all service categories for filter
$stmt = $db->query("SELECT * FROM service_categories WHERE is_active = TRUE ORDER BY name ASC");
$categories = $stmt->fetchAll();

// Get category counts
$categoryCounts = [];
foreach ($categories as $category) {
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT pi.id) as count
        FROM portfolio_images pi
        INNER JOIN fundi_profiles fp ON pi.fundi_id = fp.id
        INNER JOIN users u ON fp.user_id = u.id
        WHERE fp.verification_status = 'approved' 
        AND u.status = 'active'
        AND fp.service_category = ?
    ");
    $stmt->execute([$category['name']]);
    $result = $stmt->fetch();
    $categoryCounts[$category['name']] = $result['count'];
}

$pageTitle = 'Portfolio Gallery';
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

    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .gallery-item {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: var(--black-card);
            border: 1px solid var(--gray-800);
            transition: var(--transition-normal);
            cursor: pointer;
        }

        .gallery-item:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
            box-shadow: var(--shadow-gold);
        }

        .gallery-item-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: var(--transition-slow);
        }

        .gallery-item:hover .gallery-item-img {
            transform: scale(1.05);
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 1.5rem;
            opacity: 0;
            transition: var(--transition-normal);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-fundi-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .gallery-fundi-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--gold);
            object-fit: cover;
        }

        .gallery-fundi-name {
            color: var(--white);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .gallery-category {
            color: var(--gold);
            font-size: 0.85rem;
        }

        .gallery-caption {
            color: var(--gray-200);
            font-size: 0.9rem;
            margin: 0;
        }

        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            background: var(--black-card);
            border: 1px solid var(--gray-700);
            border-radius: var(--radius-full);
            color: var(--gray-300);
            font-weight: 500;
            transition: var(--transition-normal);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-btn:hover {
            background: var(--gray-800);
            border-color: var(--gold);
            color: var(--gold);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-color: var(--gold);
            color: var(--black);
            box-shadow: var(--shadow-gold);
        }

        .filter-count {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* Masonry effect for varied heights */
        @supports (grid-template-rows: masonry) {
            .gallery-grid {
                grid-template-rows: masonry;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fundi<span>Connect</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gallery.php">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo hasRole(ROLE_CLIENT) ? 'client-dashboard.php' : (hasRole(ROLE_FUNDI) ? 'fundi-dashboard.php' : 'admin-dashboard.php'); ?>">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-gold ms-2" href="register.php">Get Started</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Gallery Section -->
    <div class="section" style="padding-top: 120px;">
        <div class="container">
            <!-- Page Header -->
            <div class="text-center mb-4">
                <div class="gold-line"></div>
                <h1 class="section-title">Portfolio Gallery</h1>
                <p class="section-subtitle">
                    Explore quality work from our verified fundis. Get inspired and find the perfect professional for your project.
                </p>
            </div>

            <!-- Category Filters -->
            <div class="filter-buttons">
                <a href="gallery.php" class="filter-btn <?php echo $filterCategory === 'all' ? 'active' : ''; ?>">
                    <i class="bi bi-grid"></i>
                    All Categories
                    <span class="filter-count">(<?php echo count($portfolioImages); ?>)</span>
                </a>
                <?php foreach ($categories as $category): ?>
                    <?php if ($categoryCounts[$category['name']] > 0): ?>
                        <a href="gallery.php?category=<?php echo urlencode($category['name']); ?>" 
                           class="filter-btn <?php echo $filterCategory === $category['name'] ? 'active' : ''; ?>">
                            <i class="<?php echo $category['icon']; ?>"></i>
                            <?php echo htmlspecialchars($category['name']); ?>
                            <span class="filter-count">(<?php echo $categoryCounts[$category['name']]; ?>)</span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Gallery Grid -->
            <?php if (empty($portfolioImages)): ?>
                <div class="card-custom text-center py-5 mt-5">
                    <i class="bi bi-images" style="font-size: 5rem; color: var(--gray-600); opacity: 0.3;"></i>
                    <h4 class="mt-4" style="color: var(--gray-300);">No Images Found</h4>
                    <p style="color: var(--gray-400);">
                        <?php if ($filterCategory !== 'all'): ?>
                            No portfolio images available for this category yet.
                        <?php else: ?>
                            Our fundis haven't uploaded any portfolio images yet.
                        <?php endif; ?>
                    </p>
                    <a href="gallery.php" class="btn btn-gold mt-3">
                        <i class="bi bi-grid"></i> View All Categories
                    </a>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($portfolioImages as $image): ?>
                        <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $image['id']; ?>">
                            <img src="assets/uploads/portfolio/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['caption']); ?>"
                                 class="gallery-item-img">
                            
                            <div class="gallery-overlay">
                                <div class="gallery-fundi-info">
                                    <img src="<?php echo !empty($image['fundi_image']) ? 'uploads/' . htmlspecialchars($image['fundi_image']) : 'public/placeholder-user.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($image['fundi_name']); ?>"
                                         class="gallery-fundi-avatar">
                                    <div>
                                        <div class="gallery-fundi-name">
                                            <?php echo htmlspecialchars($image['fundi_name']); ?>
                                        </div>
                                        <div class="gallery-category">
                                            <?php echo htmlspecialchars($image['service_category']); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($image['caption'])): ?>
                                    <p class="gallery-caption">
                                        <?php echo htmlspecialchars($image['caption']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Image Detail Modal -->
                        <div class="modal fade" id="imageModal<?php echo $image['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content" style="background: var(--black-card); border: 1px solid var(--gray-800);">
                                    <div class="modal-header" style="border-bottom: 1px solid var(--gray-800);">
                                        <h5 class="modal-title">
                                            <?php echo !empty($image['caption']) ? htmlspecialchars($image['caption']) : 'Portfolio Image'; ?>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Image -->
                                        <div class="text-center mb-4">
                                            <img src="assets/uploads/portfolio/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($image['caption']); ?>"
                                                 class="img-fluid" 
                                                 style="max-height: 70vh; border-radius: var(--radius-md);">
                                        </div>

                                        <!-- Fundi Info -->
                                        <div class="row align-items-center">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo !empty($image['fundi_image']) ? 'uploads/' . htmlspecialchars($image['fundi_image']) : 'public/placeholder-user.jpg'; ?>" 
                                                         alt="<?php echo htmlspecialchars($image['fundi_name']); ?>"
                                                         style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid var(--gold); object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($image['fundi_name']); ?></h6>
                                                        <p class="mb-0" style="color: var(--gold); font-size: 0.9rem;">
                                                            <?php echo htmlspecialchars($image['service_category']); ?>
                                                        </p>
                                                        <small style="color: var(--gray-400);">
                                                            <i class="bi bi-clock"></i> Uploaded <?php echo timeAgo($image['uploaded_at']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <a href="fundi-profile.php?id=<?php echo $image['fundi_user_id']; ?>" 
                                                   class="btn btn-gold"
                                                   target="_blank">
                                                    <i class="bi bi-person"></i> View Profile
                                                </a>
                                                <?php if (isLoggedIn() && hasRole(ROLE_CLIENT)): ?>
                                                    <a href="post-job.php?fundi=<?php echo $image['fundi_user_id']; ?>" 
                                                       class="btn btn-dark ms-2">
                                                        <i class="bi bi-calendar-check"></i> Book Now
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Stats -->
                <div class="text-center mt-5">
                    <p style="color: var(--gray-400);">
                        Showing <strong style="color: var(--gold);"><?php echo count($portfolioImages); ?></strong> 
                        portfolio <?php echo count($portfolioImages) == 1 ? 'image' : 'images'; ?>
                        <?php if ($filterCategory !== 'all'): ?>
                            in <strong style="color: var(--gold);"><?php echo htmlspecialchars($filterCategory); ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Call to Action -->
            <div class="card-custom mt-5 text-center">
                <h4 class="mb-3">Looking for Quality Workmanship?</h4>
                <p style="color: var(--gray-300); max-width: 600px; margin: 0 auto 2rem;">
                    Browse our verified fundis and find the perfect professional for your project. 
                    All fundis are vetted and rated by real clients.
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="find-fundis.php" class="btn btn-gold">
                        <i class="bi bi-search"></i> Find Fundis
                    </a>
                    <a href="register.php" class="btn btn-outline-gold">
                        <i class="bi bi-person-plus"></i> Join as Client
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">Fundi<span>Connect</span></div>
                    <p class="footer-text">Uganda's trusted platform connecting skilled workers with clients.</p>
                    <div class="footer-social">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="find-fundis.php">Find Fundis</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="register.php">Get Started</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Support</h5>
                    <ul class="footer-links">
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="#">Help Center</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-12 mb-4">
                    <h5 class="footer-title">Newsletter</h5>
                    <p style="color: var(--gray-400); margin-bottom: 1rem;">
                        Subscribe to get updates about new fundis and features.
                    </p>
                    <div class="d-flex gap-2">
                        <input type="email" class="form-control-custom" placeholder="Your email" style="flex: 1;">
                        <button class="btn btn-gold">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> FundiConnect. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Add smooth scroll to gallery items
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.addEventListener('click', function() {
                // Smooth animation on click
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });
    </script>
</body>
</html>
