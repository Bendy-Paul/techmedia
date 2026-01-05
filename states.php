<?php
// states.php - List companies by state and filter by categories
require_once 'includes/wertyuytre.php';

// Get all states ordered by company count (top 20)
$states = $pdo->query("SELECT s.*, COUNT(c.id) as company_count FROM states s LEFT JOIN companies c ON s.id = c.state_id GROUP BY s.id ORDER BY company_count DESC, s.name ASC LIMIT 20")->fetchAll();

// Get all categories for filtering
$categories = $pdo->query("SELECT DISTINCT * FROM categories WHERE type='company' ORDER BY name ASC")->fetchAll();

// Get selected state
$slug = isset($_GET['slug']) ? $_GET['slug'] : null;
$selected_categories = isset($_GET['categories']) ? (array)$_GET['categories'] : [];

$companies = [];
$state = null;
$state_id = null;
if ($slug) {
    $state = $pdo->prepare("SELECT * FROM states WHERE slug = ?");
    $state->execute([$slug]);
    $state = $state->fetch();
    if ($state) {
        $state_id = $state['id'];
        $state_name = $state['name'];
        
        // Build category filter for PHP fallback
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
    }else{
        header("location:$url_path/");
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
    <title>Browse Tech Companies in <?php echo $state ? $state['name'] : 'All States'; ?> | TechMedia</title>
    <?php include_once('links.php'); ?>
    <link rel="stylesheet" href="<?= $url_path ?>/assets/css/states.css">
    <style>
        .pagination {
    justify-content: center !important;
}
    </style>
</head>

<body data-state-slug="<?= htmlspecialchars($state['slug'] ?? '') ?>" data-state-name="<?= htmlspecialchars($state_name ?? '') ?>" data-state-id="<?= htmlspecialchars($state_id ?? '') ?>">

<?php include 'header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1>Discover Tech Companies</h1>
        <p>Explore innovative companies across different states and industries</p>
    </div>
</div>

<div class="container">
    <!-- States Selection -->
    <div class="states-section">
        <div class="states-container">
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i>
                <h2>Browse by State</h2>
            </div>
            
            <div class="states-scroll">
                <?php foreach ($states as $s): ?>
                    <a href="<?= $url_path .'/states'.'/'. $s['slug'] ?>" class="state-chip <?= $slug == $s['slug'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($s['name']) ?>
                        <span class="count"><?= $s['company_count'] ?></span>
                    </a>
                <?php endforeach; ?>
                <a href="all-states.php" class="state-chip" style="border-style: dashed; background: transparent;">
                    View All <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <?php if ($state): ?>
        <!-- Main Content -->
        <div class="content-wrapper">
            <!-- Sidebar Filter -->
            <aside class="filter-sidebar">
                <div class="filter-card">
                    <div class="filter-header">
                        <h3>Filter Categories</h3>
                        <?php if(!empty($selected_categories)): ?>
                            <a href="<?= $url_path . '/states/' . $state['slug'] ?>" class="clear-btn">
                                <i class="fas fa-times"></i> Clear All
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="get" id="filterForm">
                        <input type="hidden" name="slug" value="<?= $state['slug'] ?>">
                        
                        <div class="filter-categories">
                            <!-- Tech Categories Group -->
                            <div class="category-group">
                                <div class="category-group-title">Technology</div>
                                <div class="category-list">
                                    <?php 
                                    $tech_categories = array_filter($categories, function($cat) {
                                        return stripos($cat['name'], 'tech') !== false || 
                                               stripos($cat['name'], 'software') !== false ||
                                               stripos($cat['name'], 'ai') !== false ||
                                               stripos($cat['name'], 'cloud') !== false ||
                                               stripos($cat['name'], 'web') !== false ||
                                               stripos($cat['name'], 'mobile') !== false ||
                                               stripos($cat['name'], 'data') !== false;
                                    });
                                    
                                    foreach ($tech_categories as $cat): ?>
                                        <div class="category-item">
                                            <input type="checkbox" 
                                                   class="category-checkbox" 
                                                   id="cat_<?= $cat['id'] ?>" 
                                                   name="categories[]" 
                                                   value="<?= $cat['id'] ?>" 
                                                   <?= in_array($cat['id'], $selected_categories) ? 'checked' : '' ?> 
                                                   >
                                            <label for="cat_<?= $cat['id'] ?>" class="category-label">
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Industry Categories Group -->
                            <div class="category-group">
                                <div class="category-group-title">Industry</div>
                                <div class="category-list">
                                    <?php 
                                    $industry_categories = array_filter($categories, function($cat) use ($tech_categories) {
                                        return !in_array($cat, $tech_categories, true);
                                    });
                                    
                                    // Sort remaining categories alphabetically
                                    usort($industry_categories, function($a, $b) {
                                        return strcmp($a['name'], $b['name']);
                                    });
                                    
                                    foreach ($industry_categories as $cat): ?>
                                        <div class="category-item">
                                            <input type="checkbox" 
                                                   class="category-checkbox" 
                                                   id="cat_<?= $cat['id'] ?>" 
                                                   name="categories[]" 
                                                   value="<?= $cat['id'] ?>" 
                                                   <?= in_array($cat['id'], $selected_categories) ? 'checked' : '' ?> >
                                            <label for="cat_<?= $cat['id'] ?>" class="category-label">
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Companies Grid -->
            <main class="companies-section">
                <div class="companies-header">
                    <div>
                        <h2><?= htmlspecialchars($state_name) ?> Companies</h2>
                    </div>
                    <div class="companies-count" id="companies-count">
                        <?= count($companies) ?> company<?= count($companies) !== 1 ? 'ies' : 'y' ?>
                    </div>
                </div>

                <?php if (!empty($selected_categories)): ?>
                    <div class="active-filters">
                        <span class="active-filters-label">Active filters:</span>
                        <?php foreach ($selected_categories as $cat_id): 
                            $cat_name = getCategoryName($cat_id, $categories);
                            if ($cat_name): ?>
                                <span class="filter-tag">
                                    <?= htmlspecialchars($cat_name) ?>
                                    <a href="#" class="remove" onclick="removeCategory(<?= $cat_id ?>)" title="Remove filter">
                                        ×
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="companies-grid" id="companies-grid"></div>
                <div id="companies-pagination" class="mt-4 m-auto text-center"></div>
                    <?php if (count($companies) > 0): ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>No companies found</h3>
                            <p>Try selecting different categories or browse another state to discover more companies.</p>
                            <a href="<?= $url_path . '/states/' . $state['slug'] ?>" class="view-profile-btn" style="max-width: 200px; margin: 0 auto;">
                                Reset Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    <?php else: ?>
        <!-- Initial State - No State Selected -->
        <div class="empty-state" style="margin: 4rem auto; max-width: 600px;">
            <div class="empty-state-icon">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <h3>Select a State to Begin</h3>
            <p>Choose a state from the options above to explore companies in that region. You can then filter by categories to find exactly what you're looking for.</p>
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                <?php if (!empty($states)): ?>
                    <a href="<?= $url_path . '/states/' . $states[0]['slug'] ?>" class="view-profile-btn" style="max-width: 200px;">
                        Start Exploring
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script>
    window.TECHMEDIA_URL_PATH = '<?= $url_path ?>';
</script>
<script src="<?= $url_path ?>/assets/js/states-companies.js?3"></script>
</body>
</html>