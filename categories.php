<?php
include_once('./includes/wertyuytre.php');
$category_id = $_GET['slug'] ?? 'web-development';
$category = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND type='company'");
$category->execute([$category_id]);
$category = $category->fetch();
if (!$category) {
    header("Location: 404.html");
    exit();
}
$child_cat = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC LIMIT 4");
$child_cat->execute([$category['id']]);
$child_cat = $child_cat->fetchAll();


$listed_companies = $pdo->prepare("SELECT * FROM companies comp LEFT JOIN company_categories comp_cat ON comp.id = comp_cat.company_id WHERE category_id = ? ORDER BY name ASC");
$listed_companies->execute([$category['id']]);

$featured_companies = $pdo->prepare("SELECT * FROM companies comp LEFT JOIN company_categories comp_cat ON comp.id = comp_cat.company_id WHERE category_id = ? AND is_featured = 1 ORDER BY name ASC");
$featured_companies->execute([$category['id']]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $category['name'] ?> | Tech Media Directory</title>

    <?php include_once('links.php'); ?>

    <style>
                /* --- Hero Section Styling --- */
        .hero-section {
            padding: 80px 0 100px;
            position: relative;
            background: var(--bg-light);
            /* Creating a curved background effect */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23f9faff' fill-opacity='1' d='M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: top;
            background-size: cover;
            min-height: 90vh;
            display: flex;
            align-items: center;
        }
        
        /* Optional: Decorative Bubbles/Shapes */
        .shape-blob {
            position: absolute;
            z-index: 0;
            /* left: 50%; */
            opacity: 0.1;
        }

        .shape-1 { top: 10%; left: 5%; width: 300px; }
        .shape-2 { bottom: 10%; right: 5%; width: 250px; }


        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(78, 121, 200, 0.1);
            color: var(--primary-color);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text-dark);
            margin-bottom: 20px;
        }


        .hero-text {
            color: var(--text-gray);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 35px;
            max-width: 90%;
        }

        .hero-btns {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            padding: 15px 35px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            background: var(--primary-gradient);
            color: white;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(78, 84, 200, 0.2);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .btn-hero-secondary {
            padding: 15px 35px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            background: white;
            color: var(--text-dark);
            border: 2px solid #eee;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-secondary:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: white;
        }

        /* Hero Image Container */
        .hero-image-container {
            position: relative;
            z-index: 2;
        }

        .hero-img-main {
            width: 100%;
            animation: float 6s ease-in-out infinite;
            border-radius: 20px;
            /* Placeholder styling if image fails */
            background: #e0e5ec; 
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Creating a mock illustration using CSS/Icon since I can't generate a real PNG */
        .mock-illustration {
            width: 100%;
            height: auto;
            position: relative;
        }

        /* Statistics Card Floating */
        .floating-card {
            position: absolute;
            /* background: white; */
            padding: 20px;
            border-radius: 15px;
            /* box-shadow: 0 20px 40px rgba(0,0,0,0.08); */
            display: flex;
            align-items: center;
            gap: 15px;
            animation: float-delayed 5s ease-in-out infinite;
            z-index: 3;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .card-icon.orange { background: #fff0e6; color: #ff9f43; }
        .card-icon.blue { background: #e6f7ff; color: #0abde3; }

        .card-1 {
            bottom: 40px;
            left: -30px;
        }

        .card-2 {
            top: 60px;
            right: -20px;
            animation-duration: 7s;
        }

        .stat-text h4 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .stat-text p { margin: 0; font-size: 0.8rem; color: var(--text-gray); }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        @keyframes float-delayed {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-section {
                padding-top: 40px;
                text-align: center;
            }

            .hero-text {
                margin: 0 auto 35px;
            }

            .hero-btns {
                justify-content: center;
            }

            .hero-image-container {
                margin-top: 60px;
            }
            
            .card-1 { left: 0; }
            .card-2 { right: 0; }
            
            .hero-title { font-size: 2.5rem; }
        }

        path, .hero-icon{
            fill: var(--secondary-accent);
            color: var(--secondary-accent);
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


    <!-- Hero Section -->
    <section class="hero-section" data-aos="fade-in" data-aos-duration="1000">
        <!-- Decorative Shape -->
        <svg class="shape-blob shape-1" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <path fill="var(--secondary-accent)" d="M44.7,-76.4C58.9,-69.2,71.8,-59.1,81.6,-46.6C91.4,-34.1,98.1,-19.2,95.8,-4.9C93.5,9.3,82.2,22.9,71.3,34.6C60.4,46.3,49.9,56.1,38.2,63.5C26.5,70.8,13.6,75.7,0.3,75.2C-13,74.7,-26,68.8,-37.1,60.8C-48.2,52.8,-57.4,42.7,-64.7,31.2C-72,19.7,-77.4,6.8,-76.4,-5.8C-75.4,-18.4,-68,-30.7,-58.5,-40.6C-49,-50.5,-37.4,-58,-25.5,-66.4C-13.6,-74.8,-1.4,-84.1,12.2,-84.8C25.8,-85.4,40.5,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
        </svg>

        <i class="hero-icon <?php echo htmlspecialchars($category['icon_class']); ?> shape-blob shape-2 text-grey floating-card d-none d-md-flex d-lg-flex d-xl-flex d-xxl-flex" style="font-size: 10rem; opacity: 0.7;"></i>


        <div class="container">
            <div class="row align-items-center">
                <!-- Text Content -->
                <div class="col-lg-12 text-center">
                    <span class="hero-badge">Top <?php echo htmlspecialchars($category['name']); ?> Platform</span>
                    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="200">
                        Discover Leading <br>
                        <span><?php echo htmlspecialchars($category['name']); ?> Companies</span>
                    </h1>
                    <p class="hero-text">
                        <?php if (!empty($category['description'])) {
                            echo htmlspecialchars($category['description']);
                        } else { ?>
                            Explore the best <?php echo htmlspecialchars($category['name']); ?> agencies, teams, and freelancers in Nigeria. Find the right partner to build your digital future.
                        <?php } ?>
                    </p>
                    <div class="hero-btns">
                        <a href="#companies-list-filter" class="btn btn-hero-primary">
                            View <?php echo htmlspecialchars($category['name']); ?> Companies <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <a href="get-listed.php" class="btn btn-hero-secondary">
                            <i class="fas fa-plus-circle text-primary"></i> List Your Company
                        </a>
                    </div>
                    <div class="mt-4 pt-3 d-flex align-items-center gap-3">
                        <div class="d-flex">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(substr($category['name'],0,1)); ?>+<?php echo urlencode(substr($category['name'],1,1)); ?>&background=random" class="rounded-circle border border-white" width="40" alt="User">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(substr($category['name'],2,1)); ?>+<?php echo urlencode(substr($category['name'],3,1)); ?>&background=random" class="rounded-circle border border-white" width="40" style="margin-left: -15px;" alt="User">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(substr($category['name'],4,1)); ?>+<?php echo urlencode(substr($category['name'],5,1)); ?>&background=random" class="rounded-circle border border-white" width="40" style="margin-left: -15px;" alt="User">
                            <div class="rounded-circle bg-light border border-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; margin-left: -15px; font-size: 0.8rem; font-weight: 700;">
                                +<?php echo number_format($listed_companies->rowCount()); ?>
                            </div>
                        </div>
                        <span class="text-muted small">Active companies in <?php echo htmlspecialchars($category['name']); ?></span>
                    </div>
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
                <?php
                foreach ($featured_companies as $company) {
                    $projects_count = $pdo->prepare("SELECT COUNT(*) as project_count FROM company_projects WHERE company_id = ?");
                    $projects_count->execute([$company['id']]);
                ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="featured-card">
                            <span class="featured-badge">Featured</span>
                            <div class="featured-body">
                                <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Partner</span>
                                <div class="company-logo">
                                    <img src="<?= htmlspecialchars($url_path."/".$company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>" style="height: 100%; width: auto;">

                                </div>
                                <h4 class="fw-bold mb-2"><?= htmlspecialchars($company['name']) ?></h4>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($company['tagline']) ?></p>

                                <div class="featured-stats">
                                    <!-- <div class="stat-item">
                                    <span class="stat-value">4.9/5</span>
                                    <span class="stat-label">Client Rating</span>
                                </div> -->
                                    <div class="stat-item">
                                        <span class="stat-value"><?= htmlspecialchars($company['year_founded'] ?? '2026') ?></span>
                                        <span class="stat-label">Founded</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?= htmlspecialchars($projects_count->fetchColumn()) ?></span>
                                        <span class="stat-label">Projects</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">₦<?= htmlspecialchars($company['starting_cost']) ?>+</span>
                                        <span class="stat-label">Avg Budget</span>
                                    </div>
                                </div>

                                <div class="tech-stack">
                                    <?php
                                    $tech_tags = $pdo->prepare("SELECT * FROM company_categories cc JOIN companies cp ON cc.company_id = cp.id JOIN categories cat ON cc.category_id = cat.id WHERE cc.company_id = ?");
                                    $tech_tags->execute([$company['id']]);
                                    foreach ($tech_tags as $tag) {
                                    ?>
                                        <span class="tech-tag"><?= htmlspecialchars($tag['name']) ?></span>
                                    <?php } ?>
                                </div>

                                <div class="company-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt text-primary-accent"></i>
                                        <span><?= htmlspecialchars($company['starting_cost']) ?>, Nigeria</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-users text-primary-accent"></i>
                                        <span><?= htmlspecialchars($company['team_size']) ?> employees</span>
                                    </div>
                                </div>
                                <a href="<?= $url_path ?>/premium-listed/<?= htmlspecialchars($company['slug']) ?>"><button class="btn-featured">View Profile</button></a>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>

            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="section-padding" style="background-color: #f8fafc;" data-aos="fade-up" data-aos-delay="100" id="companies-list-filter">
        <div class="container">
            <div class="filter-section" data-aos="zoom-in" data-aos-delay="200">
                <h3 class="filter-title">Filter Companies</h3>
                <form id="filter-form">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="filter-group">
                                <div class="filter-label">Location</div>
                                <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['id']) ?>">
                                <select class="form-select" name="location">
                                    <option value="">All Locations</option>
                                    <?php 
                                    
                                    $locations = $pdo->query("SELECT * FROM states ORDER BY name ASC")->fetchAll();
                                    foreach ($locations as $loc) { ?>
                                        <option value="<?= htmlspecialchars($loc['id']) ?>"><?= htmlspecialchars($loc['name']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="filter-group">
                                <div class="filter-label">Price Range</div>
                                <select class="form-select" name="price_range">
                                    <option value="all">Any Budget</option>
                                    <option value="500000">Under ₦500,000</option>
                                    <option value="2000000">₦500,000 - ₦2M</option>
                                    <option value="5000000">₦2M - ₦5M</option>
                                    <option value="10000000">Over ₦5M</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="filter-group">
                                <div class="filter-label">Verification Status</div>
                                <select class="form-select" name="verified">
                                    <option value="">All</option>
                                    <option value="1">Verified Only</option>
                                    <option value="0">Unverified</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="filter-group">
                                <div class="filter-label">Team Size</div>
                                <select class="form-select" name="team_size">
                                    <option value="">Any Size</option>
                                    <option value="1-10">Freelancer (1)</option>
                                    <option value="11-50">Small (2-10)</option>
                                    <option value="51-200">Medium (11-50)</option>
                                    <option value="200+">Large (51+)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="reset" class="btn btn-outline-primary">Reset Filters</button>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>

            <!-- Company Listings -->
            <div class="row g-4" id="companies-list">
                <!-- Companies will be loaded here by JS -->
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-5" data-aos="fade-up" data-aos-delay="100">
                <div id="pagination" class="d-flex justify-content-center"></div>
            </nav>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding" data-aos="fade-up" class="bg-light">
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

    <script src="<?= $url_path ?>/assets/js/companies-filter.js"></script>
</body>

</html>