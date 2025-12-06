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
           s.name as state_name
    FROM companies c
    LEFT JOIN cities ci ON c.city_id = ci.id
    LEFT JOIN states s ON c.state_id = s.id
    WHERE c.slug = ?
");
$stmt->execute([$slug]);
$company = $stmt->fetch();

// 3. Validate Existence
if (!$company) {
    header("Location: 404.html");
    exit();
}

// 4. Redirect UPGRADE if Premium (Optional but good UX)
// If a gold company lands here, send them to the nice page
if ($company['subscription_tier'] !== 'free') {
    header("Location: premium-listed.php?slug=" . urlencode($slug));
    exit();
}

// 5. Fetch Basic Relations
$projStmt = $pdo->prepare("SELECT * FROM company_projects WHERE company_id = ?");
$projStmt->execute([$company['id']]);
$projects = $projStmt->fetchAll();

$socials = json_decode($company['social_links'] ?? '{}', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($company['name']) ?> | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>

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
            overflow: hidden;
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
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
    <?php include_once('header.php'); ?>

    <!-- Company Header -->
    <section class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center text-md-start">
                    <div class="company-logo" data-aos="zoom-in">
                        <?php if(!empty($company['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-code"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-7" data-aos="fade-right">
                    <h1 class="fw-bold mb-2"><?= htmlspecialchars($company['name']) ?></h1>
                    <p class="lead mb-3 text-white"><?= htmlspecialchars($company['tagline']) ?></p>
                    <div class="d-flex flex-wrap">
                        <?php 
                            $catStmt = $pdo->prepare("SELECT c.name FROM categories c JOIN company_categories cc ON c.id = cc.category_id WHERE cc.company_id = ?");
                            $catStmt->execute([$company['id']]);
                            $cats = $catStmt->fetchAll(PDO::FETCH_COLUMN);
                            foreach($cats as $catName) {
                                echo '<span class="category-badge">'.htmlspecialchars($catName).'</span>';
                            }
                        ?>
                    </div>
                </div>
                <div class="col-md-3 text-center text-md-end" data-aos="fade-left">
                    <?php if(!empty($company['website_url'])): ?>
                        <a href="<?= htmlspecialchars($company['website_url']) ?>" target="_blank" class="btn btn-outline-primary mb-2">Visit Website</a>
                    <?php endif; ?>
                    <p class="small"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($company['city_name'] ?? '') ?>, Nigeria</p>
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
                    <div class="text-muted">
                        <p><?= nl2br(htmlspecialchars($company['description'])) ?></p>
                    </div>
                </div>
                
                <!-- Recent Projects (If any) -->
                <?php if(count($projects) > 0): ?>
                <div class="info-card mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="fw-bold mb-4">Projects</h3>
                    <div class="row">
                        <?php foreach($projects as $project): ?>
                        <div class="col-md-6 mb-4">
                            <div class="p-3 border rounded h-100">
                                <h5 class="fw-bold"><?= htmlspecialchars($project['title']) ?></h5>
                                <p class="text-muted small"><?= htmlspecialchars($project['description']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Ad Container -->
                <div class="ad-container mb-4" style="position: sticky; top: 80px;">
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
                        <a href="<?= htmlspecialchars($company['website_url']) ?>" target="_blank" class="text-decoration-none"><?= htmlspecialchars($company['website_url']) ?></a>
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
                        <p class="mb-0"><?= htmlspecialchars($company['year_founded'] ?? 'N/A') ?></p>
                    </div>
                </div>

                <!-- Profile Strength Chart (Static or Basic for Free) -->
                <div class="info-card mb-4" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Profile Completeness</h3>
                    <div class="progress" style="height: 20px;">
                        <?php 
                            $completeness = 50; // Base
                            if(!empty($company['logo_url'])) $completeness += 10;
                            if(!empty($company['description'])) $completeness += 10;
                            if(!empty($company['phone'])) $completeness += 10;
                            if(!empty($company['email'])) $completeness += 10;
                            if(count($projects) > 0) $completeness += 10;
                        ?>
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $completeness ?>%" aria-valuenow="<?= $completeness ?>" aria-valuemin="0" aria-valuemax="100"><?= $completeness ?>%</div>
                    </div>
                    <small class="text-muted d-block mt-2">Complete your profile to gain more trust.</small>
                </div>
            
                <!-- Location Map -->
                <div class="info-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h4 class="fw-bold mb-4">Location</h4>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> <?= nl2br(htmlspecialchars($company['address'])) ?></p>
                        <p class="mb-2"><?= htmlspecialchars($company['city_name']) ?></p>
                        <p class="mb-0">Nigeria</p>
                    </div>
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
    <?php include_once('footer.php'); ?>


</body>
</html>