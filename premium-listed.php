<?php
require 'includes/wertyuytre.php';

// 1. Get Slug & Validate
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: 404.html");
    exit();
}

$slug = $_GET['slug'];

// 2. Fetch Company Data
$stmt = $pdo->prepare("
    SELECT c.*, 
           ci.name as city_name, 
           s.name as state_name, 
           co.name as country_name
    FROM companies c
    LEFT JOIN cities ci ON c.city_id = ci.id
    LEFT JOIN states s ON c.state_id = s.id
    LEFT JOIN countries co ON c.country_id = co.id
    WHERE c.slug = ?
");
$stmt->execute([$slug]);
$company = $stmt->fetch();

// 3. Handling Redirects
if (!$company) {
    // Company not found
    header("Location: $url_path/404.html");
    exit();
}

if ($company['subscription_tier'] === 'free') {
    // Not a premium company, redirect to standard listing
    header("Location: $url_path/listed.php/" . urlencode($slug));
    exit();
}

// 4. Fetch Related Data

// Projects
$projStmt = $pdo->prepare("SELECT * FROM company_projects WHERE company_id = ?");
$projStmt->execute([$company['id']]);
$projects = $projStmt->fetchAll();

// Gallery
$galStmt = $pdo->prepare("SELECT * FROM company_galleries WHERE company_id = ?");
$galStmt->execute([$company['id']]);
$gallery = $galStmt->fetchAll();

// Articles (News related to this company)
$newsStmt = $pdo->prepare("
    SELECT a.title, a.slug, a.published_at, a.content 
    FROM articles a
    JOIN article_companies ac ON a.id = ac.article_id
    WHERE ac.company_id = ? AND a.status = 'published'
    ORDER BY a.published_at DESC LIMIT 3
");
$newsStmt->execute([$company['id']]);
$company_news = $newsStmt->fetchAll();

// Parse JSON Data
$socials = json_decode($company['social_links'] ?? '{}', true);
$stats = json_decode($company['profile_stats'] ?? '{}', true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($company['name']) ?> | Premium Profile</title>
    
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


                /* Article Hero */
        .profile-header {
        position: relative;
            width: 100%;
            height: 28rem;
            display: flex;
            align-items: center; /* Vertically center text */
            justify-content: center; /* Horizontally center text */
             overflow: hidden; /*Crucial: hides the messy edges caused by the blur */
            color: white;
        }

        /* The Blurred Image Layer */
        .profile-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Replace with your image URL */
            background-image: url('<?= htmlspecialchars($url_path."/".$company['logo_url']) ?>');
            background-size: cover;
            background-position: center;
            
            /* 1. The Blur Effect */
            filter: blur(5px); 
            
            /* 2. Scale Fix: When you blur, edges get white/faded. 
               Scaling up slightly pushes those bad edges off-screen. */
            transform: scale(1.1); 
            
            z-index: -2; /* Places it at the very back */
        }

        /* The Dark Overlay Layer */
        .profile-header::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            
            /* Black overlay with 40% opacity */
            background-color: rgba(52, 74, 98, 0.4); 
            
            z-index: -1; /* Places it above the image but behind the text */
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
            overflow: hidden;
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .verified-badge {
            position: absolute;
            bottom: -10px;
            right: 0px;
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
            color: white;
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
            color: white;
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

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
    <!-- Navigation -->
    <?php include_once('header.php'); ?>

    <!-- Company Header -->
    <section class="profile-header">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-md-2 text-center text-md-start">
                    <div class="company-logo" data-aos="zoom-in">
                        <?php if(!empty($company['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($url_path."/".$company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-building"></i>
                        <?php endif; ?>
                        
                        <?php if($company['is_verified']): ?>
                        <div class="verified-badge">
                            <i class="fas fa-check"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-7" data-aos="fade-right">
                    <h1 class="fw-bold mb-2"><?= htmlspecialchars($company['name']) ?></h1>
                    <p class="lead mb-3"><?= htmlspecialchars($company['tagline']) ?></p>
                    <div class="d-flex flex-wrap mb-3">
                        <!-- Display Categories dynamically -->
                        <?php 
                            $catStmt = $pdo->prepare("SELECT c.name FROM categories c JOIN company_categories cc ON c.id = cc.category_id WHERE cc.company_id = ?");
                            $catStmt->execute([$company['id']]);
                            $cats = $catStmt->fetchAll(PDO::FETCH_COLUMN);
                            foreach($cats as $catName) {
                                echo '<span class="category-badge">'.htmlspecialchars($catName).'</span>';
                            }
                        ?>
                    </div>
                    <div class="d-flex gap-3">
                        <?php if(!empty($socials['twitter'])): ?>
                            <a href="<?= htmlspecialchars($socials['twitter']) ?>" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if(!empty($socials['linkedin'])): ?>
                            <a href="<?= htmlspecialchars($socials['linkedin']) ?>" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                        <?php if(!empty($socials['facebook'])): ?>
                            <a href="<?= htmlspecialchars($socials['facebook']) ?>" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                         <?php if(!empty($socials['instagram'])): ?>
                            <a href="<?= htmlspecialchars($socials['instagram']) ?>" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3 text-center text-md-end" data-aos="fade-left">
                    <?php if(!empty($company['website_url'])): ?>
                        <a href="<?= htmlspecialchars($company['website_url']) ?>" target="_blank" class="btn btn-light mb-2">Visit Website</a>
                    <?php endif; ?>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($company['city_name'] ?? '') ?>, <?= htmlspecialchars($company['country_name'] ?? 'Nigeria') ?></p>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Company Overview -->
                <div class="info-card mb-4" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Company Overview</h3>
                    <div class="article-content text-muted">
                        <p><?= nl2br(htmlspecialchars($company['description'])) ?></p>
                    </div>
                </div>

                <!-- Tech Stack & Tools -->
                <?php
                    // Fetch stack_ids as array
                    $stack_ids = json_decode($company['stack_ids'] ?? '[]', true);
                    if (!is_array($stack_ids)) $stack_ids = [];
                    $stacks = [];
                    if (count($stack_ids) > 0) {
                        $in = str_repeat('?,', count($stack_ids) - 1) . '?';
                        $sql = "SELECT * FROM stacks WHERE id IN ($in) ORDER BY FIELD(id, " . implode(",", array_map('intval', $stack_ids)) . ")";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($stack_ids);
                        $stacks = $stmt->fetchAll();
                    }
                ?>


                <!-- Competitive Analytics (Charts) -->
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="fw-bold mb-4">Performance Analytics</h3>
                    <?php
                        // Use metrics from profile_stats JSON
                        $response_rate = isset($stats['response_rate']) ? (float)$stats['response_rate'] : null;
                        $completeness = isset($stats['completeness']) ? (float)$stats['completeness'] : null;
                        $views = isset($company['views']) ? (int)$company['views'] : null;
                        $projects_count = isset($projects) ? count($projects) : 0;
                    ?>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-card text-center">
                                <h5 class="mb-2">Response Rate</h5>
                                <div style="font-size:2.2rem;font-weight:700; color:#2563eb;">
                                    <?= $response_rate !== null ? $response_rate . '%' : 'N/A' ?>
                                </div>
                                <div class="small text-muted">How quickly this company responds to inquiries.</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card text-center">
                                <h5 class="mb-2">Profile Completeness</h5>
                                <div style="font-size:2.2rem;font-weight:700; color:#10b981;">
                                    <?= $completeness !== null ? $completeness . '%' : 'N/A' ?>
                                </div>
                                <div class="small text-muted">How complete the company profile is.</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card text-center">
                                <h5 class="mb-2">Profile Views</h5>
                                <div style="font-size:2.2rem;font-weight:700; color:#f59e0b;">
                                    <?= $views !== null ? number_format($views) : 'N/A' ?>
                                </div>
                                <div class="small text-muted">Total times this profile has been viewed.</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card text-center">
                                <h5 class="mb-2">Projects Listed</h5>
                                <div style="font-size:2.2rem;font-weight:700; color:#3b82f6;">
                                    <?= $projects_count ?>
                                </div>
                                <div class="small text-muted">Number of projects in portfolio.</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Projects -->
                <?php if(count($projects) > 0): ?>
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="fw-bold mb-4">Recent Projects</h3>
                    <div class="row">
                        <?php foreach($projects as $project): ?>
                        <div class="col-md-6 mb-4">
                            <div class="project-card p-3 h-100">
                                <h5 class="fw-bold"><?= htmlspecialchars($project['title']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($project['description']) ?></p>
                                <?php if(!empty($project['image_url'])): ?>
                                    <img src="<?= htmlspecialchars($project['image_url']) ?>" class="img-fluid rounded mt-2" alt="Project Image">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Company Articles -->
                <?php if(count($company_news) > 0): ?>
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="fw-bold mb-4">Company News & Insights</h3>
                    <?php foreach($company_news as $news): ?>
                    <div class="article-card mb-4">
                        <h5 class="fw-bold"><a href="article.php?slug=<?= $news['slug'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($news['title']) ?></a></h5>
                        <p class="text-muted"><?= htmlspecialchars(substr(strip_tags($news['content']), 0, 150)) ?>...</p>
                        <span class="text-muted small">Published: <?= date('F j, Y', strtotime($news['published_at'])) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Details -->
                <div class="info-card mb-4" data-aos="fade-left">
                    <h4 class="fw-bold mb-4">Contact Details</h4>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Website</h6>
                        <a href="<?= htmlspecialchars($company['website_url']) ?>" target="_blank" class="text-decoration-none text-truncate d-block"><?= htmlspecialchars($company['website_url']) ?></a>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Phone</h6>
                        <p class="mb-0"><?= htmlspecialchars($company['phone']) ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Email</h6>
                        <p class="mb-0"><?= htmlspecialchars($company['email']) ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Team Size</h6>
                        <p class="mb-0"><?= htmlspecialchars($company['team_size'] ?? 'N/A') ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Year Started</h6>
                        <?php
                            $thisYear = (int)date('Y');
                            $companyYear = (int)($company['year_founded'] ?? 0);
                            $companyAge = $companyYear ? ($thisYear - $companyYear) : null;
                            $peerAges = [];
                            if (isset($catIdsArr) && count($catIdsArr) > 0) {
                                $in = str_repeat('?,', count($catIdsArr)-1) . '?';
                                $sql = "SELECT DISTINCT c.year_founded FROM companies c JOIN company_categories cc ON c.id = cc.company_id WHERE cc.category_id IN ($in) AND c.id != ? AND c.year_founded IS NOT NULL AND c.year_founded > 1900";
                                $params = array_merge($catIdsArr, [$company['id']]);
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute($params);
                                foreach($stmt as $row) {
                                    $peerYear = (int)$row['year_founded'];
                                    if($peerYear) $peerAges[] = $thisYear - $peerYear;
                                }
                            }
                            $avgPeerAge = count($peerAges) ? round(array_sum($peerAges)/count($peerAges),1) : null;
                        ?>
                        <p class="mb-0">
                            <?php if($companyYear): ?>
                                <?= htmlspecialchars($companyYear) ?> (<?= $companyAge ?> yrs old)
                                <?php if($avgPeerAge !== null): ?>
                                    <br><span class="small text-muted">Category avg: <?= $avgPeerAge ?> yrs</span>
                                <?php endif; ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Starting Cost Per Project</h6>
                        <p class="mb-0">
                            <?= !empty($company['starting_cost']) ? $company['currency'] . ' ' . number_format($company['starting_cost']) : 'Contact for Quote' ?>
                        </p>
                    </div>
                </div>

                <?php if(count($stacks) > 0): ?>
                    <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="120">
                        <h3 class="fw-bold mb-4">Tech Stack & Tools</h3>
                        <div class="row g-3 align-items-center">
                            <?php foreach($stacks as $stack): ?>
                            <div class="col-lg-4 mb-2">
                                <div class="text-center gap-2">
                                    <span style="font-size:1.7rem;"><i class="<?= htmlspecialchars($stack['icon_class'] ?? 'fas fa-code') ?> text-primary"></i></span><br>
                                    <span class="fw-semibold small"><?= htmlspecialchars($stack['name']) ?></span>
                                    <?php if(!empty($stack['category'])): ?>
                                        <!-- <span class="badge bg-light text-dark border ms-1 small"><?= htmlspecialchars($stack['category']) ?></span> -->
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Profile Strength -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h4 class="fw-bold mb-4">Profile Strength</h4>
                    <?php
                        // Calculate profile strength (example: based on completeness of key fields)
                        $fields = ['name','tagline','description','email','phone','website_url','year_founded','team_size','logo_url', 'address'];
                        $filled = 0;
                        foreach($fields as $f) if(!empty($company[$f])) $filled++;
                        $profile_strength = round(($filled/count($fields))*100);

                        // Get average profile strength for companies in the same categories
                        $catIds = $pdo->prepare("SELECT category_id FROM company_categories WHERE company_id = ?");
                        $catIds->execute([$company['id']]);
                        $catIdsArr = $catIds->fetchAll(PDO::FETCH_COLUMN);
                        $avgStrength = null;
                        if (count($catIdsArr) > 0) {
                            $in = str_repeat('?,', count($catIdsArr)-1) . '?';
                            $sql = "SELECT c.id, c.name, c.logo_url, c.tagline, c.description, c.email, c.phone, c.website_url, c.year_founded, c.team_size, c.starting_cost, c.address FROM companies c JOIN company_categories cc ON c.id = cc.company_id WHERE cc.category_id IN ($in) AND c.id != ?";
                            $params = array_merge($catIdsArr, [$company['id']]);
                            $peers = $pdo->prepare($sql);
                            $peers->execute($params);
                            $peerStrengths = [];
                            foreach($peers as $peer) {
                                $peerFilled = 0;
                                foreach($fields as $f) if(!empty($peer[$f])) $peerFilled++;
                                $peerStrengths[] = round(($peerFilled/count($fields))*100);
                            }
                            $avgStrength = count($peerStrengths) ? round(array_sum($peerStrengths)/count($peerStrengths)) : null;
                        }
                    ?>
                    <div class="mb-2">
                        <div class="progress" style="height: 28px; background: #e5e7eb;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $profile_strength ?>%; font-weight:600; font-size:1.1rem;" aria-valuenow="<?= $profile_strength ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= $profile_strength ?>%
                            </div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <?php if($avgStrength !== null): ?>
                            <span>Your profile is <b><?= $profile_strength ?>%</b> complete. Category average: <b><?= $avgStrength ?>%</b>.</span>
                        <?php else: ?>
                            <span>Your profile is <b><?= $profile_strength ?>%</b> complete.</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Gallery -->
                <?php if(count($gallery) > 0): ?>
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="200">
                    <h4 class="fw-bold mb-4">Gallery</h4>
                    <div class="row g-2">
                        <?php foreach($gallery as $img): ?>
                        <div class="col-6">
                            <div class="gallery-item">
                                <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Gallery" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Location Map (Placeholder logic) -->
                <div class="info-card" data-aos="fade-left" data-aos-delay="300">
                    <h4 class="fw-bold mb-4">Office Locations</h4>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <p class="fw-bold mb-2">Headquarters</p>
                        <p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> <?= nl2br(htmlspecialchars($company['address'])) ?></p>
                        <p class="mb-0"><?= htmlspecialchars($company['city_name']) ?></p>
                    </div>
                    <?php
                        // Fetch branch locations (excluding HQ)
                        $branchStmt = $pdo->prepare("SELECT l.*, c.name as city_name FROM company_locations l LEFT JOIN cities c ON l.city_id = c.id WHERE l.company_id = ?");
                        $branchStmt->execute([$company['id']]);
                        $branches = $branchStmt->fetchAll();
                    ?>
                    <?php if(count($branches) > 0): ?>
                        <div class="mb-3">
                            <p class="fw-bold mb-2">Branches</p>
                            <?php foreach($branches as $branch): ?>
                                <div class="border rounded p-2 mb-2 bg-white">
                                    <span class="fw-semibold"><i class="fas fa-map-marker-alt text-secondary me-1"></i> <?= htmlspecialchars($branch['city_name']) ?></span><br>
                                    <span class="small text-muted"><?= nl2br(htmlspecialchars($branch['address'])) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="fas fa-map me-2"></i> View Offices on Map
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // No charts needed for metrics cards
    </script>
</body>
</html>