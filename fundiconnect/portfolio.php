<?php
/**
 * Portfolio Management Page (Fundi)
 * Allows fundis to upload, manage and delete their portfolio images
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Require fundi login
requireRole(ROLE_FUNDI);

$fundi_user_id = $_SESSION['user_id'];

// Get fundi profile ID
$stmt = $db->prepare("SELECT id FROM fundi_profiles WHERE user_id = ?");
$stmt->execute([$fundi_user_id]);
$fundiProfile = $stmt->fetch();

if (!$fundiProfile) {
    setFlashMessage('error', 'Fundi profile not found. Please complete your profile first.');
    redirect('fundi-dashboard.php');
}

$fundi_profile_id = $fundiProfile['id'];

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        $caption = sanitize($_POST['caption']);
        
        // Validate file upload
        if (!isset($_FILES['portfolio_image']) || $_FILES['portfolio_image']['error'] === UPLOAD_ERR_NO_FILE) {
            setFlashMessage('error', 'Please select an image to upload');
        } else {
            // Upload file
            $uploadResult = uploadFile(
                $_FILES['portfolio_image'],
                ['jpg', 'jpeg', 'png', 'webp'],
                5242880, // 5MB
                'portfolio'
            );
            
            if ($uploadResult['success']) {
                // Insert portfolio image record
                try {
                    $stmt = $db->prepare("
                        INSERT INTO portfolio_images (fundi_id, image_path, caption, uploaded_at) 
                        VALUES (?, ?, ?, NOW())
                    ");
                    $stmt->execute([$fundi_profile_id, $uploadResult['filename'], $caption]);
                    
                    setFlashMessage('success', 'Portfolio image uploaded successfully!');
                } catch (PDOException $e) {
                    error_log("Portfolio Upload Error: " . $e->getMessage());
                    setFlashMessage('error', 'Failed to save portfolio image. Please try again.');
                    
                    // Delete uploaded file if database insert failed
                    $filePath = UPLOADS_PATH . '/portfolio/' . $uploadResult['filename'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            } else {
                setFlashMessage('error', $uploadResult['message']);
            }
        }
    }
    redirect('portfolio.php');
}

// Handle image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token');
    } else {
        $image_id = (int)$_POST['image_id'];
        
        // Fetch image details
        $stmt = $db->prepare("SELECT * FROM portfolio_images WHERE id = ? AND fundi_id = ?");
        $stmt->execute([$image_id, $fundi_profile_id]);
        $image = $stmt->fetch();
        
        if ($image) {
            // Delete from database
            $stmt = $db->prepare("DELETE FROM portfolio_images WHERE id = ?");
            $stmt->execute([$image_id]);
            
            // Delete physical file
            $filePath = UPLOADS_PATH . '/portfolio/' . $image['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            setFlashMessage('success', 'Portfolio image deleted successfully!');
        } else {
            setFlashMessage('error', 'Image not found or you do not have permission to delete it');
        }
    }
    redirect('portfolio.php');
}

// Fetch all portfolio images for this fundi
$stmt = $db->prepare("SELECT * FROM portfolio_images WHERE fundi_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$fundi_profile_id]);
$portfolioImages = $stmt->fetchAll();

$pageTitle = 'My Portfolio';
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
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .portfolio-card {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: var(--black-card);
            border: 1px solid var(--gray-800);
            transition: var(--transition-normal);
        }

        .portfolio-card:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
            box-shadow: var(--shadow-gold);
        }

        .portfolio-card-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            cursor: pointer;
        }

        .portfolio-card-body {
            padding: 1rem;
        }

        .portfolio-card-caption {
            color: var(--gray-300);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .portfolio-card-date {
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        .portfolio-card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .upload-area {
            border: 2px dashed var(--gray-700);
            border-radius: var(--radius-lg);
            padding: 3rem 2rem;
            text-align: center;
            background: var(--black-card);
            transition: var(--transition-normal);
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
        }

        .upload-area.dragover {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.1);
        }

        .upload-icon {
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .preview-container {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            background: var(--black-soft);
            border-radius: var(--radius-md);
            margin-top: 1rem;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: var(--radius-md);
            border: 2px solid var(--gold);
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="fundi-dashboard.php" class="sidebar-brand">
                Fundi<span>Connect</span>
            </a>
            
            <ul class="sidebar-menu">
                <li><a href="fundi-dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                <li><a href="job-requests.php"><i class="bi bi-inbox"></i> Job Requests</a></li>
                <li><a href="my-jobs.php"><i class="bi bi-briefcase"></i> My Jobs</a></li>
                <li><a href="portfolio.php" class="active"><i class="bi bi-images"></i> Portfolio</a></li>
                <li><a href="gallery.php"><i class="bi bi-star"></i> Reviews</a></li>
                <li><a href="job-detail.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
            </ul>
            
            <div class="sidebar-divider"></div>
            
            <ul class="sidebar-menu">
                <li><a href="edit-profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                <li><a href="my-jobs.php"><i class="bi bi-wallet2"></i> Earnings</a></li>
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
                    <h1><i class="bi bi-images"></i> My Portfolio</h1>
                    <p>Showcase your best work to attract more clients</p>
                </div>
                <div class="dashboard-actions">
                    <button type="button" class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-plus-circle"></i> Upload Image
                    </button>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3><?php echo count($portfolioImages); ?></h3>
                                <p>Portfolio Images</p>
                            </div>
                            <div class="stat-card-icon">
                                <i class="bi bi-images"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3>5 MB</h3>
                                <p>Max File Size</p>
                            </div>
                            <div class="stat-card-icon blue">
                                <i class="bi bi-file-earmark-image"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h3>JPG, PNG</h3>
                                <p>Supported Formats</p>
                            </div>
                            <div class="stat-card-icon green">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolio Grid -->
            <?php if (empty($portfolioImages)): ?>
                <div class="card-custom text-center py-5">
                    <i class="bi bi-images" style="font-size: 5rem; color: var(--gray-600); opacity: 0.3;"></i>
                    <h4 class="mt-4" style="color: var(--gray-300);">No Portfolio Images Yet</h4>
                    <p style="color: var(--gray-400);">
                        Start building your portfolio by uploading images of your best work.
                    </p>
                    <button type="button" class="btn btn-gold mt-3" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-plus-circle"></i> Upload Your First Image
                    </button>
                </div>
            <?php else: ?>
                <div class="card-custom">
                    <h4 class="mb-4">
                        Your Portfolio
                        <span style="color: var(--gray-500); font-size: 0.9rem; font-weight: 400;">
                            (<?php echo count($portfolioImages); ?> <?php echo count($portfolioImages) == 1 ? 'image' : 'images'; ?>)
                        </span>
                    </h4>

                    <div class="portfolio-grid">
                        <?php foreach ($portfolioImages as $image): ?>
                            <div class="portfolio-card">
                                <img src="assets/uploads/portfolio/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['caption']); ?>"
                                     class="portfolio-card-img"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#viewModal<?php echo $image['id']; ?>">
                                
                                <div class="portfolio-card-body">
                                    <?php if (!empty($image['caption'])): ?>
                                        <p class="portfolio-card-caption">
                                            <?php echo htmlspecialchars($image['caption']); ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="portfolio-card-caption" style="font-style: italic; color: var(--gray-500);">
                                            No caption
                                        </p>
                                    <?php endif; ?>
                                    
                                    <p class="portfolio-card-date">
                                        <i class="bi bi-clock"></i> Uploaded <?php echo timeAgo($image['uploaded_at']); ?>
                                    </p>

                                    <div class="portfolio-card-actions">
                                        <button type="button" 
                                                class="btn btn-dark btn-sm flex-grow-1"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal<?php echo $image['id']; ?>">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm" 
                                                style="background: var(--danger); color: var(--white); border: none;"
                                                onclick="confirmDelete(<?php echo $image['id']; ?>, '<?php echo htmlspecialchars($image['caption'], ENT_QUOTES); ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal<?php echo $image['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content" style="background: var(--black-card); border: 1px solid var(--gray-800);">
                                        <div class="modal-header" style="border-bottom: 1px solid var(--gray-800);">
                                            <h5 class="modal-title">
                                                <?php echo !empty($image['caption']) ? htmlspecialchars($image['caption']) : 'Portfolio Image'; ?>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="assets/uploads/portfolio/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($image['caption']); ?>"
                                                 class="img-fluid" 
                                                 style="max-height: 80vh; border-radius: var(--radius-md);">
                                            <p class="mt-3" style="color: var(--gray-400);">
                                                <i class="bi bi-clock"></i> Uploaded <?php echo formatDate($image['uploaded_at'], 'F j, Y g:i A'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tips Card -->
            <div class="card-custom mt-4">
                <h5 class="mb-3">
                    <i class="bi bi-lightbulb" style="color: var(--gold);"></i> Portfolio Tips
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                            <div>
                                <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                    Show Your Best Work
                                </strong>
                                <small style="color: var(--gray-400);">
                                    Upload high-quality images of completed projects
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                            <div>
                                <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                    Add Captions
                                </strong>
                                <small style="color: var(--gray-400);">
                                    Describe what you did and materials used
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                            <div>
                                <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                    Keep It Updated
                                </strong>
                                <small style="color: var(--gray-400);">
                                    Regularly add photos of recent projects
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 1.2rem;"></i>
                            <div>
                                <strong style="color: var(--white); display: block; margin-bottom: 0.25rem;">
                                    Show Variety
                                </strong>
                                <small style="color: var(--gray-400);">
                                    Display different types of work you can do
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background: var(--black-card); border: 1px solid var(--gray-800);">
                <div class="modal-header" style="border-bottom: 1px solid var(--gray-800);">
                    <h5 class="modal-title">
                        <i class="bi bi-upload"></i> Upload Portfolio Image
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="portfolio.php" enctype="multipart/form-data" id="uploadForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="upload">

                        <!-- File Input -->
                        <div class="mb-4">
                            <label class="form-label-custom">Select Image *</label>
                            <input type="file" 
                                   name="portfolio_image" 
                                   id="portfolioImageInput"
                                   class="form-control-custom" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   required>
                            <small style="color: var(--gray-400);">
                                Max file size: 5MB. Supported formats: JPG, PNG, WEBP
                            </small>
                        </div>

                        <!-- Preview -->
                        <div id="imagePreview" style="display: none;">
                            <label class="form-label-custom">Preview</label>
                            <div class="preview-container">
                                <img id="previewImg" class="preview-image" src="" alt="Preview">
                                <div class="flex-grow-1">
                                    <p id="previewName" style="color: var(--white); margin: 0;"></p>
                                    <small id="previewSize" style="color: var(--gray-400);"></small>
                                </div>
                                <button type="button" class="btn btn-dark btn-sm" onclick="clearImage()">
                                    <i class="bi bi-x"></i> Remove
                                </button>
                            </div>
                        </div>

                        <!-- Caption -->
                        <div class="mb-3">
                            <label class="form-label-custom">Caption (Optional)</label>
                            <textarea name="caption" 
                                      class="form-control-custom" 
                                      rows="3" 
                                      placeholder="Describe this work... e.g., 'Kitchen plumbing installation with modern fixtures'"></textarea>
                            <small style="color: var(--gray-400);">
                                Add a brief description to help clients understand your work
                            </small>
                        </div>

                        <!-- Guidelines -->
                        <div class="alert" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold); margin: 0;">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Image Guidelines:</strong>
                            <ul class="mb-0 mt-2" style="padding-left: 1.5rem;">
                                <li>Use clear, well-lit photos</li>
                                <li>Show the finished work</li>
                                <li>Avoid blurry or low-quality images</li>
                                <li>Include close-ups of detailed work</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--gray-800);">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gold">
                            <i class="bi bi-upload"></i> Upload Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--black-card); border: 1px solid var(--gray-800);">
                <div class="modal-header" style="border-bottom: 1px solid var(--gray-800);">
                    <h5 class="modal-title">
                        <i class="bi bi-trash" style="color: var(--danger);"></i> Delete Image
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="image_id" id="deleteImageId">
                        
                        <p style="color: var(--gray-300);">
                            Are you sure you want to delete this portfolio image?
                        </p>
                        <p style="color: var(--gray-400); font-size: 0.9rem;">
                            <strong id="deleteImageCaption" style="color: var(--white);"></strong>
                        </p>
                        <div class="alert" style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); margin: 0;">
                            <i class="bi bi-exclamation-triangle"></i> 
                            This action cannot be undone!
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--gray-800);">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: var(--danger); color: var(--white); border: none;">
                            <i class="bi bi-trash"></i> Delete Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        // Image preview
        document.getElementById('portfolioImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size
                if (file.size > 5242880) { // 5MB
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('previewName').textContent = file.name;
                    document.getElementById('previewSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        function clearImage() {
            document.getElementById('portfolioImageInput').value = '';
            document.getElementById('imagePreview').style.display = 'none';
        }

        function confirmDelete(imageId, caption) {
            document.getElementById('deleteImageId').value = imageId;
            document.getElementById('deleteImageCaption').textContent = caption || 'No caption';
            deleteModal.show();
        }

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
