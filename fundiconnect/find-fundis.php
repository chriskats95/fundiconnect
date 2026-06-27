<?php
/**
 * Find Fundis Page - Listing Page
 * Clients can browse and search fundis
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin(); // Allow both clients and fundis to browse

// Get search/filter parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Fetch fundis with filters
$query = "
    SELECT u.*, fp.service_category, fp.location, fp.rating, fp.verification_status
    FROM users u
    JOIN fundi_profiles fp ON u.id = fp.user_id
    WHERE u.role = 'fundi' AND u.status = 'active'
";

$params = [];

if (!empty($search)) {
    $query .= " AND (u.full_name LIKE ? OR fp.service_category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND fp.service_category = ?";
    $params[] = $category;
}

$query .= " ORDER BY fp.rating DESC, u.full_name ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$fundis = $stmt->fetchAll();

$pageTitle = 'Find Fundis';
require_once 'includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php displayFlashMessage(); ?>

        <div class="dashboard-header">
            <h1><i class="bi bi-search"></i> Find Skilled Fundis</h1>
            <p>Browse verified professionals near you</p>
        </div>

        <!-- Search & Filter -->
        <div class="card-search p-4 mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control form-control-custom" 
                           placeholder="Search by name or skill..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-control form-control-custom">
                        <option value="">All Services</option>
                        <?php
                        $catStmt = $db->query("SELECT DISTINCT service_category FROM fundi_profiles ORDER BY service_category");
                        while ($cat = $catStmt->fetch()) {
                            $selected = ($cat['service_category'] == $category) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($cat['service_category'])."' $selected>".htmlspecialchars($cat['service_category'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-gold w-100">Search</button>
                </div>
            </form>
        </div>

        <?php if (empty($fundis)): ?>
            <div class="card-custom text-center py-5">
                <i class="bi bi-search fs-1 text-muted mb-3"></i>
                <h4>No fundis found</h4>
                <p>Try changing your search terms</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($fundis as $fundi): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-custom h-100">
                            <div class="text-center pt-4">
                                <img src="<?= getProfileImage($fundi) ?>" 
                                     alt="<?= htmlspecialchars($fundi['full_name']) ?>"
                                     class="rounded-circle mb-3" 
                                     style="width: 110px; height: 110px; border: 3px solid var(--gold);">
                            </div>
                            
                            <div class="text-center">
                                <h5><?= htmlspecialchars($fundi['full_name']) ?></h5>
                                <p class="text-gold"><?= htmlspecialchars($fundi['service_category']) ?></p>
                                <p class="text-muted small">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($fundi['location'] ?? 'Location not set') ?>
                                </p>
                            </div>

                            <div class="d-flex justify-content-center gap-1 my-3">
                                <?php 
                                $rating = round($fundi['rating'] ?? 0);
                                for($i=1; $i<=5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $rating ? '-fill' : '' ?>" style="color: var(--gold);"></i>
                                <?php endfor; ?>
                            </div>

                            <div class="text-center mt-auto pb-4">
                                <a href="fundi-profile.php?id=<?= $fundi['id'] ?>" class="btn btn-gold w-75">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>