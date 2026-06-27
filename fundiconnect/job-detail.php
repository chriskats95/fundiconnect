<?php
/**
 * Job Detail Page - Clean & Complete Version
 * Fixed for Fundi access to pending jobs
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($job_id <= 0) {
    setFlashMessage('error', 'Invalid job ID');
    redirect('index.php');
}

$current_user_id = $_SESSION['user_id'];
$currentUser = getCurrentUser();

// Fetch job details
$stmt = $db->prepare("
    SELECT jr.*, 
           c.full_name as client_name, c.email as client_email, c.phone as client_phone, c.profile_image as client_image,
           f.full_name as fundi_name, f.email as fundi_email, f.phone as fundi_phone, f.profile_image as fundi_image
    FROM job_requests jr
    JOIN users c ON jr.client_id = c.id
    LEFT JOIN users f ON jr.fundi_id = f.id
    WHERE jr.id = ?
");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    setFlashMessage('error', 'Job not found');
    redirect('index.php');
}

// ==================== PERMISSION LOGIC ====================
$isClient = ($current_user_id == $job['client_id']);
$isAdmin  = hasRole(ROLE_ADMIN);
$isFundi  = hasRole(ROLE_FUNDI);

$canView = false;

if ($isClient || $isAdmin) {
    $canView = true;
} elseif ($isFundi) {
    // Fundi can view jobs assigned to them OR pending jobs matching their category
    $fundiStmt = $db->prepare("SELECT service_category FROM fundi_profiles WHERE user_id = ?");
    $fundiStmt->execute([$current_user_id]);
    $fundiProfile = $fundiStmt->fetch();
    $fundiCategory = $fundiProfile['service_category'] ?? '';

    $canView = (
        ($job['fundi_id'] == $current_user_id) || 
        ($job['status'] === 'pending' && $job['service_category'] === $fundiCategory)
    );
}

if (!$canView) {
    setFlashMessage('error', 'You do not have permission to view this job');
    redirect('index.php');
}
// =======================================================

// Handle Mark as Complete (Fundi only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_complete') {
    if ($isFundi && in_array($job['status'], ['accepted', 'in_progress'])) {
        $stmt = $db->prepare("UPDATE job_requests SET status = 'completed', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$job_id]);

        setFlashMessage('success', 'Job marked as completed successfully!');
        redirect('job-detail.php?id=' . $job_id);
    }
}

// Handle Send Message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = sanitize($_POST['message'] ?? '');
        if (!empty($message)) {
            $receiver_id = $isClient ? ($job['fundi_id'] ?? 0) : $job['client_id'];

            if ($receiver_id) {
                $stmt = $db->prepare("
                    INSERT INTO messages (sender_id, receiver_id, job_id, message, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$current_user_id, $receiver_id, $job_id, $message]);

                setFlashMessage('success', 'Message sent successfully!');
            }
            redirect('job-detail.php?id=' . $job_id);
        }
    }
}

// Fetch conversation messages
$stmt = $db->prepare("
    SELECT m.*, u.full_name as sender_name, u.profile_image as sender_image
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.job_id = ?
    ORDER BY m.created_at ASC
");
$stmt->execute([$job_id]);
$messages = $stmt->fetchAll();

// Mark messages as read
if (!empty($messages)) {
    $stmt = $db->prepare("UPDATE messages SET is_read = TRUE 
                         WHERE job_id = ? AND receiver_id = ? AND is_read = FALSE");
    $stmt->execute([$job_id, $current_user_id]);
}

$pageTitle = htmlspecialchars($job['title']);
require_once 'includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php displayFlashMessage(); ?>

        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1><?= htmlspecialchars($job['title']) ?></h1>
                    <p class="text-muted">Posted <?= timeAgo($job['created_at']) ?></p>
                </div>
                <span class="badge-status badge-<?= htmlspecialchars($job['status']) ?>">
                    <?= ucfirst(str_replace('_', ' ', $job['status'])) ?>
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card-custom mb-4">
                    <h5 class="mb-3">Job Description</h5>
                    <p style="white-space: pre-wrap; color: var(--gray-300); line-height: 1.7;">
                        <?= htmlspecialchars($job['description']) ?>
                    </p>

                    <div class="row mt-4 text-muted">
                        <div class="col-sm-6">
                            <strong>Category:</strong> <?= htmlspecialchars($job['service_category']) ?><br>
                            <strong>Location:</strong> <?= htmlspecialchars($job['location']) ?>
                        </div>
                        <?php if (!empty($job['budget'])): ?>
                        <div class="col-sm-6">
                            <strong>Budget:</strong> 
                            <span style="color: var(--gold);">UGX <?= number_format($job['budget']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Messages -->
                <div class="card-custom">
                    <h5 class="mb-3"><i class="bi bi-chat-dots"></i> Messages</h5>
                    
                    <div id="messagesContainer" style="max-height: 420px; overflow-y: auto; background: var(--black-soft); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <?php if (empty($messages)): ?>
                            <p class="text-center text-muted py-5">No messages yet. Start the conversation below.</p>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): 
                                $isMe = ($msg['sender_id'] == $current_user_id);
                            ?>
                                <div class="d-flex <?= $isMe ? 'justify-content-end' : '' ?> mb-3">
                                    <div style="max-width: 75%;">
                                        <div style="background: <?= $isMe ? 'var(--gold)' : 'var(--gray-800)' ?>; 
                                                    color: <?= $isMe ? '#000' : '#fff' ?>; 
                                                    padding: 12px 16px; border-radius: 12px;">
                                            <strong><?= htmlspecialchars($msg['sender_name']) ?></strong><br>
                                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                        </div>
                                        <small class="text-muted d-block mt-1 <?= $isMe ? 'text-end' : '' ?>">
                                            <?= timeAgo($msg['created_at']) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Send Message Form -->
                    <?php if (($isClient && $job['fundi_id']) || $isFundi): ?>
                        <form method="POST" action="job-detail.php?id=<?= $job_id ?>">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <input type="hidden" name="action" value="send_message">
                            <div class="d-flex gap-2">
                                <textarea name="message" class="form-control-custom" rows="2" 
                                    placeholder="Type your message..." required></textarea>
                                <button type="submit" class="btn btn-gold">Send</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <!-- Client Card -->
                <div class="card-custom mb-4">
                    <h6>Client</h6>
                    <div class="text-center my-3">
                        <img src="<?= getProfileImage(['profile_image' => $job['client_image']]) ?>" 
                             class="rounded-circle" style="width: 85px; height: 85px; border: 3px solid var(--gold);">
                    </div>
                    <h5 class="text-center"><?= htmlspecialchars($job['client_name']) ?></h5>
                </div>

                <!-- Assigned Fundi -->
                <?php if ($job['fundi_id']): ?>
                <div class="card-custom mb-4">
                    <h6>Assigned Fundi</h6>
                    <div class="text-center my-3">
                        <img src="<?= getProfileImage(['profile_image' => $job['fundi_image']]) ?>" 
                             class="rounded-circle" style="width: 85px; height: 85px; border: 3px solid var(--gold);">
                    </div>
                    <h5 class="text-center"><?= htmlspecialchars($job['fundi_name']) ?></h5>
                </div>
                <?php endif; ?>

                <!-- Fundi Action -->
                <?php if ($isFundi && in_array($job['status'], ['accepted', 'in_progress'])): ?>
                <div class="card-custom">
                    <form method="POST" onsubmit="return confirm('Mark this job as completed?')">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="action" value="mark_complete">
                        <button type="submit" class="btn btn-gold w-100">
                            <i class="bi bi-check-circle-fill"></i> Mark as Completed
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto scroll to bottom of messages
    const msgContainer = document.getElementById('messagesContainer');
    if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;
</script>
</body>
</html>