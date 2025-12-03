<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeCrafters NG | Premium Profile | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>

    <style>

        .premium-badge {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 80px 0 40px;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.05"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
            background-size: cover;
        }
        
        .company-logo {
            width: 140px;
            height: 140px;
            border-radius: 25px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-accent);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            position: relative;
        }
        
        .verified-badge {
            position: absolute;
            bottom: -10px;
            right: -10px;
            background: var(--premium-gold);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            border: 3px solid white;
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
        
        .chart-container, canvas {
            position: relative;
            margin: auto;
            text-align: center;
            height: 300px;
            /* width: 100%; */
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-accent);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background: var(--primary-accent);
            color: white;
            transform: translateY(-3px);
        }
        
        .gallery-item {
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 3rem;
        }
        
        .project-card {
            border-radius: 12px;
            overflow: hidden;
            background: #f8fafc;
            transition: transform 0.3s ease;
        }
        
        .project-card:hover {
            transform: translateY(-5px);
        }
        
        .article-card {
            border-left: 4px solid var(--primary-accent);
            padding-left: 20px;
        }
        
        .video-container {
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }
    </style>
</head>
<body>

    <?php include_once('event-ticker.php'); ?>
    <?php include_once('header.php'); ?>
    <script>
        document.getElementById('categories').classList.add('active');
        document.getElementById('categories-mobile').classList.add('active');
    </script>

<main class="main-content">
        <!-- Company Header -->
    <section class="profile-header">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-md-2 text-center text-md-start">
                    <div class="company-logo" data-aos="zoom-in">
                        <i class="fas fa-laptop-code"></i>
                        <div class="verified-badge">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-7" data-aos="fade-right">
                    <h1 class="fw-bold mb-2">CodeCrafters NG</h1>
                    <p class="lead mb-3">Enterprise-grade digital solutions for forward-thinking businesses</p>
                    <div class="d-flex flex-wrap mb-3">
                        <span class="category-badge">Web Development</span>
                        <span class="category-badge">Mobile Applications</span>
                        <span class="category-badge">UI/UX Design</span>
                        <span class="category-badge">Cloud Solutions</span>
                        <span class="category-badge">Digital Transformation</span>
                        <span class="category-badge">E-commerce</span>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 text-center text-md-end" data-aos="fade-left">
                    <a href="#" class="btn btn-light mb-2">Visit Website</a>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-1"></i> Lagos & Abuja, Nigeria</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Company Video -->
                <div class="info-card mb-4" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Company Overview</h3>
                    <div class="video-container">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <p class="mt-3 text-muted">Watch our company story and learn about our approach to digital innovation.</p>
                </div>
                
                <!-- Competitive Analytics -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fw-bold mb-4">Performance Analytics</h3>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <h5 class="text-center mb-3">Client Satisfaction</h5>
                            <div class="chart-container">
                                <canvas id="satisfactionChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <h5 class="text-center mb-3">Project Delivery Time</h5>
                            <div class="chart-container">
                                <canvas id="deliveryChart" class="col-12"></canvas>
                            </div>
                        </div>
                        <!-- <div class="col-md-12 mb-4">
                            <h5 class="text-center mb-3">Client Retention</h5>
                            <div class="chart-container">
                                <canvas id="retentionChart"></canvas>
                            </div>
                        </div> -->
                    </div>
                </div>
                
                <!-- Recent Projects -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="fw-bold mb-4">Recent Projects</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="project-card p-3 h-100">
                                <h5 class="fw-bold">FinTech Mobile Banking App</h5>
                                <p class="text-muted">A comprehensive mobile banking solution with biometric authentication and real-time transactions.</p>
                                <span class="badge bg-primary">Completed: Jan 2025</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="project-card p-3 h-100">
                                <h5 class="fw-bold">E-commerce Platform</h5>
                                <p class="text-muted">Multi-vendor marketplace with advanced inventory management and AI recommendations.</p>
                                <span class="badge bg-primary">Completed: Nov 2024</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="project-card p-3 h-100">
                                <h5 class="fw-bold">Healthcare Management System</h5>
                                <p class="text-muted">Enterprise solution for patient records, appointments, and telemedicine capabilities.</p>
                                <span class="badge bg-primary">Completed: Aug 2024</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="project-card p-3 h-100">
                                <h5 class="fw-bold">Educational Learning Platform</h5>
                                <p class="text-muted">Interactive LMS with video courses, assessments, and progress tracking.</p>
                                <span class="badge bg-primary">Completed: Jun 2024</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Company Articles -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="fw-bold mb-4">Company News & Insights</h3>
                    <div class="article-card mb-4">
                        <h5 class="fw-bold">CodeCrafters NG Wins 2025 Digital Innovation Award</h5>
                        <p class="text-muted">We're proud to announce that our work on the FinTech banking app has been recognized with the prestigious Digital Innovation Award.</p>
                        <span class="text-muted small">Published: March 15, 2025</span>
                    </div>
                    <div class="article-card mb-4">
                        <h5 class="fw-bold">Expanding Our Services to East African Market</h5>
                        <p class="text-muted">We're excited to announce our expansion into Kenya and Tanzania, bringing our enterprise solutions to more African businesses.</p>
                        <span class="text-muted small">Published: February 3, 2025</span>
                    </div>
                    <div class="article-card">
                        <h5 class="fw-bold">The Future of AI in Web Development: Our Perspective</h5>
                        <p class="text-muted">Our CTO shares insights on how artificial intelligence is transforming the web development landscape and how we're integrating these technologies.</p>
                        <span class="text-muted small">Published: January 18, 2025</span>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Details -->
                <div class="info-card mb-4" data-aos="fade-left">
                    <h4 class="fw-bold mb-4">Contact Details</h4>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Website</h6>
                        <a href="#" class="text-decoration-none">www.codecrafters.ng</a>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Phone</h6>
                        <p class="mb-0">+234 901 234 5678</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Email</h6>
                        <p class="mb-0">contact@codecrafters.ng</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Team Size</h6>
                        <p class="mb-0">50-100 Employees</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Year Started</h6>
                        <p class="mb-0">2012</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Starting Cost Per Project</h6>
                        <p class="mb-0">₦2,500,000</p>
                    </div>
                </div>
                
                <!-- Profile Strength -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h4 class="fw-bold mb-4">Profile Strength</h4>
                    <div class="chart-container">
                        <canvas id="profileStrengthChart"></canvas>
                    </div>
                </div>
                
                <!-- Gallery -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="200">
                    <h4 class="fw-bold mb-4">Gallery</h4>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="gallery-item">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="gallery-item">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="gallery-item">
                                <i class="fas fa-laptop"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="gallery-item">
                                <i class="fas fa-award"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Location Map -->
                <div class="info-card" data-aos="fade-left" data-aos-delay="300">
                    <h4 class="fw-bold mb-4">Our Offices</h4>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <p class="fw-bold mb-2">Lagos Headquarters</p>
                        <p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> 42, Innovation Way</p>
                        <p class="mb-0">Lekki Phase 1, Lagos</p>
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <p class="fw-bold mb-2">Abuja Office</p>
                        <p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> 15, Digital Transformation Avenue</p>
                        <p class="mb-0">Maitama, Abuja</p>
                    </div>
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="fas fa-map me-2"></i> View Offices on Map
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

    <?php include_once('footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Profile Strength Chart
        const profileCtx = document.getElementById('profileStrengthChart').getContext('2d');
        const profileChart = new Chart(profileCtx, {
            type: 'radar',
            data: {
                labels: ['Profile Completeness', 'Response Rate', 'Client Reviews', 'Project Portfolio', 'Industry Experience', 'Social Proof'],
                datasets: [{
                    label: 'Profile Strength',
                    data: [95, 98, 92, 90, 88, 85],
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

        // Client Satisfaction Chart
        const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
        const satisfactionChart = new Chart(satisfactionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Very Satisfied', 'Satisfied', 'Neutral', 'Unsatisfied'],
                datasets: [{
                    data: [75, 20, 4, 1],
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Project Delivery Time Chart
        const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
        const deliveryChart = new Chart(deliveryCtx, {
            type: 'bar',
            data: {
                labels: ['CodeCrafters NG', 'Industry Average'],
                datasets: [{
                    label: 'Days to Delivery',
                    data: [45, 68],
                    backgroundColor: [
                        '#2563eb',
                        '#94a3b8'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Days'
                        }
                    }
                }
            }
        });

        // Client Retention Chart
        const retentionCtx = document.getElementById('retentionChart').getContext('2d');
        const retentionChart = new Chart(retentionCtx, {
            type: 'line',
            data: {
                labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
                datasets: [{
                    label: 'Client Retention Rate',
                    data: [82, 85, 88, 90, 92, 94],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 80,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Percentage %'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>