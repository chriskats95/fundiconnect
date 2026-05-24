<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact FundiConnect - Get in touch with our team for support, partnerships, or general inquiries.">
    <title>Contact Us | FundiConnect</title>
    
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#fundis">Find Fundis</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex gap-2">
                    <a href="login.php" class="btn btn-outline-gold">Login</a>
                    <a href="register.php" class="btn btn-gold">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="section section-dark" style="padding-top: 140px;">
        <div class="container">
            <div class="text-center mb-5">
                <div class="gold-line"></div>
                <h1 class="mb-3">Get in Touch</h1>
                <p class="text-muted mx-auto" style="max-width: 600px;">
                    Have questions, feedback, or need support? We are here to help. Reach out to us through any of the channels below.
                </p>
            </div>
            
            <!-- Contact Info Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h5>Visit Us</h5>
                        <p>Plot 45, Kampala Road<br>Kampala, Uganda</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h5>Email Us</h5>
                        <p>info@fundiconnect.ug<br>support@fundiconnect.ug</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h5>Call Us</h5>
                        <p>+256 700 123 456<br>+256 800 987 654</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="section section-darker">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Send Us a Message</h2>
                    <p class="text-muted mb-4">
                        Fill out the form below and our team will get back to you within 24 hours. Whether you need help with your account, want to report an issue, or have a business inquiry, we are ready to assist.
                    </p>
                    
                    <form id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">First Name</label>
                                <input type="text" class="form-control form-control-custom" placeholder="John" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Last Name</label>
                                <input type="text" class="form-control form-control-custom" placeholder="Doe" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 mt-3">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" class="form-control form-control-custom" placeholder="john@example.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-custom">Phone Number (Optional)</label>
                            <input type="tel" class="form-control form-control-custom" placeholder="+256 700 000 000">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-custom">Subject</label>
                            <select class="form-control form-control-custom form-select" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="billing">Billing Question</option>
                                <option value="partnership">Partnership Opportunity</option>
                                <option value="feedback">Feedback</option>
                                <option value="report">Report an Issue</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label-custom">Your Message</label>
                            <textarea class="form-control form-control-custom" rows="5" placeholder="Tell us how we can help you..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-gold btn-lg w-100">
                            Send Message <i class="bi bi-send ms-2"></i>
                        </button>
                    </form>
                </div>
                
                <div class="col-lg-6">
                    <div class="card-custom p-4">
                        <h4 class="mb-4">Frequently Asked Questions</h4>
                        
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item" style="background: transparent; border-color: var(--gray-800);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" 
                                            style="background: var(--gray-900); color: var(--white);">
                                        How do I hire a fundi?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="background: var(--gray-900);">
                                        Simply search for the service you need, browse through verified fundis in your area, check their ratings and reviews, then send a job request. The fundi will respond to your request and you can discuss details before confirming.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item" style="background: transparent; border-color: var(--gray-800);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2"
                                            style="background: var(--gray-900); color: var(--white);">
                                        How do I become a verified fundi?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="background: var(--gray-900);">
                                        Register as a fundi, complete your profile with your skills and experience, upload identification documents, and submit for verification. Our team will review your application within 48 hours.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item" style="background: transparent; border-color: var(--gray-800);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3"
                                            style="background: var(--gray-900); color: var(--white);">
                                        Is FundiConnect free to use?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="background: var(--gray-900);">
                                        Yes! Creating an account and browsing fundis is completely free for clients. Fundis can also join for free. We only charge a small service fee on completed jobs.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item" style="background: transparent; border-color: var(--gray-800);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4"
                                            style="background: var(--gray-900); color: var(--white);">
                                        How are fundis verified?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="background: var(--gray-900);">
                                        We verify fundis through ID verification, skill assessment, and background checks. Verified fundis display a gold badge on their profile to indicate they have passed our verification process.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item" style="background: transparent; border-color: var(--gray-800);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5"
                                            style="background: var(--gray-900); color: var(--white);">
                                        What if I am not satisfied with the work?
                                    </button>
                                </h2>
                                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="background: var(--gray-900);">
                                        We have a dispute resolution process in place. If you are not satisfied, you can report the issue through your dashboard. Our support team will mediate and help resolve the matter fairly.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 rounded text-center" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3);">
                            <i class="bi bi-headset fs-3 d-block mb-2" style="color: var(--gold);"></i>
                            <h5>Need More Help?</h5>
                            <p class="text-muted mb-3 small">Our support team is available 24/7</p>
                            <a href="tel:+256700123456" class="btn btn-gold btn-sm">
                                <i class="bi bi-telephone me-2"></i>Call Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="section section-dark">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-3">Find Us</h2>
                <p class="text-muted">Visit our office in Kampala</p>
            </div>
            
            <div class="card-custom p-0 overflow-hidden">
                <div style="background: var(--gray-800); height: 400px; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <i class="bi bi-geo-alt fs-1 mb-3" style="color: var(--gold);"></i>
                        <h5>FundiConnect Headquarters</h5>
                        <p class="text-muted">Plot 45, Kampala Road, Kampala, Uganda</p>
                        <a href="https://maps.google.com/?q=Kampala,Uganda" target="_blank" class="btn btn-gold">
                            <i class="bi bi-map me-2"></i>Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Section -->
    <section class="section section-darker">
        <div class="container text-center">
            <h2 class="mb-3">Connect With Us</h2>
            <p class="text-muted mb-4">Follow us on social media for updates, tips, and community stories</p>
            
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="#" class="btn btn-dark btn-lg">
                    <i class="bi bi-facebook me-2"></i>Facebook
                </a>
                <a href="#" class="btn btn-dark btn-lg">
                    <i class="bi bi-twitter-x me-2"></i>Twitter
                </a>
                <a href="#" class="btn btn-dark btn-lg">
                    <i class="bi bi-instagram me-2"></i>Instagram
                </a>
                <a href="#" class="btn btn-dark btn-lg">
                    <i class="bi bi-linkedin me-2"></i>LinkedIn
                </a>
                <a href="#" class="btn btn-dark btn-lg">
                    <i class="bi bi-youtube me-2"></i>YouTube
                </a>
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
                        <li><a href="index.php#services">Services</a></li>
                        <li><a href="index.php#how-it-works">How It Works</a></li>
                        <li><a href="index.php#fundis">Find Fundis</a></li>
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

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--black-card); border-color: var(--gray-800);">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.15);">
                            <i class="bi bi-check-lg fs-1" style="color: var(--success);"></i>
                        </div>
                    </div>
                    <h4 class="mb-2">Message Sent!</h4>
                    <p class="text-muted mb-4">Thank you for reaching out. Our team will get back to you within 24 hours.</p>
                    <button type="button" class="btn btn-gold" data-bs-dismiss="modal">Got it</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
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

        // Contact form submission
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Reset form
            this.reset();
        });
    </script>
</body>
</html>
