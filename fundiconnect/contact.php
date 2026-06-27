<?php
/**
 * Contact Page
 * Public contact form with Google reCAPTCHA v3
 * Sends email to admin and saves to database
 */

require_once 'config/config.php';
require_once 'includes/functions.php';

// reCAPTCHA Configuration
// TODO: Replace with your actual reCAPTCHA keys from https://www.google.com/recaptcha/admin
define('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'); // Test key - Replace with your actual site key
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'); // Test key - Replace with your actual secret key

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $db;
    
    // Get form data
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    $recaptchaToken = $_POST['recaptcha_token'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Invalid email address';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }
    
    // Verify reCAPTCHA
    if (empty($recaptchaToken)) {
        $errors[] = 'reCAPTCHA verification failed. Please try again.';
    } else {
        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaData = [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $recaptchaToken,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($recaptchaData)
            ]
        ];
        
        $context = stream_context_create($options);
        $response = @file_get_contents($recaptchaUrl, false, $context);
        
        if ($response) {
            $responseData = json_decode($response, true);
            
            if (!$responseData['success'] || $responseData['score'] < 0.5) {
                $errors[] = 'reCAPTCHA verification failed. You might be a bot.';
            }
        } else {
            // If reCAPTCHA service is unreachable, log and continue (don't block legitimate users)
            error_log("reCAPTCHA verification failed: Unable to reach Google servers");
        }
    }
    
    if (empty($errors)) {
        try {
            // Save to database
            $stmt = $db->prepare("
                INSERT INTO contact_submissions (name, email, subject, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $subject, $message]);
            
            // Send email to admin
            // TODO: Implement PHPMailer email sending
            /*
            require_once 'includes/PHPMailer/PHPMailer.php';
            require_once 'includes/PHPMailer/SMTP.php';
            require_once 'includes/PHPMailer/Exception.php';
            
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;
            
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'your@gmail.com';
                $mail->Password   = 'your_app_password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                
                $mail->setFrom('noreply@fundiconnect.com', 'FundiConnect');
                $mail->addAddress('admin@fundiconnect.com', 'Admin');
                $mail->addReplyTo($email, $name);
                
                $mail->isHTML(true);
                $mail->Subject = 'New Contact Form Submission: ' . $subject;
                $mail->Body    = "
                    <h2>New Contact Form Submission</h2>
                    <p><strong>From:</strong> {$name} ({$email})</p>
                    <p><strong>Subject:</strong> {$subject}</p>
                    <p><strong>Message:</strong></p>
                    <p>{$message}</p>
                ";
                
                $mail->send();
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
            }
            */
            
            setFlashMessage('success', 'Thank you for contacting us! We will get back to you soon.');
            redirect('contact.php');
            
        } catch (PDOException $e) {
            error_log("Contact Form Error: " . $e->getMessage());
            setFlashMessage('error', 'Failed to send your message. Please try again.');
        }
    } else {
        setFlashMessage('error', implode('<br>', $errors));
    }
}

$pageTitle = 'Contact Us';
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

    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo RECAPTCHA_SITE_KEY; ?>"></script>
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
                        <a class="nav-link" href="find-fundis.php">Find Fundis</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Contact</a>
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

    <!-- Contact Section -->
    <div class="section" style="padding-top: 120px;">
        <div class="container">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <div class="gold-line"></div>
                <h1 class="section-title">Get In Touch</h1>
                <p class="section-subtitle">
                    Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-7 mb-4">
                    <div class="card-custom">
                        <h4 class="mb-4">Send Us a Message</h4>
                        
                        <form method="POST" action="contact.php" id="contactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Your Name *</label>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control-custom" 
                                           placeholder="John Doe"
                                           required
                                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Your Email *</label>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control-custom" 
                                           placeholder="john@example.com"
                                           required
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label-custom">Subject *</label>
                                <input type="text" 
                                       name="subject" 
                                       class="form-control-custom" 
                                       placeholder="How can we help you?"
                                       required
                                       value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label-custom">Message *</label>
                                <textarea name="message" 
                                          class="form-control-custom" 
                                          rows="6" 
                                          placeholder="Tell us more about your inquiry..."
                                          required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                                <small style="color: var(--gray-400);">
                                    Minimum 10 characters
                                </small>
                            </div>

                            <!-- Hidden reCAPTCHA token field -->
                            <input type="hidden" name="recaptcha_token" id="recaptchaToken">

                            <!-- reCAPTCHA Notice -->
                            <div class="alert mb-3" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                                <i class="bi bi-shield-check"></i> 
                                This site is protected by reCAPTCHA and the Google
                                <a href="https://policies.google.com/privacy" target="_blank" style="color: var(--gold); text-decoration: underline;">Privacy Policy</a> and
                                <a href="https://policies.google.com/terms" target="_blank" style="color: var(--gold); text-decoration: underline;">Terms of Service</a> apply.
                            </div>

                            <button type="submit" class="btn btn-gold" id="submitBtn">
                                <i class="bi bi-send"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-5">
                    <!-- Contact Info Cards -->
                    <div class="contact-info-card mb-4">
                        <div class="contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h5>Email Us</h5>
                        <p>support@fundiconnect.com</p>
                        <small style="color: var(--gray-500);">We'll respond within 24 hours</small>
                    </div>

                    <div class="contact-info-card mb-4">
                        <div class="contact-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h5>Call Us</h5>
                        <p>+256 700 000 000</p>
                        <small style="color: var(--gray-500);">Mon-Fri from 8am to 6pm</small>
                    </div>

                    <div class="contact-info-card mb-4">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h5>Visit Us</h5>
                        <p>Kampala, Uganda</p>
                        <small style="color: var(--gray-500);">Plot 123, Example Street</small>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <h5>Business Hours</h5>
                        <p>Monday - Friday: 8am - 6pm</p>
                        <p style="margin: 0;">Saturday: 9am - 2pm</p>
                        <small style="color: var(--gray-500);">Closed on Sundays</small>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="card-custom mt-5">
                <h4 class="mb-4">
                    <i class="bi bi-question-circle" style="color: var(--gold);"></i> 
                    Frequently Asked Questions
                </h4>
                
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item" style="background: var(--black-soft); border: 1px solid var(--gray-800); margin-bottom: 1rem; border-radius: var(--radius-md);">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" 
                                    style="background: var(--black-soft); color: var(--white); border: none; padding: 1.25rem;">
                                How do I hire a fundi?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="color: var(--gray-300); padding: 1.25rem;">
                                Simply browse our verified fundis, view their profiles and reviews, then click "Book Now" to post a job request. The fundi will be notified and can accept your request.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" style="background: var(--black-soft); border: 1px solid var(--gray-800); margin-bottom: 1rem; border-radius: var(--radius-md);">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2"
                                    style="background: var(--black-soft); color: var(--white); border: none; padding: 1.25rem;">
                                How do fundis get verified?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="color: var(--gray-300); padding: 1.25rem;">
                                Fundis submit their credentials and work documents during registration. Our admin team reviews each application and verifies the fundi's skills and experience before approval.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" style="background: var(--black-soft); border: 1px solid var(--gray-800); margin-bottom: 1rem; border-radius: var(--radius-md);">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3"
                                    style="background: var(--black-soft); color: var(--white); border: none; padding: 1.25rem;">
                                What payment methods are accepted?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="color: var(--gray-300); padding: 1.25rem;">
                                Payment is arranged directly between you and the fundi. You can negotiate payment methods (cash, mobile money, bank transfer) based on your preference and the job agreement.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" style="background: var(--black-soft); border: 1px solid var(--gray-800); border-radius: var(--radius-md);">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4"
                                    style="background: var(--black-soft); color: var(--white); border: none; padding: 1.25rem;">
                                Can I cancel a job request?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="color: var(--gray-300); padding: 1.25rem;">
                                Yes, you can cancel pending or accepted job requests from your dashboard. The assigned fundi will be notified immediately. Please be courteous and cancel as early as possible.
                            </div>
                        </div>
                    </div>
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

        // reCAPTCHA v3 Integration
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';
            
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo RECAPTCHA_SITE_KEY; ?>', {action: 'contact'}).then(function(token) {
                    document.getElementById('recaptchaToken').value = token;
                    document.getElementById('contactForm').submit();
                });
            });
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
