<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech News & Insights | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>

    <style>
        /* News Hero */
        .news-hero {
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
            padding: 120px 0 60px;
            position: relative;
        }
        .news-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 15% 30%, rgba(37, 99, 235, 0.1) 0%, transparent 45%),
                radial-gradient(circle at 85% 70%, rgba(59, 130, 246, 0.05) 0%, transparent 45%);
            z-index: 1;
        }
        .news-hero-content {
            position: relative;
            z-index: 2;
        }

        /* News Card */
        .news-card {
            background: var(--bg-card);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            transition: 0.3s;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }
        .news-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        .news-body {
            padding: 25px;
        }
        .news-category {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 12px;
        }
        .news-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }
        .news-tag {
            background: rgba(37, 99, 235, 0.05);
            color: var(--text-muted);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
        }
        .news-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Category Filter */
        .category-filter {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            margin-bottom: 30px;
        }
        .filter-btn {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            margin: 5px;
            transition: 0.3s;
        }
        .filter-btn:hover, .filter-btn.active {
            background: var(--primary-accent);
            color: white;
        }

        /* Sidebar */
        .sidebar-widget {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            margin-bottom: 30px;
        }
        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .tag-item {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-decoration: none;
            transition: 0.3s;
        }
        .tag-item:hover {
            background: var(--primary-accent);
            color: white;
        }
    </style>
</head>
<body>
    <?php include_once('event-ticker.php'); ?>
    <?php include_once('header.php'); ?>    
    <script>
        document.getElementById('news').classList.add('active');
        document.getElementById('news-mobile').classList.add('active');
    </script>

    <!-- News Hero -->
    <section class="news-hero">
        <div class="container news-hero-content">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <h1 class="display-4 fw-bold mb-4">Tech News & Insights</h1>
                    <p class="lead text-muted mb-4">Stay updated with the latest trends, innovations, and stories from Nigeria's booming tech ecosystem.</p>
                    <div class="input-group input-group-lg mx-auto" style="max-width: 500px;">
                        <input type="text" class="form-control" placeholder="Search articles...">
                        <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Category Filter -->
                <div class="category-filter" data-aos="fade-up">
                    <h5 class="fw-bold mb-3">Filter by Category</h5>
                    <div class="d-flex flex-wrap">
                        <button class="filter-btn active">All</button>
                        <button class="filter-btn">Startups</button>
                        <button class="filter-btn">Funding</button>
                        <button class="filter-btn">Policy</button>
                        <button class="filter-btn">Innovation</button>
                        <button class="filter-btn">Events</button>
                        <button class="filter-btn">Interviews</button>
                    </div>
                </div>

                <!-- Featured Article -->
                <div class="news-card mb-5" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://placehold.co/800x400/f1f5f9/2563eb?text=Featured+Tech+News" class="news-img" alt="Featured Article">
                    <div class="news-body">
                        <span class="news-category">Featured • Innovation</span>
                        <h2 class="fw-bold mb-3">Nigerian AI Startup Secures $5M Funding to Revolutionize Agriculture</h2>
                        <p class="text-muted mb-3">AgriTech Solutions NG has developed an AI-powered platform that helps farmers optimize crop yields using satellite imagery and machine learning algorithms.</p>
                        <div class="news-tags">
                            <span class="news-tag">#AI</span>
                            <span class="news-tag">#Agriculture</span>
                            <span class="news-tag">#Funding</span>
                            <span class="news-tag">#MachineLearning</span>
                        </div>
                        <div class="news-meta">
                            <span><i class="fas fa-user me-1"></i> By Chinedu Okoro</span>
                            <span><i class="fas fa-calendar me-1"></i> March 15, 2025</span>
                            <span><i class="fas fa-clock me-1"></i> 5 min read</span>
                        </div>
                        <a href="article.php" class="btn btn-primary mt-3">Read Full Article</a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories Widget -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="200">
                    <h5 class="fw-bold mb-4">Categories</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-decoration-none d-flex justify-content-between"><span>Startups</span> <span class="text-muted">24</span></a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none d-flex justify-content-between"><span>Funding</span> <span class="text-muted">18</span></a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none d-flex justify-content-between"><span>Policy</span> <span class="text-muted">12</span></a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none d-flex justify-content-between"><span>Innovation</span> <span class="text-muted">31</span></a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none d-flex justify-content-between"><span>Events</span> <span class="text-muted">15</span></a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none d-flex justify-content-between"><span>Interviews</span> <span class="text-muted">9</span></a></li>
                    </ul>
                </div>

                <!-- Tags Widget -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="300">
                    <h5 class="fw-bold mb-4">Popular Tags</h5>
                    <div class="tag-cloud">
                        <a href="#" class="tag-item">AI</a>
                        <a href="#" class="tag-item">Fintech</a>
                        <a href="#" class="tag-item">Funding</a>
                        <a href="#" class="tag-item">Blockchain</a>
                        <a href="#" class="tag-item">Startups</a>
                        <a href="#" class="tag-item">E-commerce</a>
                        <a href="#" class="tag-item">AgriTech</a>
                        <a href="#" class="tag-item">HealthTech</a>
                        <a href="#" class="tag-item">EdTech</a>
                        <a href="#" class="tag-item">Mobile</a>
                    </div>
                </div>

                <!-- Newsletter Widget -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="400">
                    <h5 class="fw-bold mb-3">Stay Updated</h5>
                    <p class="text-muted small mb-3">Get the latest tech news and insights delivered to your inbox.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary">Subscribe</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="news-section container">
                        <!-- News Grid -->
                <div class="row g-4">
                    <!-- Article 1 -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="news-card">
                            <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Startup+Funding" class="news-img" alt="Article">
                            <div class="news-body">
                                <span class="news-category">Funding</span>
                                <h4 class="fw-bold mb-2">Fintech Startup PayNaija Raises $8M Series A</h4>
                                <p class="text-muted small mb-3">The round was led by international venture capital firms looking to tap into Nigeria's growing digital payments market.</p>
                                <div class="news-tags">
                                    <span class="news-tag">#Fintech</span>
                                    <span class="news-tag">#Funding</span>
                                    <span class="news-tag">#Payments</span>
                                </div>
                                <div class="news-meta">
                                    <span><i class="fas fa-calendar me-1"></i> March 12, 2025</span>
                                    <span><i class="fas fa-clock me-1"></i> 3 min read</span>
                                </div>
                                <a href="article.html" class="btn btn-outline-primary btn-sm mt-2">Read More</a>
                            </div>
                        </div>
                    </div>

                    <!-- Article 2 -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="news-card">
                            <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Gov+Policy" class="news-img" alt="Article">
                            <div class="news-body">
                                <span class="news-category">Policy</span>
                                <h4 class="fw-bold mb-2">New Startup Act: What It Means for Nigerian Entrepreneurs</h4>
                                <p class="text-muted small mb-3">The recently passed legislation provides tax incentives and regulatory support for early-stage technology companies.</p>
                                <div class="news-tags">
                                    <span class="news-tag">#Policy</span>
                                    <span class="news-tag">#Startups</span>
                                    <span class="news-tag">#Legislation</span>
                                </div>
                                <div class="news-meta">
                                    <span><i class="fas fa-calendar me-1"></i> March 10, 2025</span>
                                    <span><i class="fas fa-clock me-1"></i> 4 min read</span>
                                </div>
                                <a href="article.html" class="btn btn-outline-primary btn-sm mt-2">Read More</a>
                            </div>
                        </div>
                    </div>

                    <!-- Article 3 -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="news-card">
                            <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Tech+Event" class="news-img" alt="Article">
                            <div class="news-body">
                                <span class="news-category">Events</span>
                                <h4 class="fw-bold mb-2">Lagos Tech Festival 2025: Highlights and Key Takeaways</h4>
                                <p class="text-muted small mb-3">Over 5,000 attendees gathered for Africa's largest tech conference featuring groundbreaking innovations and networking opportunities.</p>
                                <div class="news-tags">
                                    <span class="news-tag">#Events</span>
                                    <span class="news-tag">#Conference</span>
                                    <span class="news-tag">#Networking</span>
                                </div>
                                <div class="news-meta">
                                    <span><i class="fas fa-calendar me-1"></i> March 8, 2025</span>
                                    <span><i class="fas fa-clock me-1"></i> 6 min read</span>
                                </div>
                                <a href="article.html" class="btn btn-outline-primary btn-sm mt-2">Read More</a>
                            </div>
                        </div>
                    </div>

                    <!-- Article 4 -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="news-card">
                            <img src="https://placehold.co/600x400/f1f5f9/2563eb?text=Blockchain" class="news-img" alt="Article">
                            <div class="news-body">
                                <span class="news-category">Innovation</span>
                                <h4 class="fw-bold mb-2">Blockchain Adoption Grows Among Nigerian Financial Institutions</h4>
                                <p class="text-muted small mb-3">Major banks are exploring distributed ledger technology to improve security and reduce transaction costs.</p>
                                <div class="news-tags">
                                    <span class="news-tag">#Blockchain</span>
                                    <span class="news-tag">#Banking</span>
                                    <span class="news-tag">#Innovation</span>
                                </div>
                                <div class="news-meta">
                                    <span><i class="fas fa-calendar me-1"></i> March 5, 2025</span>
                                    <span><i class="fas fa-clock me-1"></i> 5 min read</span>
                                </div>
                                <a href="article.html" class="btn btn-outline-primary btn-sm mt-2">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
    </section>

    <?php include_once('footer.php'); ?>

    <script>
        // Category filter functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>