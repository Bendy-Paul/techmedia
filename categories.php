<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Development Companies | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>

</head>
<body>

    <?php include_once('event-ticker.php'); ?>
    <?php include_once('header.php'); ?>
    <script>
        document.getElementById('categories').classList.add('active');
        document.getElementById('categories-mobile').classList.add('active');
    </script>

    <!-- Category Hero -->
    <section class="category-hero" data-aos="fade-in" data-aos-duration="1000">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 category-hero-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html" class="text-primary-accent">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-primary-accent">Categories</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Web Development</li>
                        </ol>
                    </nav>
                    <h1 class="display-4 fw-bold mb-4" data-aos="fade-up" data-aos-delay="200">Web Development Companies</h1>
                    <p class="lead text-muted mb-4" data-aos="fade-up" data-aos-delay="300">Discover Nigeria's top web development agencies, freelancers, and tech teams building the digital future of Africa.</p>
                    <div class="d-flex flex-wrap gap-2" data-aos="fade-up" data-aos-delay="400">
                        <span class="badge bg-light text-dark border">Frontend Development</span>
                        <span class="badge bg-light text-dark border">Backend Development</span>
                        <span class="badge bg-light text-dark border">E-commerce Solutions</span>
                        <span class="badge bg-light text-dark border">CMS Development</span>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block" data-aos="zoom-in" data-aos-delay="500">
                    <i class="fas fa-laptop-code text-primary-accent" style="font-size: 10rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Companies -->
    <section class="section-padding featured-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up" data-aos-delay="100">
                <div>
                    <span class="section-badge">Featured</span>
                    <h2 class="fw-bold">Premium Web Development Partners</h2>
                    <p class="text-muted">Top-rated and verified companies with proven track records</p>
                </div>
                <a href="#" class="text-primary-accent text-decoration-none fw-bold">View All Featured <i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                <!-- Featured Company 1 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="featured-card">
                        <span class="featured-badge">Featured</span>
                        <div class="featured-body">
                            <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Partner</span>
                            <div class="company-logo">
                                <i class="fas fa-code"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Code Crafters NG</h4>
                            <p class="text-muted small mb-3">Full-service web development agency specializing in custom web applications and e-commerce solutions for enterprises and startups.</p>
                            
                            <div class="featured-stats">
                                <div class="stat-item">
                                    <span class="stat-value">4.9/5</span>
                                    <span class="stat-label">Client Rating</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">48h</span>
                                    <span class="stat-label">Avg Response</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">150+</span>
                                    <span class="stat-label">Projects</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">₦2M+</span>
                                    <span class="stat-label">Avg Budget</span>
                                </div>
                            </div>
                            
                            <div class="tech-stack">
                                <span class="tech-tag">React</span>
                                <span class="tech-tag">Node.js</span>
                                <span class="tech-tag">MongoDB</span>
                                <span class="tech-tag">AWS</span>
                            </div>
                            
                            <div class="company-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt text-primary-accent"></i>
                                    <span>Lagos, Nigeria</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users text-primary-accent"></i>
                                    <span>15-50 employees</span>
                                </div>
                            </div>
                            <a href="premium-listed.php"><button class="btn-featured">View Profile</button></a>
                        </div>
                    </div>
                </div>
                <!-- Featured Company 2 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="featured-card">
                        <span class="featured-badge">Featured</span>
                        <div class="featured-body">
                            <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Partner</span>
                            <div class="company-logo">
                                <i class="fas fa-magic"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Web Wizards Ltd</h4>
                            <p class="text-muted small mb-3">Creative web solutions with focus on UX/UI design and responsive web development for modern businesses.</p>
                            
                            <div class="featured-stats">
                                <div class="stat-item">
                                    <span class="stat-value">4.7/5</span>
                                    <span class="stat-label">Client Rating</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">24h</span>
                                    <span class="stat-label">Avg Response</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">80+</span>
                                    <span class="stat-label">Projects</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">₦1.5M+</span>
                                    <span class="stat-label">Avg Budget</span>
                                </div>
                            </div>
                            
                            <div class="tech-stack">
                                <span class="tech-tag">Vue.js</span>
                                <span class="tech-tag">Laravel</span>
                                <span class="tech-tag">MySQL</span>
                                <span class="tech-tag">DigitalOcean</span>
                            </div>
                            
                            <div class="company-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt text-primary-accent"></i>
                                    <span>Abuja, Nigeria</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users text-primary-accent"></i>
                                    <span>10-30 employees</span>
                                </div>
                            </div>
                            <button class="btn-featured">View Profile</button>
                        </div>
                    </div>
                </div>
                <!-- Featured Company 3 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="featured-card">
                        <span class="featured-badge">Featured</span>
                        <div class="featured-body">
                            <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Partner</span>
                            <div class="company-logo">
                                <i class="fas fa-cube"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Digital Builders Inc</h4>
                            <p class="text-muted small mb-3">Enterprise web solutions and digital transformation for large corporations and government agencies.</p>
                            
                            <div class="featured-stats">
                                <div class="stat-item">
                                    <span class="stat-value">4.8/5</span>
                                    <span class="stat-label">Client Rating</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">72h</span>
                                    <span class="stat-label">Avg Response</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">200+</span>
                                    <span class="stat-label">Projects</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">₦5M+</span>
                                    <span class="stat-label">Avg Budget</span>
                                </div>
                            </div>
                            
                            <div class="tech-stack">
                                <span class="tech-tag">Angular</span>
                                <span class="tech-tag">.NET</span>
                                <span class="tech-tag">SQL Server</span>
                                <span class="tech-tag">Azure</span>
                            </div>
                            
                            <div class="company-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt text-primary-accent"></i>
                                    <span>Lagos, Nigeria</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users text-primary-accent"></i>
                                    <span>50-100 employees</span>
                                </div>
                            </div>
                            <button class="btn-featured">View Profile</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="section-padding" style="background-color: #f8fafc;" data-aos="fade-up" data-aos-delay="100">
        <div class="container">
            <div class="filter-section" data-aos="zoom-in" data-aos-delay="200">
                <h3 class="filter-title">Filter Companies</h3>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="filter-group">
                            <div class="filter-label">Location</div>
                            <select class="form-select">
                                <option selected>All Locations</option>
                                <option>Lagos</option>
                                <option>Abuja</option>
                                <option>Port Harcourt</option>
                                <option>Ibadan</option>
                                <option>Kano</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="filter-group">
                            <div class="filter-label">Price Range</div>
                            <select class="form-select">
                                <option selected>Any Budget</option>
                                <option>Under ₦500,000</option>
                                <option>₦500,000 - ₦2M</option>
                                <option>₦2M - ₦5M</option>
                                <option>Over ₦5M</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="filter-group">
                            <div class="filter-label">Verification Status</div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn-filter active">All</button>
                                <button class="btn-filter">Verified Only</button>
                                <button class="btn-filter">Unverified</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="filter-group">
                            <div class="filter-label">Team Size</div>
                            <select class="form-select">
                                <option selected>Any Size</option>
                                <option>Freelancer (1)</option>
                                <option>Small (2-10)</option>
                                <option>Medium (11-50)</option>
                                <option>Large (51+)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-outline-primary">Reset Filters</button>
                    <button class="btn btn-primary">Apply Filters</button>
                </div>
            </div>

            <!-- Company Listings -->
            <div class="row g-4">
                <!-- Company 1 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="company-card">
                        <div class="company-body">
                            <div class="company-logo-sm">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Dev Studio NG</h5>
                            <div class="align-items-center mb-2">
                                <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span><br>
                                <span class="badge bg-light text-dark border ms-2">Lagos</span>
                            </div>
                            <p class="text-muted small mb-3">Specializing in React and Node.js applications for startups and SMEs.</p>
                            <div class="d-flex justify-content-between text-muted small">
                                <span><i class="fas fa-users me-1"></i> 5-15 employees</span>
                                <span><i class="fas fa-star me-1 text-warning"></i> 4.5</span>
                            </div>
                            <a href="listed.php"><button class="btn-view-profile">View Profile</button></a>
                        </div>
                    </div>
                </div>
                <!-- Company 2 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="company-card">
                        <div class="company-body">
                            <div class="company-logo-sm">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Web Innovate Ltd</h5>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-light text-dark border">Abuja</span>
                            </div>
                            <p class="text-muted small mb-3">Creative agency focused on innovative web solutions and digital experiences.</p>
                            <div class="d-flex justify-content-between text-muted small">
                                <span><i class="fas fa-users me-1"></i> 20-50 employees</span>
                                <span><i class="fas fa-star me-1 text-warning"></i> 4.3</span>
                            </div>
                            <button class="btn-view-profile">View Profile</button>
                        </div>
                    </div>
                </div>
                <!-- Company 3 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="company-card">
                        <div class="company-body">
                            <div class="company-logo-sm">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Tech Squad Africa</h5>
                            <div class="align-items-center mb-2">
                                <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span><br>
                                <span class="badge bg-light text-dark border ms-2">Port Harcourt</span>
                            </div>
                            <p class="text-muted small mb-3">Full-stack development team with expertise in Laravel and Vue.js.</p>
                            <div class="d-flex justify-content-between text-muted small">
                                <span><i class="fas fa-users me-1"></i> 10-25 employees</span>
                                <span><i class="fas fa-star me-1 text-warning"></i> 4.7</span>
                            </div>
                            <button class="btn-view-profile">View Profile</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-5" data-aos="fade-up" data-aos-delay="100">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding" data-aos="fade-up" data-aos-delay="100">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4">Are you a Web Development Company?</h2>
                    <p class="text-muted mb-4">Join our directory to connect with clients looking for your expertise.</p>
                    <a href="get-listed.php" class="btn btn-primary btn-lg rounded-pill px-5 py-3">List Your Company</a>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('footer.php'); ?>

</body>
</html>