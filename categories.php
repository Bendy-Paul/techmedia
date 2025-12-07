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
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($category['name']); ?></li>
                        </ol>
                    </nav>
                    <h1 class="display-4 fw-bold mb-4" data-aos="fade-up" data-aos-delay="200"><?php echo htmlspecialchars($category['name']); ?> Companies</h1>
                    <p class="lead text-white mb-4" data-aos="fade-up" data-aos-delay="300">Discover Nigeria's top <?php echo htmlspecialchars($category['name']); ?> agencies, freelancers, and tech teams building the digital future of Africa.</p>
                    <div class="d-flex flex-wrap gap-2" data-aos="fade-up" data-aos-delay="400">
                        <?php foreach ($child_cat as $child) { ?>
                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($child['name']); ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block" data-aos="zoom-in" data-aos-delay="500">
                    <i class="<?php echo htmlspecialchars($category['icon_class']); ?> text-white" style="font-size: 10rem; opacity: 0.7;"></i>
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
                <?php
                foreach ($listed_companies as $listed_company) {
                ?>
                <!-- Company 1 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="company-card">
                        <div class="company-body">
                            <div class="company-logo-sm">
                                <img src="<?= htmlspecialchars($url_path ."/".$listed_company['logo_url']) ?>" alt="<?= htmlspecialchars($listed_company['name']) ?>" style="height: 100%; width: auto;">
                            </div>
                            <h5 class="fw-bold mb-2"><?= htmlspecialchars($listed_company['name']); ?></h5>
                            <div class="align-items-center mb-2">
                                <?= $listed_company['is_verified'] ? '<span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>' : '<span class="verified-badge bg-light text-black"><i class="fas fa-times-circle"></i> Unverified</span>' ?><br>
                                <span class="badge bg-light text-dark border ms-2"><?= htmlspecialchars($listed_company['location'] ?? 'Lagos') ?></span>
                            </div>
                            <p class="text-muted small mb-3"><?= htmlspecialchars($listed_company['tagline']) ?></p>
                            <div class="d-flex justify-content-between text-muted small">
                                <span><i class="fas fa-users me-1"></i> <?= htmlspecialchars($listed_company['team_size']) ?> employees</span>
                                <span><i class="fas fa-star me-1 text-warning"></i> 4.5</span>
                            </div>
                            <a href="listed.php?slug=<?= htmlentities($listed_company['slug']) ?>"><button class="btn-view-profile">View Profile</button></a>
                        </div>
                    </div>
                </div>

                <?php
                }
                ?>


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