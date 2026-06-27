<?php
/**
 * Fundi Profile Page - Public View
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Get fundi ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'Fundi not found');
    redirect('find-fundis.php');
}

$fundi_user_id = (int)$_GET['id'];

// Fetch fundi details
$stmt = $db->prepare("
    SELECT u.*, fp.*
    FROM users u
    INNER JOIN fundi_profiles fp ON u.id = fp.user_id
    WHERE u.id = ? AND u.role = ? AND u.status = 'active'
");
$stmt->execute([$fundi_user_id, ROLE_FUNDI]);
$fundi = $stmt->fetch();

if (!$fundi) {
    setFlashMessage('error', 'Fundi not found or not available');
    redirect('find-fundis.php');
}

// Portfolio Images
$stmt = $db->prepare("SELECT * FROM portfolio_images WHERE fundi_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$fundi['id']]);  // fundi_profiles.id
$portfolioImages = $stmt->fetchAll();

// Reviews
$stmt = $db->prepare("
    SELECT r.*, u.full_name as client_name, u.profile_image as client_image
    FROM reviews r
    JOIN users u ON r.client_id = u.id
    WHERE r.fundi_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$fundi_user_id]);
$reviews = $stmt->fetchAll();

$totalReviews = count($reviews);

// Calculate average rating if not in fundi_profiles
$avgRating = $fundi['rating'] ?? 0;

$pageTitle = htmlspecialchars($fundi['full_name']) . ' - Profile';
require_once 'includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php displayFlashMessage(); ?>

        <div class="container mt-4">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-lg-4 mb-4">
                    <div class="card-custom text-center">
                        <img src="<?= getProfileImage($fundi) ?>" 
                             alt="<?= htmlspecialchars($fundi['full_name']) ?>"
                             class="profile-avatar-large mb-3" style="width: 140px; height: 140px;">

                        <h3><?= htmlspecialchars($fundi['full_name']) ?></h3>
                        <p class="text-gold"><?= htmlspecialchars($fundi['service_category']) ?></p>
                        
                        <p class="text-muted">
                            <i class="bi bi-geo-alt"></i> 
                            <?= htmlspecialchars($fundi['location'] ?? 'Location not set') ?>
                        </p>

                        <div class="gold-line my-3"></div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <span style="font-size: 2rem; color: var(--gold);">
                                <?= number_format($avgRating, 1) ?>
                            </span>
                            <span class="text-warning">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= round($avgRating) ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </span>
                            <small class="d-block text-muted">(<?= $totalReviews ?> reviews)</small>
                        </div>

                        <a href="post-job.php?fundi=<?= $fundi_user_id ?>" class="btn btn-gold w-100 mb-2">
                            <i class="bi bi-calendar-check"></i> Book Now
                        </a>
                        <a href="job-detail.php?user=<?= $fundi_user_id ?>" class="btn btn-dark w-100">
                            <i class="bi bi-chat-dots"></i> Message
                        </a>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- About -->
                    <div class="card-custom mb-4">
                        <h4><i class="bi bi-person-badge"></i> About Me</h4>
                        <p style="color: var(--gray-300); line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($fundi['bio'] ?? 'No bio available yet.')) ?>
                        </p>
                    </div>

                    <!-- Portfolio -->
                    <?php if (!empty($portfolioImages)): ?>
                        <div class="card-custom mb-4">
                            <h4><i class="bi bi-images"></i> Portfolio (<?= count($portfolioImages) ?>)</h4>
                            <div class="row g-3">
                                <?php foreach ($portfolioImages as $image): ?>
                                    <div class="col-md-4">
                                        <img src="assets/uploads/portfolio/<?= htmlspecialchars($image['image_path']) ?>" 
                                             alt="<?= htmlspecialchars($image['caption'] ?? '') ?>"
                                             class="img-fluid rounded" 
                                             style="height: 200px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#modal<?= $image['id'] ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Reviews -->
                    <div class="card-custom">
                        <h4><i class="bi bi-chat-square-text"></i> Reviews (<?= $totalReviews ?>)</h4>
                        <?php if (empty($reviews)): ?>
                            <p class="text-muted text-center py-4">No reviews yet.</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($review['client_name']) ?></strong>
                                        <span class="text-warning">
                                            <?= str_repeat('★', (int)$review['rating']) ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-1"><?= timeAgo($review['created_at']) ?></p>
                                    <p><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

