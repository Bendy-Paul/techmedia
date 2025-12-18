<?php
require 'includes/wertyuytre.php';

// Filter Logic
$search = $_GET['s'] ?? '';
$location = $_GET['loc'] ?? '';
$type = $_GET['type'] ?? '';
$level = $_GET['level'] ?? '';
$salary = $_GET['salary'] ?? ''; // Simple string match for now

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build Query
$sql = "SELECT j.*, c.logo_url as company_logo 
        FROM jobs j 
        LEFT JOIN companies c ON j.company_id = c.id 
        WHERE j.expires_at >= NOW()";

$params = [];

if (!empty($search)) {
    $sql .= " AND (j.title LIKE ? OR j.company_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($location)) {
    if (strtolower($location) == 'remote') {
        $sql .= " AND j.is_remote = 1";
    } else {
        $sql .= " AND j.location LIKE ?";
        $params[] = "%$location%";
    }
}
if (!empty($type)) {
    $sql .= " AND j.job_type = ?";
    $params[] = $type;
}
if (!empty($level)) {
    $sql .= " AND j.experience_level = ?";
    $params[] = $level;
}

// Order by Featured first, then Newest
$sql .= " ORDER BY j.is_featured DESC, j.created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// Count for Pagination
$countSql = "SELECT COUNT(*) FROM jobs j WHERE j.expires_at >= NOW()";
// Re-apply filters for count (omitted for brevity, use same logic in production)
$total_jobs = $pdo->query("SELECT COUNT(*) FROM jobs WHERE expires_at >= NOW()")->fetchColumn(); 
$total_pages = ceil($total_jobs / $limit);

// Stats
$new_jobs_count = $pdo->query("SELECT COUNT(*) FROM jobs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings | Tech Media Directory</title>
    <?php include_once('links.php') ?>
    <style>
        /* Job Listings Specific Styles */
        .jobs-hero {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.8) 0%, rgba(59, 130, 246, 0.7) 100%), url('assets/images/office-bg.jpg') center/cover;
            padding: 120px 0 60px;
            position: relative;
            color: #fff;
        }
        
        .job-card {
            background: var(--bg-card);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(37, 99, 235, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
            border-color: var(--primary-accent);
        }
        
        .job-card-header {
            padding: 25px 25px 15px;
            border-bottom: 1px solid rgba(37, 99, 235, 0.1);
        }
        
        .job-company-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-accent);
            overflow: hidden;
        }
        
        .job-company-logo img { width: 100%; height: 100%; object-fit: contain; }
        
        .job-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .featured-job { border: 2px solid var(--primary-accent); }
        .featured-label { background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; }
        .urgent-job { border-left: 4px solid #ef4444; }
        .urgent-label { background: #ef4444; color: white; }
        .remote-badge { background: rgba(34, 197, 94, 0.1); color: #16a34a; }

        .job-card-body { padding: 20px 25px; flex-grow: 1; }
        
        .skill-tag {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 5px;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .job-card-footer {
            padding: 15px 25px 25px;
            border-top: 1px solid rgba(37, 99, 235, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .job-sidebar-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <?php include_once('event-ticker.php'); ?>
    <?php include_once('header.php'); ?>

    <!-- Hero Section -->
    <section class="jobs-hero">
        <div class="container text-center position-relative z-2">
            <h1 class="display-4 fw-bold mb-3">Find Your Dream Tech Job</h1>
            <p class="lead text-white-50 mb-4">Discover opportunities with Nigeria's top tech companies</p>
            
            <!-- Job Search Bar -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form action="" method="GET" class="card p-3 border-0 shadow-lg">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="s" class="form-control form-control-lg border-0 bg-light" placeholder="Job title, keywords..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="loc" class="form-control form-control-lg border-0 bg-light" placeholder="City or 'Remote'" value="<?= htmlspecialchars($location) ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select form-select-lg border-0 bg-light">
                                    <option value="">Job Type</option>
                                    <option value="Full-time" <?= $type=='Full-time'?'selected':'' ?>>Full-time</option>
                                    <option value="Part-time" <?= $type=='Part-time'?'selected':'' ?>>Part-time</option>
                                    <option value="Contract" <?= $type=='Contract'?'selected':'' ?>>Contract</option>
                                    <option value="Internship" <?= $type=='Internship'?'selected':'' ?>>Internship</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Banner -->
    <section class="py-4 bg-light">
        <div class="container text-center">
            <h5 class="mb-3 text-primary">🔥 <?= $new_jobs_count ?> New Jobs Posted This Week</h5>
        </div>
    </section>

    <!-- Job Listings -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 mb-5">
                    <div class="job-sidebar-card">
                        <h5 class="fw-bold mb-4">Filter Jobs</h5>
                        <form action="" method="GET">
                            <!-- Preserve search params -->
                            <?php if($search): ?><input type="hidden" name="s" value="<?= $search ?>"><?php endif; ?>
                            
                            <div class="mb-4">
                                <h6 class="fw-bold small text-muted mb-2">Job Type</h6>
                                <?php $types = ['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance']; ?>
                                <?php foreach($types as $t): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="<?= $t ?>" id="t_<?= $t ?>" <?= $type == $t ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="t_<?= $t ?>"><?= $t ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="fw-bold small text-muted mb-2">Experience Level</h6>
                                <?php $levels = ['Entry Level', 'Mid Level', 'Senior', 'Executive']; ?>
                                <?php foreach($levels as $l): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="level" value="<?= $l ?>" id="l_<?= $l ?>" <?= $level == $l ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="l_<?= $l ?>"><?= $l ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="jobs.php" class="btn btn-link text-muted btn-sm mt-2">Reset</a>
                            </div>
                        </form>
                    </div>

                    <!-- CTA -->
                    <div class="job-sidebar-card text-center">
                        <h5 class="fw-bold mb-3">Post a Job</h5>
                        <p class="text-muted small mb-3">Reach thousands of qualified tech professionals.</p>
                        <a href="get-listed.php" class="btn btn-outline-primary w-100">Post Now</a>
                    </div>
                </div>
                
                <!-- Listings -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0"><?= count($jobs) ?> Jobs Found</h4>
                    </div>
                    
                    <div class="row g-4">
                        <?php if(count($jobs) > 0): ?>
                            <?php foreach($jobs as $job): ?>
                            <div class="col-md-6">
                                <div class="job-card <?= $job['is_featured'] ? 'featured-job' : '' ?> <?= $job['is_urgent'] ? 'urgent-job' : '' ?>">
                                    <div class="job-card-header">
                                        <div class="d-flex align-items-start">
                                            <div class="job-company-logo">
                                                <?php if($job['company_logo']): ?>
                                                    <img src="<?= htmlspecialchars($job['company_logo']) ?>" alt="Logo">
                                                <?php else: ?>
                                                    <i class="fas fa-building"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <?php if($job['is_featured']): ?>
                                                    <span class="job-badge featured-label">Featured</span>
                                                <?php elseif($job['is_urgent']): ?>
                                                    <span class="job-badge urgent-label">Urgent</span>
                                                <?php elseif($job['is_remote']): ?>
                                                    <span class="job-badge remote-badge">Remote</span>
                                                <?php endif; ?>
                                                
                                                <h3 class="h5 fw-bold mb-1 text-truncate"><?= htmlspecialchars($job['title']) ?></h3>
                                                <p class="text-muted small mb-0"><?= htmlspecialchars($job['company_name']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="job-card-body">
                                        <div class="d-flex flex-wrap gap-3 mb-3 small text-muted">
                                            <span><i class="fas fa-map-marker-alt text-primary me-1"></i> <?= $job['is_remote'] ? 'Remote' : htmlspecialchars($job['location']) ?></span>
                                            <span><i class="fas fa-briefcase text-primary me-1"></i> <?= $job['job_type'] ?></span>
                                            <span><i class="fas fa-money-bill-wave text-primary me-1"></i> <?= htmlspecialchars($job['salary_range'] ?? 'Negotiable') ?></span>
                                        </div>
                                        
                                        <p class="text-muted small mb-3 line-clamp-3">
                                            <?= htmlspecialchars(substr(strip_tags($job['description']), 0, 100)) ?>...
                                        </p>
                                        
                                        <div class="mb-0">
                                            <?php 
                                            $skills = explode(',', $job['skills']);
                                            foreach(array_slice($skills, 0, 3) as $skill): 
                                            ?>
                                                <span class="skill-tag"><?= htmlspecialchars(trim($skill)) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="job-card-footer">
                                        <span class="small text-muted"><?= date('M d', strtotime($job['created_at'])) ?></span>
                                        <a href="<?= $url_path ?>/job-details/<?= htmlspecialchars($job['slug']) ?>" class="btn btn-sm btn-primary">Apply Now</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <img src="assets/images/empty.svg" style="width:100px; opacity:0.5;" class="mb-3">
                                <h3>No jobs found</h3>
                                <p class="text-muted">Try adjusting your search filters.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if($total_pages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination">
                            <?php for($i=1; $i<=$total_pages; $i++): ?>
                            <li class="page-item <?= $page==$i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('footer.php'); ?>
</body>
</html>