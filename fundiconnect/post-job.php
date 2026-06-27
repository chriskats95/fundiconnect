<?php
/**
 * Post New Job Page - Complete & Clean Version
 * For Clients
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireRole(ROLE_CLIENT);   // Only clients can post jobs

$currentUser = getCurrentUser();
$client_id = $currentUser['id'];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        $title            = sanitize($_POST['title']);
        $service_category = sanitize($_POST['service_category']);
        $description      = sanitize($_POST['description']);
        $location         = sanitize($_POST['location']);
        $budget           = !empty($_POST['budget']) ? (float)$_POST['budget'] : null;
        $scheduled_date   = sanitize($_POST['scheduled_date'] ?? null);
        $scheduled_time   = sanitize($_POST['scheduled_time'] ?? null);
        $is_emergency     = isset($_POST['is_emergency']) ? 1 : 0;

        if (empty($title) || empty($service_category) || empty($description) || empty($location)) {
            setFlashMessage('error', 'Please fill all required fields');
        } else {
            $stmt = $db->prepare("
                INSERT INTO job_requests (
                    client_id, service_category, title, description, location, 
                    budget, is_emergency, scheduled_date, scheduled_time, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $client_id,
                $service_category,
                $title,
                $description,
                $location,
                $budget,
                $is_emergency,
                $scheduled_date,
                $scheduled_time
            ]);

            $newJobId = $db->lastInsertId();

            setFlashMessage('success', 'Job posted successfully! Fundis will be notified.');
            redirect('my-jobs.php');
        }
    }
}

$pageTitle = 'Post a New Job';
require_once 'includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php displayFlashMessage(); ?>

        <div class="dashboard-header">
            <h1><i class="bi bi-plus-circle"></i> Post a New Job</h1>
            <p>Describe what you need help with</p>
        </div>

        <div class="card-custom p-4">
            <form method="POST" action="post-job.php">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label-custom">Job Title</label>
                        <input type="text" name="title" class="form-control form-control-custom" 
                               placeholder="e.g. Need urgent painting for 3 bedroom house" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Service Category</label>
                        <input type="text" name="service_category" class="form-control form-control-custom" 
                               placeholder="e.g. Painting, Solar Installation, Plumbing, Generator Repair, etc." required>
                        <small class="text-muted">Be specific so right fundis can see your job</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Location</label>
                        <input type="text" name="location" class="form-control form-control-custom" 
                               placeholder="e.g. Kampala, Wakiso, Entebbe" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Budget (UGX) - Optional</label>
                        <input type="number" name="budget" class="form-control form-control-custom" 
                               placeholder="500000" min="0" step="1000">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Preferred Date</label>
                        <input type="date" name="scheduled_date" class="form-control form-control-custom">
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Job Description</label>
                        <textarea name="description" class="form-control form-control-custom" rows="6" 
                            placeholder="Describe the job in detail. Include any important requirements..." required></textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_emergency" id="emergency" class="form-check-input">
                            <label for="emergency" class="form-check-label text-warning">
                                <strong>🚨 This is an Emergency Job</strong>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="client-dashboard.php" class="btn btn-dark flex-grow-1">Cancel</a>
                        <button type="submit" class="btn btn-gold flex-grow-1">
                            <i class="bi bi-send"></i> Post Job
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>