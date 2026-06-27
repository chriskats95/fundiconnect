<?php
/**
 * Rate Fundi Page
 * Allows clients to rate and review fundis after job completion
 * Interactive star rating system
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Require client login
requireRole(ROLE_CLIENT);

$client_id = $_SESSION['user_id'];

// Get job ID
if (!isset($_GET['job']) || empty($_GET['job'])) {
    setFlashMessage('error', 'Job not found');
    redirect('my-jobs.php');
}

$job_id = (int)$_GET['job'];

// Fetch job details
$stmt = $db->prepare("
    SELECT jr.*, u.full_name as fundi_name, u.profile_image as fundi_image, fp.service_category
    FROM job_requests jr
    INNER JOIN users u ON jr.fundi_id = u.id
    INNER JOIN fundi_profiles fp ON u.id = fp.user_id
    WHERE jr.id = ? AND jr.client_id = ? AND jr.status = 'completed' AND jr.fundi_id IS NOT NULL
");
$stmt->execute([$job_id, $client_id]);
$job = $stmt->fetch();

if (!$job) {
    setFlashMessage('error', 'Job not found or not eligible for rating');
    redirect('my-jobs.php');
}

// Check if already rated
$stmt = $db->prepare("SELECT id FROM reviews WHERE job_id = ? AND client_id = ?");
$stmt->execute([$job_id, $client_id]);
$existingReview = $stmt->fetch();

if ($existingReview) {
    setFlashMessage('error', 'You have already rated this job');
    redirect('job-detail.php?id=' . $job_id);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        $rating = (int)$_POST['rating'];
        $comment = sanitize($_POST['comment']);
        
        // Validation
        $errors = [];
        
        if ($rating < 1 || $rating > 5) {
            $errors[] = 'Please select a rating between 1 and 5 stars';
        }
        
        if (empty($errors)) {
            try {
                // Insert review
                $stmt = $db->prepare("
                    INSERT INTO reviews (job_id, client_id, fundi_id, rating, comment, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$job_id, $client_id, $job['fundi_id'], $rating, $comment]);
                
                // Update fundi's average rating
                $stmt = $db->prepare("
                    SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
                    FROM reviews
                    WHERE fundi_id = ?
                ");
                $stmt->execute([$job['fundi_id']]);
                $ratingData = $stmt->fetch();
                
                // Update fundi profile
                $stmt = $db->prepare("
                    UPDATE fundi_profiles 
                    SET rating = ? 
                    WHERE user_id = ?
                ");
                $stmt->execute([round($ratingData['avg_rating'], 2), $job['fundi_id']]);
                
                // Create notification for fundi
                $stmt = $db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, created_at) 
                    VALUES (?, ?, ?, 'review', NOW())
                ");
                $stmt->execute([
                    $job['fundi_id'],
                    'New Review Received!',
                    $_SESSION['user_name'] . ' rated you ' . $rating . ' stars for: ' . $job['title']
                ]);
                
                setFlashMessage('success', 'Thank you! Your review has been submitted.');
                redirect('job-detail.php?id=' . $job_id);
                
            } catch (PDOException $e) {
                error_log("Review Submission Error: " . $e->getMessage());
                setFlashMessage('error', 'Failed to submit review. Please try again.');
            }
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
    }
}

$pageTitle = 'Rate Fundi';
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
        /* Star Rating Styles */
        .star-rating {
            display: flex;
            gap: 0.5rem;
            font-size: 3rem;
            cursor: pointer;
            justify-content: center;
            margin: 2rem 0;
        }

        .star-rating i {
            color: var(--gray-700);
            transition: var(--transition-normal);
        }

        .star-rating i:hover,
        .star-rating i.active {
            color: var(--gold);
            transform: scale(1.1);
        }

        .star-rating i.hovered {
            color: var(--gold-light);
        }

        .rating-label {
            text-align: center;
            font-size: 1.5rem;
            color: var(--gold);
            font-weight: 600;
            margin-top: 1rem;
            min-height: 2rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="client-dashboard.php" class="sidebar-brand">
                Fundi<span>Connect</span>
            </a>
            
            <ul class="sidebar-menu">
                <li><a href="client-dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                <li><a href="gallery.php"><i class="bi bi-search"></i> Find Fundis</a></li>
                <li><a href="my-jobs.php" class="active"><i class="bi bi-briefcase"></i> My Jobs</a></li>
                <li><a href="job-detail.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
                <li><a href="post-job.php"><i class="bi bi-star"></i> Post a Job</a></li>
            </ul>
            
            <div class="sidebar-divider"></div>
            
            <ul class="sidebar-menu">
                <li><a href="edit-profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                <li><a href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
                <li><a href="edit-profile.php"><i class="bi bi-gear"></i> Settings</a></li>
            </ul>
            
            <div class="sidebar-divider"></div>
            
            <ul class="sidebar-menu">
                <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="dashboard-header">
                <div class="dashboard-title">
                    <h1><i class="bi bi-star"></i> Rate Your Experience</h1>
                    <p>
                        <a href="job-detail.php?id=<?php echo $job_id; ?>" style="color: var(--gold);">
                            <i class="bi bi-arrow-left"></i> Back to Job Details
                        </a>
                    </p>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Job Info Card -->
                    <div class="card-custom mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <img src="<?php echo !empty($job['fundi_image']) ? 'uploads/' . htmlspecialchars($job['fundi_image']) : 'public/placeholder-user.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($job['fundi_name']); ?>"
                                     style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid var(--gold); object-fit: cover;">
                            </div>
                            <div class="col-md-9">
                                <h4 class="mb-2"><?php echo htmlspecialchars($job['fundi_name']); ?></h4>
                                <p class="mb-2" style="color: var(--gold);">
                                    <i class="bi bi-tag"></i> <?php echo htmlspecialchars($job['service_category']); ?>
                                </p>
                                <p class="mb-0" style="color: var(--gray-300);">
                                    <strong>Job:</strong> <?php echo htmlspecialchars($job['title']); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Form -->
                    <div class="card-custom">
                        <h4 class="text-center mb-4">How was your experience?</h4>
                        
                        <form method="POST" action="rate-fundi.php?job=<?php echo $job_id; ?>" id="ratingForm">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="rating" id="ratingValue" value="0">

                            <!-- Star Rating -->
                            <div class="star-rating" id="starRating">
                                <i class="bi bi-star-fill" data-rating="1"></i>
                                <i class="bi bi-star-fill" data-rating="2"></i>
                                <i class="bi bi-star-fill" data-rating="3"></i>
                                <i class="bi bi-star-fill" data-rating="4"></i>
                                <i class="bi bi-star-fill" data-rating="5"></i>
                            </div>

                            <div class="rating-label" id="ratingLabel">
                                Click on the stars to rate
                            </div>

                            <div class="gold-line" style="margin: 2rem auto;"></div>

                            <!-- Comment -->
                            <div class="mb-4">
                                <label class="form-label-custom">Share your experience (Optional)</label>
                                <textarea name="comment" 
                                          class="form-control-custom" 
                                          rows="6" 
                                          placeholder="Tell us about your experience with this fundi. What did they do well? Any areas for improvement?"></textarea>
                                <small style="color: var(--gray-400);">
                                    Your review helps other clients make informed decisions
                                </small>
                            </div>

                            <!-- Tips -->
                            <div class="alert mb-4" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                                <i class="bi bi-lightbulb"></i> 
                                <strong>Rating Guide:</strong>
                                <ul class="mb-0 mt-2" style="padding-left: 1.5rem;">
                                    <li>⭐ - Poor: Work was unsatisfactory</li>
                                    <li>⭐⭐ - Below Average: Work needs improvement</li>
                                    <li>⭐⭐⭐ - Good: Work met expectations</li>
                                    <li>⭐⭐⭐⭐ - Very Good: Work exceeded expectations</li>
                                    <li>⭐⭐⭐⭐⭐ - Excellent: Outstanding work!</li>
                                </ul>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-gold" id="submitBtn" disabled>
                                    <i class="bi bi-send"></i> Submit Review
                                </button>
                                <a href="job-detail.php?id=<?php echo $job_id; ?>" class="btn btn-dark">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Why Reviews Matter -->
                    <div class="card-custom mt-4">
                        <h5 class="mb-3">
                            <i class="bi bi-info-circle" style="color: var(--info);"></i> 
                            Why Reviews Matter
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                                    <div>
                                        <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                            Help Others
                                        </strong>
                                        <small style="color: var(--gray-400);">
                                            Your feedback helps other clients find quality fundis
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                                    <div>
                                        <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                            Improve Service
                                        </strong>
                                        <small style="color: var(--gray-400);">
                                            Constructive feedback helps fundis improve their work
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                                    <div>
                                        <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                            Build Trust
                                        </strong>
                                        <small style="color: var(--gray-400);">
                                            Honest reviews create a trusted marketplace
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                                    <div>
                                        <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                            Reward Excellence
                                        </strong>
                                        <small style="color: var(--gray-400);">
                                            Great reviews help skilled fundis get more work
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        const stars = document.querySelectorAll('.star-rating i');
        const ratingValue = document.getElementById('ratingValue');
        const ratingLabel = document.getElementById('ratingLabel');
        const submitBtn = document.getElementById('submitBtn');
        
        const labels = {
            0: 'Click on the stars to rate',
            1: '⭐ Poor',
            2: '⭐⭐ Below Average',
            3: '⭐⭐⭐ Good',
            4: '⭐⭐⭐⭐ Very Good',
            5: '⭐⭐⭐⭐⭐ Excellent'
        };

        let selectedRating = 0;

        // Click event - select rating
        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-rating'));
                ratingValue.value = selectedRating;
                updateStars(selectedRating);
                ratingLabel.textContent = labels[selectedRating];
                submitBtn.disabled = false;
            });

            // Hover effect
            star.addEventListener('mouseenter', function() {
                const hoverRating = parseInt(this.getAttribute('data-rating'));
                highlightStars(hoverRating);
            });
        });

        // Reset hover on mouse leave
        document.getElementById('starRating').addEventListener('mouseleave', function() {
            updateStars(selectedRating);
        });

        function updateStars(rating) {
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                if (starRating <= rating) {
                    star.classList.add('active');
                    star.classList.remove('hovered');
                } else {
                    star.classList.remove('active', 'hovered');
                }
            });
        }

        function highlightStars(rating) {
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                if (starRating <= rating) {
                    star.classList.add('hovered');
                } else {
                    star.classList.remove('hovered');
                }
            });
        }

        // Form validation
        document.getElementById('ratingForm').addEventListener('submit', function(e) {
            const rating = parseInt(ratingValue.value);
            
            if (rating < 1 || rating > 5) {
                e.preventDefault();
                alert('Please select a rating by clicking on the stars');
                return false;
            }

            // Confirm submission
            const confirmMsg = `You are about to submit a ${rating} star rating. Continue?`;
            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return false;
            }
        });

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
