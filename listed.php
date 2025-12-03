<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Web Solutions | Tech Media Directory</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@next/dist/aos.css" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-light: #f8fafc;
            --bg-card: #ffffff;
            --primary-accent: #2563eb;
            --secondary-accent: #3b82f6;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background-color: var(--bg-light);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', sans-serif;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 120px 0 120px;
            color: white;
            border-bottom: 3px solid var(--primary-accent);
        }
        
        .profile-header h1 {
            font-size: 2.5rem;
            letter-spacing: -0.5px;
        }
        
        .profile-header .lead {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .company-logo {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary-accent);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .info-card {
            background: var(--bg-card);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(37, 99, 235, 0.1);
            padding: 25px;
            transition: transform 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .ad-container {
            background: #f1f5f9;
            border: 1px dashed #94a3b8;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
        }
        
        .category-badge {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 8px;
            margin-bottom: 8px;
            display: inline-block;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.html">
                <i class="bi bi-hexagon-fill text-primary"></i>
                TECH<span>MEDIA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="web-development.html">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Companies</a>
                    </li>
                </ul>
                <button class="btn btn-primary">Get Listed</button>
            </div>
        </div>
    </nav>

    <!-- Company Header -->
    <section class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center text-md-start">
                    <div class="company-logo" data-aos="zoom-in">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
                <div class="col-md-7" data-aos="fade-right">
                    <h1 class="fw-bold mb-2">Nova Web Solutions</h1>
                    <p class="lead mb-3 text-white">Building digital experiences that drive business growth</p>
                    <div class="d-flex flex-wrap">
                        <span class="category-badge">Web Development</span>
                        <span class="category-badge">UI/UX Design</span>
                        <span class="category-badge">E-commerce</span>
                    </div>
                </div>
                <div class="col-md-3 text-center text-md-end" data-aos="fade-left">
                    <a href="#" class="btn btn-outline-primary mb-2">Visit Website</a>
                    <p class="small"><i class="fas fa-map-marker-alt me-1"></i> Lagos, Nigeria</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                
                <!-- About Company -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fw-bold mb-4">About Our Company</h3>
                    <p class="mb-3">Nova Web Solutions is a leading web development agency based in Lagos, Nigeria. Founded in 2015, we specialize in creating custom web applications, e-commerce solutions, and digital platforms that help businesses thrive in the digital space.</p>
                    <p class="mb-3">Our team of 15-25 skilled developers, designers, and project managers work collaboratively to deliver projects that exceed client expectations. We believe in the power of technology to transform businesses and create meaningful user experiences.</p>
                    <p>With over 200 projects completed for clients across various industries, we have the expertise and experience to handle projects of any complexity.</p>
                </div>


                <!-- Ad Container -->
                <div class="ad-container mb-4" data-aos="fade-up" data-aos-delay="200"  style="position: sticky; top: 20px;">
                    <h5 class="text-muted">Advertisement</h5>
                    <p class="text-muted">Looking for mobile app development?</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Check Out AppCraft Studios</a>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Details -->
                <div class="info-card mb-4" data-aos="fade-left">
                    <h4 class="fw-bold mb-4">Contact Details</h4>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Website</h6>
                        <a href="#" class="text-decoration-none">www.novawebsolutions.ng</a>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Phone</h6>
                        <p class="mb-0">+234 801 234 5678</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Email</h6>
                        <p class="mb-0">hello@novawebsolutions.ng</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Team Size</h6>
                        <p class="mb-0">15-25 Employees</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Year Started</h6>
                        <p class="mb-0">2015</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Starting Cost Per Project</h6>
                        <p class="mb-0">₦500,000</p>
                    </div>
                </div>

                <!-- Profile Strength Chart -->
                <div class="info-card mb-4" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Profile Strength</h3>
                    <div class="chart-container">
                        <canvas id="profileStrengthChart"></canvas>
                    </div>
                </div>
            
                
                <!-- Location Map -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h4 class="fw-bold mb-4">Location</h4>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> 24, Tech Innovation Drive</p>
                        <p class="mb-2">Yaba, Lagos</p>
                        <p class="mb-0">Nigeria</p>
                    </div>
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="fas fa-map me-2"></i> View on Map
                    </a>
                </div>
                
                <!-- Ad Container -->
                <div class="ad-container" data-aos="fade-left" data-aos-delay="200">
                    <h5 class="text-muted">Advertisement</h5>
                    <p class="text-muted">Need digital marketing services?</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Visit Digital Growth NG</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="fw-bold mb-3">TECH<span class="text-primary">MEDIA</span></h4>
                    <p class="text-light">The definitive directory for Nigeria's tech ecosystem.</p>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h6 class="fw-bold mb-3">Company</h6>
                    <a href="#" class="d-block text-light mb-2 text-decoration-none">About Us</a>
                    <a href="#" class="d-block text-light mb-2 text-decoration-none">Careers</a>
                    <a href="#" class="d-block text-light mb-2 text-decoration-none">Contact</a>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h6 class="fw-bold mb-3">Resources</h6>
                    <a href="#" class="d-block text-light mb-2 text-decoration-none">Blog</a>
                    <a href="#" class="d-block text-light mb-2 text-decoration-none">Help Center</a>
                    <a href="#" class="d-block text-light mb-2 text-decoration-none">API</a>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Newsletter</h6>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button class="btn btn-primary">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 Tech Media Directory. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Profile Strength Chart
        const profileCtx = document.getElementById('profileStrengthChart').getContext('2d');
        const profileChart = new Chart(profileCtx, {
            type: 'radar',
            data: {
                labels: ['Profile Completeness', 'Response Rate', 'Client Reviews', 'Project Portfolio', 'Industry Experience'],
                datasets: [{
                    label: 'Profile Strength',
                    data: [85, 90, 75, 80, 70],
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: '#2563eb',
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#2563eb'
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>