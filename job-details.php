<?php
require_once 'includes/wertyuytre.php';

if (!isset($_GET['slug'])) {
    header("Location: jobs.php");
    exit();
}

$slug = $_GET['slug'];

// Fetch Job + Company Info
$stmt = $pdo->prepare("
    SELECT j.*, 
           c.name as comp_name, c.logo_url as comp_logo, c.description as comp_desc, c.website_url, c.slug as comp_slug,
           c.team_size
    FROM jobs j
    LEFT JOIN companies c ON j.company_id = c.id
    WHERE j.slug = ?
");
$stmt->execute([$slug]);
$job = $stmt->fetch();

if (!$job) {
    header("Location: 404.html");
    exit();
}

// Fallback for company data if job was posted manually without a company profile
$company_name = $job['comp_name'] ?? $job['company_name'];
$company_logo = $job['comp_logo'] ?? null;
$company_desc = $job['comp_desc'] ?? "Company information not available.";

// Fetch Similar Jobs
$simStmt = $pdo->prepare("SELECT * FROM jobs WHERE id != ? AND (job_type = ? OR experience_level = ?) ORDER BY created_at DESC LIMIT 3");
$simStmt->execute([$job['id'], $job['job_type'], $job['experience_level']]);
$similar_jobs = $simStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> at <?= htmlspecialchars($company_name) ?> | Tech Media Directory</title>
    <?php include_once('links.php') ?>
    <style>
        /* Keep existing styles */
        .job-details-hero {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.9) 0%, rgba(59, 130, 246, 0.8) 100%);
            padding: 100px 0 60px;
            color: #fff;
        }
        .company-logo-large {
            width: 100px; height: 100px;
            border-radius: 20px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: var(--primary-accent);
        }
        .company-logo-large img { width: 80%; height: 80%; object-fit: contain; }
        
        .job-tag {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-right: 8px;
        }
        
        .job-description-card, .job-sidebar-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 2px solid #eff6ff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>

    <?php include_once('header.php'); ?>

    <!-- Job Details Hero -->
    <section class="job-details-hero">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="company-logo-large">
                    <?php if($company_logo): ?>
                        <img src="<?= htmlspecialchars($company_logo) ?>" alt="Logo">
                    <?php else: ?>
                        <i class="fas fa-building"></i>
                    <?php endif; ?>
                </div>
                <div class="text-center text-md-start flex-grow-1">
                    <?php if($job['is_featured']): ?>
                        <span class="badge bg-warning text-dark mb-2">Featured Job</span>
                    <?php endif; ?>
                    <h1 class="fw-bold mb-2"><?= htmlspecialchars($job['title']) ?></h1>
                    <h4 class="h5 opacity-75 mb-3"><?= htmlspecialchars($company_name) ?></h4>
                    
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 opacity-90">
                        <span><i class="fas fa-map-marker-alt me-1"></i> <?= $job['is_remote'] ? 'Remote' : htmlspecialchars($job['location']) ?></span>
                        <span><i class="fas fa-briefcase me-1"></i> <?= $job['job_type'] ?></span>
                        <span><i class="fas fa-money-bill-wave me-1"></i> <?= htmlspecialchars($job['salary_range'] ?? 'Negotiable') ?></span>
                        <span><i class="fas fa-clock me-1"></i> Posted <?= date('M d', strtotime($job['created_at'])) ?></span>
                    </div>
                    
                    <div class="mt-3">
                        <?php 
                        $skills = explode(',', $job['skills']);
                        foreach($skills as $skill): 
                            if(trim($skill) == '') continue;
                        ?>
                            <span class="job-tag"><?= htmlspecialchars(trim($skill)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Job Details Content -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="job-description-card">
                        <h3 class="section-title">Job Description</h3>
                        <div class="text-muted" style="white-space: pre-line; line-height: 1.8;">
                            <?= htmlspecialchars($job['description']) ?>
                        </div>
                    </div>
                    
                    <!-- Similar Jobs -->
                    <?php if(count($similar_jobs) > 0): ?>
                    <h4 class="fw-bold mb-4">Similar Jobs</h4>
                    <div class="row">
                        <?php foreach($similar_jobs as $sim): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm p-3">
                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($sim['title']) ?></h6>
                                <p class="small text-muted mb-2"><?= htmlspecialchars($sim['company_name']) ?></p>
                                <div class="d-flex justify-content-between small text-muted mt-auto">
                                    <span><?= $sim['location'] ?></span>
                                    <span class="text-primary fw-bold"><?= $sim['salary_range'] ?></span>
                                </div>
                                <a href="<?= htmlspecialchars($url_path) ?>/job-details/<?= $sim['slug'] ?>" class="stretched-link"></a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="job-sidebar-card">
                        <a href="<?= htmlspecialchars($job['apply_link'] ?? '#') ?>" target="_blank" class="btn btn-primary w-100 py-3 fw-bold mb-3">Apply Now</a>
                        <button class="btn btn-outline-primary w-100 py-3 fw-bold" onclick="alert('Saved to favorites!')"><i class="far fa-bookmark me-2"></i> Save Job</button>
                    </div>
                    
                    <div class="job-sidebar-card">
                        <h5 class="fw-bold mb-4">About Company</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 8px; display:flex; align-items:center; justify-content:center; color: var(--primary-accent); margin-right: 15px;">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($company_name) ?></h6>
                                <small class="text-muted"><?= $job['team_size'] ?? 'Tech Company' ?></small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">
                            <?= htmlspecialchars(substr($company_desc, 0, 150)) ?>...
                        </p>
                        <?php if(isset($job['comp_slug'])): ?>
                            <a href="<?= htmlspecialchars($url_path) ?>/listed/<?= $job['comp_slug'] ?>" class="btn btn-link p-0 text-decoration-none small fw-bold">View Company Profile</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="job-sidebar-card">
                        <h5 class="fw-bold mb-4">Job Overview</h5>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Posted Date</span>
                                <span class="fw-bold"><?= date('M d, Y', strtotime($job['created_at'])) ?></span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Location</span>
                                <span class="fw-bold"><?= $job['is_remote'] ? 'Remote' : htmlspecialchars($job['location']) ?></span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Job Type</span>
                                <span class="fw-bold"><?= $job['job_type'] ?></span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Experience</span>
                                <span class="fw-bold"><?= $job['experience_level'] ?></span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">Salary</span>
                                <span class="fw-bold text-success"><?= htmlspecialchars($job['salary_range'] ?? 'Negotiable') ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('footer.php'); ?>

</body>
</html>