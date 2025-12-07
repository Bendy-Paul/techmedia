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
    header("Location: 404.html");
    exit();
}

if ($company['subscription_tier'] === 'free') {
    // Not a premium company, redirect to standard listing
    header("Location: listed.php?slug=" . urlencode($slug));
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
                
                <!-- Competitive Analytics (Charts) -->
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
                        <p class="mb-0"><?= htmlspecialchars($company['year_founded'] ?? 'N/A') ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Starting Cost Per Project</h6>
                        <p class="mb-0">
                            <?= !empty($company['starting_cost']) ? $company['currency'] . ' ' . number_format($company['starting_cost']) : 'Contact for Quote' ?>
                        </p>
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
                    <h4 class="fw-bold mb-4">Office Location</h4>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <p class="fw-bold mb-2">Headquarters</p>
                        <p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> <?= nl2br(htmlspecialchars($company['address'])) ?></p>
                        <p class="mb-0"><?= htmlspecialchars($company['city_name']) ?></p>
                    </div>
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

        // Prepare Data for Charts (Using Profile Stats from DB)
        const statsData = {
            completeness: <?= $stats['completeness'] ?? 0 ?>,
            response_rate: <?= $stats['response_rate'] ?? 0 ?>
        };

        // Profile Strength Chart
        const profileCtx = document.getElementById('profileStrengthChart').getContext('2d');
        const profileChart = new Chart(profileCtx, {
            type: 'radar',
            data: {
                labels: ['Profile Completeness', 'Response Rate', 'Client Reviews', 'Project Portfolio', 'Industry Experience', 'Social Proof'],
                datasets: [{
                    label: 'Profile Strength',
                    // Mapping DB stats + some calculated/random values for demo visualization
                    data: [statsData.completeness, statsData.response_rate, 80, 85, 75, 90], 
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: '#2563eb',
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Client Satisfaction Chart (Dummy data for visual - needs review table data to be dynamic)
        const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
        const satisfactionChart = new Chart(satisfactionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Very Satisfied', 'Satisfied', 'Neutral', 'Unsatisfied'],
                datasets: [{
                    data: [75, 20, 4, 1],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Project Delivery Time Chart (Dummy visual)
        const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
        const deliveryChart = new Chart(deliveryCtx, {
            type: 'bar',
            data: {
                labels: ['This Company', 'Industry Average'],
                datasets: [{
                    label: 'Days to Delivery',
                    data: [45, 68],
                    backgroundColor: ['#2563eb', '#94a3b8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Days' }
                    }
                }
            }
        });
    </script>
</body>
</html>