<!-- 404.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | Tech Media Directory</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="./assets/css/styles.css">
    
    <style>
        .error-section {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4rem 0;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: var(--primary-accent);
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-illustration {
            max-width: 400px;
            margin: 0 auto 2rem;
        }
    </style>
</head>
<body>

    <!-- Modern Header -->
    <div class="navbar-container">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="index.html">
                    <i class="bi bi-hexagon-fill text-primary-accent"></i>
                    TECH<span>MEDIA</span>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="collapse navbar-collapse desktop-nav">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-center">
                        <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="about-us.html">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="get-listed.html">Get Listed</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact-us.html">Contact</a></li>
                    </ul>
                    <div class="d-flex mt-3 mt-lg-0">
                        <button class="btn-get-listed">Get Listed</button>
                    </div>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle navbar-toggler border-0" type="button">
                    <i class="fas fa-bars text-primary-accent" style="font-size: 1.5rem;"></i>
                </button>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu-container" id="mobileMenu">
        <div class="mobile-menu-header">
            <a class="navbar-brand" href="index.html">
                <i class="bi bi-hexagon-fill text-primary-accent"></i>
                TECH<span>MEDIA</span>
            </a>
            <button class="mobile-menu-close border-0 bg-transparent">
                <i class="fas fa-times text-primary-accent" style="font-size: 1.5rem;"></i>
            </button>
        </div>
        
        <a href="index.html" class="mobile-nav-link">Home</a>
        <a href="about-us.html" class="mobile-nav-link">About</a>
        <a href="get-listed.html" class="mobile-nav-link">Get Listed</a>
        <a href="contact-us.html" class="mobile-nav-link">Contact</a>
        
        <div class="mt-4 pt-4 border-top border-secondary">
            <button class="btn-get-listed w-100">Get Listed</button>
        </div>
    </div>

    <!-- 404 Section -->
    <section class="error-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="error-code">404</div>
                    <h2 class="fw-bold mb-4">Page Not Found</h2>
                    <p class="text-muted mb-5 fs-5">The page you're looking for doesn't exist or has been moved.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="index.html" class="btn btn-primary rounded-pill px-4 py-2">Go Home</a>
                        <a href="contact-us.html" class="btn btn-outline-primary rounded-pill px-4 py-2">Report Issue</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5">
                    <h4 class="text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-hexagon-fill text-primary-accent"></i>
                        TECH<span>MEDIA</span>
                    </h4>
                    <p class="text-muted pe-4">The number one digital ecosystem for Nigerian tech companies, startups, and innovators. Connecting the future, today.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h6 class="text-dark mb-3 text-uppercase fw-bold small ls-1">Company</h6>
                    <a href="about-us.html" class="footer-link">About Us</a>
                    <a href="#" class="footer-link">Careers</a>
                    <a href="privacy-policy.html" class="footer-link">Privacy Policy</a>
                    <a href="terms-of-service.html" class="footer-link">Terms of Service</a>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h6 class="text-dark mb-3 text-uppercase fw-bold small ls-1">Resources</h6>
                    <a href="#" class="footer-link">Blog</a>
                    <a href="#" class="footer-link">Help Center</a>
                    <a href="media-kit.html" class="footer-link">Media Kit</a>
                    <a href="#" class="footer-link">API Access</a>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="text-dark mb-3 text-uppercase fw-bold small ls-1">Newsletter</h6>
                    <p class="text-muted small">Get the latest tech events and funding news delivered to your inbox.</p>
                    <div class="input-group">
                        <input type="email" class="form-control bg-white border-secondary" placeholder="Enter email">
                        <button class="btn btn-primary" style="background-color: var(--primary-accent); border:none;">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary border-opacity-25 pt-4 mt-4 text-center text-muted small">
                &copy; 2025 Tech Media Directory. All Rights Reserved. Built for Nigeria.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize AOS
            AOS.init({
                once: true,
                offset: 80,
                duration: 1000,
            });

            // Mobile Menu Toggle
            $('.mobile-menu-toggle').click(function() {
                $('#mobileMenu').addClass('active');
            });

            $('.mobile-menu-close').click(function() {
                $('#mobileMenu').removeClass('active');
            });
        });
    </script>
</body>
</html>