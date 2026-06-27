<?php
/**
 * Edit Profile Page - Fixed & Reliable Version
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireRole(ROLE_FUNDI);

$currentUser = getCurrentUser();
$user_id = $currentUser['id'];
global $db;

// Fetch current data
$stmt = $db->prepare("
    SELECT u.*, f.*
    FROM users u
    LEFT JOIN fundi_profiles f ON u.id = f.user_id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$fundi = $stmt->fetch();

if (!$fundi) {
    setFlashMessage('error', 'Profile not found');
    redirect('fundi-dashboard.php');
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        $full_name       = sanitize($_POST['full_name']);
        $phone           = sanitize($_POST['phone']);
        $location        = sanitize($_POST['location']);
        $service_category = sanitize($_POST['service_category']);
        $bio             = sanitize($_POST['bio']);
        $experience_years = (int)$_POST['experience_years'];
        $hourly_rate     = (float)$_POST['hourly_rate'];

        // Profile Image Upload
        $profile_image = $fundi['profile_image'];
        if (!empty($_FILES['profile_image']['name'])) {
            $uploadResult = uploadFile($_FILES['profile_image'], 'profiles', 5);
            if ($uploadResult['success']) {
                $profile_image = $uploadResult['filename'];
            } else {
                setFlashMessage('error', $uploadResult['message']);
            }
        }

        // Update Users
        $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, profile_image = ? WHERE id = ?");
        $stmt->execute([$full_name, $phone, $profile_image, $user_id]);

        // Update Fundi Profile (Fixed)
        $stmt = $db->prepare("
            INSERT INTO fundi_profiles (user_id, service_category, bio, experience_years, hourly_rate, location)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                service_category = VALUES(service_category),
                bio = VALUES(bio),
                experience_years = VALUES(experience_years),
                hourly_rate = VALUES(hourly_rate),
                location = VALUES(location),
                updated_at = NOW()
        ");
        $stmt->execute([$user_id, $service_category, $bio, $experience_years, $hourly_rate, $location]);

        setFlashMessage('success', 'Profile updated successfully!');
        redirect('fundi-dashboard.php');
    }
}

$pageTitle = 'Edit Profile';
require_once 'includes/header.php';
?>

<!-- Rest of your HTML form remains the same as before -->
<div class="dashboard-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php displayFlashMessage(); ?>

        <div class="dashboard-header">
            <h1><i class="bi bi-person-gear"></i> Edit Profile</h1>
        </div>

        <div class="card-custom p-4">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="row g-4">
                    <div class="col-lg-4 text-center">
                        <img src="<?= getProfileImage($fundi) ?>" class="rounded-circle mb-3" style="width: 160px; height: 160px; object-fit: cover; border: 4px solid var(--gold);">
                        <input type="file" name="profile_image" class="form-control form-control-custom" accept="image/*">
                    </div>

                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control form-control-custom" value="<?= htmlspecialchars($fundi['full_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="tel" name="phone" class="form-control form-control-custom" value="<?= htmlspecialchars($fundi['phone'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label>Service Category</label>
                                <input type="text" name="service_category" class="form-control form-control-custom" 
                                       value="<?= htmlspecialchars($fundi['service_category'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label>Location</label>
                                <input type="text" name="location" class="form-control form-control-custom" 
                                       value="<?= htmlspecialchars($fundi['location'] ?? '') ?>" required>
                            </div>
                        </div>

                        <!-- Other fields remain the same -->
                        <div class="d-flex gap-3 mt-4">
                            <a href="fundi-dashboard.php" class="btn btn-dark flex-grow-1">Cancel</a>
                            <button type="submit" class="btn btn-gold flex-grow-1">Save Changes</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>