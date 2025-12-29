<?php
// states.php - List companies by state and filter by categories
require_once 'includes/wertyuytre.php';

// Get all states ordered by company count (top 20)
$states = $pdo->query("SELECT s.*, COUNT(c.id) as company_count FROM states s LEFT JOIN companies c ON s.id = c.state_id GROUP BY s.id ORDER BY company_count DESC, s.name ASC LIMIT 20")->fetchAll();

// Get all categories for filtering
$categories = $pdo->query("SELECT * FROM categories WHERE type='company' ORDER BY name ASC")->fetchAll();

// Get selected state
$state_id = isset($_GET['state_id']) ? intval($_GET['state_id']) : null;
$selected_categories = isset($_GET['categories']) ? (array)$_GET['categories'] : [];

$companies = [];
$state = null;
if ($state_id) {
    $state = $pdo->prepare("SELECT * FROM states WHERE id = ?");
    $state->execute([$state_id]);
    $state = $state->fetch();
    if ($state) {
        // Build category filter
        $cat_sql = '';
        $params = [$state_id];
        if (!empty($selected_categories)) {
            $in = str_repeat('?,', count($selected_categories) - 1) . '?';
            $cat_sql = " AND cc.category_id IN ($in)";
            $params = array_merge($params, $selected_categories);
        }
        $sql = "SELECT c.* FROM companies c LEFT JOIN company_categories cc ON c.id = cc.company_id WHERE c.state_id = ? $cat_sql GROUP BY c.id ORDER BY c.name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $companies = $stmt->fetchAll();
    }
}

// For tag display helper
function getCategoryName($id, $categories) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $id) return $cat['name'];
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Companies by State | TechMedia</title>
    
    <?php include_once('links.php'); ?>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --hover-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
        }

        /* Header Styling */
        .page-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        /* State Grid */
        .state-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .state-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .state-card:hover, .state-card.active {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .state-card .count-badge {
            background: #f1f5f9;
            color: var(--text-muted);
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 99px;
            font-weight: 600;
        }
        
        .state-card:hover .count-badge {
            background: white;
            color: var(--primary);
        }

        /* Filter Pills */
        .filter-container {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .filter-check-input {
            display: none; /* Hide default checkbox */
        }

        .filter-pill {
            display: inline-block;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 0.5rem;
            margin-right: 0.25rem;
            user-select: none;
        }

        .filter-pill:hover {
            background: #e2e8f0;
        }

        /* Logic: If checkbox checked, style adjacent label */
        .filter-check-input:checked + .filter-pill {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        /* Company Cards */
        .company-card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            background: white;
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .company-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
            border-color: #bfdbfe;
        }

        .company-logo-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .company-logo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .company-logo-placeholder {
            font-size: 1.5rem;
            color: #cbd5e1;
        }

        .btn-view-profile {
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            font-weight: 600;
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-view-profile:hover {
            background: var(--primary);
            color: white;
        }

        .active-tags-wrapper .tag {
            background: white;
            border: 1px solid #bfdbfe;
            color: var(--primary);
            font-size: 0.85rem;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .active-tags-wrapper .tag a {
            color: #ef4444;
            margin-left: 6px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="fw-bold mb-2 text-dark">Explore Companies</h1>
        <p class="text-muted mb-0">Discover top tech companies across different regions.</p>
    </div>
</div>

<div class="container pb-5">
    
    <div class="mb-5">
        <h5 class="text-uppercase text-muted fw-bold small mb-3 letter-spacing-1">Select a State</h5>
        <div class="state-grid">
            <?php foreach ($states as $s): ?>
                <a href="?state_id=<?= $s['id'] ?>" class="state-card <?= $state_id == $s['id'] ? 'active' : '' ?>">
                    <span><?= htmlspecialchars($s['name']) ?></span>
                    <span class="count-badge"><?= $s['company_count'] ?></span>
                </a>
            <?php endforeach; ?>
            <a href="all-states.php" class="state-card justify-content-center bg-light text-muted">
                View All <i class="fas fa-arrow-right ms-2 small"></i>
            </a>
        </div>
    </div>

    <?php if ($state): ?>
        <hr class="my-5" style="opacity: 0.05;">

        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="sticky-top" style="top: 2rem; z-index: 1;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0"><?= htmlspecialchars($state['name']) ?></h4>
                        <?php if(!empty($selected_categories)): ?>
                            <a href="?state_id=<?= $state_id ?>" class="small text-danger text-decoration-none">Clear Filters</a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="get" id="filterForm" class="filter-container shadow-sm">
                        <input type="hidden" name="state_id" value="<?= $state_id ?>">
                        <h6 class="fw-bold mb-3 small text-uppercase text-muted">Filter by Category</h6>
                        
                        <div class="d-flex flex-wrap">
                            <?php foreach ($categories as $cat): ?>
                                <div>
                                    <input type="checkbox" 
                                           class="filter-check-input" 
                                           id="cat_<?= $cat['id'] ?>" 
                                           name="categories[]" 
                                           value="<?= $cat['id'] ?>" 
                                           <?= in_array($cat['id'], $selected_categories) ? 'checked' : '' ?> 
                                           onchange="this.form.submit()">
                                    <label class="filter-pill" for="cat_<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-9">
                <?php if (!empty($selected_categories)): ?>
                    <div class="active-tags-wrapper mb-4">
                        <span class="text-muted small me-2">Active Filters:</span>
                        <?php foreach ($selected_categories as $cat_id): ?>
                            <span class="tag shadow-sm">
                                <?= htmlspecialchars(getCategoryName($cat_id, $categories)) ?>
                                <a href="?state_id=<?= $state_id ?>&categories=<?= implode(',', array_diff($selected_categories, [$cat_id])) ?>" title="Remove">&times;</a>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php if (count($companies) > 0): ?>
                        <?php foreach ($companies as $company): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="company-card p-4 d-flex flex-column h-100">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="company-logo-wrapper">
                                            <?php if ($company['logo_url']): ?>
                                                <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                                            <?php else: ?>
                                                <i class="fas fa-building company-logo-placeholder"></i>
                                            <?php endif; ?>
                                        </div>
                                        </div>
                                    
                                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($company['name']) ?></h5>
                                    <p class="text-muted small mb-3 flex-grow-1">
                                        <?= !empty($company['tagline']) ? htmlspecialchars($company['tagline']) : 'A leading tech company in ' . $state['name'] ?>
                                    </p>
                                    
                                    <div class="d-flex align-items-center text-muted small mb-3">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i> 
                                        <?= htmlspecialchars($state['name']) ?>
                                    </div>

                                    <div class="mt-auto">
                                        <a href="company-profile.php?id=<?= $company['id'] ?>" class="btn btn-view-profile">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <div class="bg-white rounded-3 p-5 border border-dashed">
                                <i class="fas fa-search fa-3x text-muted mb-3 opacity-25"></i>
                                <h5 class="text-muted">No companies found</h5>
                                <p class="small text-muted mb-0">Try selecting different categories or browse another state.</p>
                                <a href="?state_id=<?= $state_id ?>" class="btn btn-outline-primary btn-sm mt-3">Reset Filters</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>