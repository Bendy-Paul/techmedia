<?php
require_once 'includes/wertyuytre.php';

// --- 1. Capture Search Parameters ---
$search_query = $_GET['s'] ?? '';
$location_filter = $_GET['loc'] ?? '';
$category_filter = $_GET['cat'] ?? ''; // e.g. 'fintech'
$sort_by = $_GET['sort'] ?? 'relevance';

// Type Filters (Array of types to include)
// Default to all if none specified
$types = [];
if (isset($_GET['type_company'])) $types[] = 'company';
if (isset($_GET['type_event'])) $types[] = 'event';
if (isset($_GET['type_article'])) $types[] = 'article';
if (isset($_GET['type_job'])) $types[] = 'job';

if (empty($types)) $types = ['company', 'event', 'article', 'job'];

// --- 2. Pagination Setup ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; 
$offset = ($page - 1) * $limit;

// --- 3. Build Unified Query (UNION ALL) ---
// We need to normalize columns from different tables to match a common structure:
// type, id, title, description, image_url, meta_1 (location/author), meta_2 (date/size), meta_3 (extra), url_slug

$queries = [];
$params = [];

// -- Query for Companies --
if (in_array('company', $types)) {
    $q = "SELECT 
            'company' as type,
            c.id, 
            c.name as title, 
            c.tagline as description, 
            c.logo_url as image_url, 
            ci.name as meta_1, 
            c.team_size as meta_2, 
            'View Profile' as action_text,
            c.slug as url_slug,
            c.created_at
          FROM companies c
          LEFT JOIN cities ci ON c.city_id = ci.id
          WHERE 1=1";
    
    if (!empty($search_query)) {
        $q .= " AND (c.name LIKE ? OR c.description LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }
    if (!empty($location_filter)) {
        $q .= " AND ci.name LIKE ?";
        $params[] = "%$location_filter%";
    }
    // Category filtering for companies requires a join, for simplicity in UNION we skip deep cat filter 
    // unless we do a subquery or join. Let's keep it simple for text search primarily.
    
    $queries[] = $q;
}

// -- Query for Events --
if (in_array('event', $types)) {
    $q = "SELECT 
            'event' as type,
            e.id, 
            e.title, 
            e.description, 
            e.banner_image_url as image_url, 
            e.location_name as meta_1, 
            e.start_datetime as meta_2, 
            'View Event' as action_text,
            e.slug as url_slug,
            e.created_at
          FROM events e
          LEFT JOIN cities ci ON e.city_id = ci.id
          WHERE e.start_datetime >= NOW()"; // Only upcoming
    
    if (!empty($search_query)) {
        $q .= " AND (e.title LIKE ? OR e.description LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }
    if (!empty($location_filter)) {
        $q .= " AND (e.location_name LIKE ? OR ci.name LIKE ?)";
        $params[] = "%$location_filter%";
        $params[] = "%$location_filter%";
    }
    
    $queries[] = $q;
}

// -- Query for Articles --
if (in_array('article', $types)) {
    $q = "SELECT 
            'article' as type,
            a.id, 
            a.title, 
            a.content as description, 
            a.featured_image_url as image_url, 
            u.full_name as meta_1, 
            a.published_at as meta_2, 
            'Read Article' as action_text,
            a.slug as url_slug,
            a.created_at
          FROM articles a
          LEFT JOIN users u ON a.author_id = u.id
          WHERE a.status = 'published'";
    
    if (!empty($search_query)) {
        $q .= " AND (a.title LIKE ? OR a.content LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }
    // Articles don't usually have location, so ignore location filter or return empty if strict
    if (!empty($location_filter)) {
        $q .= " AND 1=0"; // Force empty if searching by location
    }
    
    $queries[] = $q;
}

// -- Query for Jobs --
if (in_array('job', $types)) {
    $q = "SELECT 
            'job' as type,
            j.id, 
            j.title, 
            j.description, 
            '' as image_url, 
            j.location as meta_1, 
            j.company_name as meta_2, 
            'View Job' as action_text,
            j.slug as url_slug,
            j.created_at
          FROM jobs j
          WHERE j.expires_at >= NOW()";
    
    if (!empty($search_query)) {
        $q .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.company_name LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }
    if (!empty($location_filter)) {
        $q .= " AND j.location LIKE ?";
        $params[] = "%$location_filter%";
    }
    
    $queries[] = $q;
}

// Combine Queries
if (count($queries) > 0) {
    $final_sql = implode(" UNION ALL ", $queries);
    
    // Sort
    if ($sort_by == 'newest') {
        $final_sql .= " ORDER BY created_at DESC";
    } else {
        // Default relevance (simulated by title match priority would require complex SQL)
        // Defaulting to newest for now
        $final_sql .= " ORDER BY created_at DESC";
    }
    
    // Pagination
    // We need to count total first
    $count_sql = "SELECT COUNT(*) FROM ($final_sql) as total_table";
    // We need to bind params twice? No, PDO can't reuse named params easily in UNIONs with ?
    // Actually, we must duplicate params for the count query if we use ? placeholders
    // A cleaner way for simple search:
    
    // Count Query Execution
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params); 
    $total_results = $stmt->fetchColumn();
    $total_pages = ceil($total_results / $limit);

    // Fetch Limit Query
    $final_sql .= " LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($final_sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
} else {
    $results = [];
    $total_results = 0;
    $total_pages = 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | Tech Media Directory</title>
    
    <?php include_once('links.php')?>

    <style>
        .search-header {
            background: #f8fafc;
            padding: 3rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .search-filters {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            position: sticky;
            top: 20px;
        }
        .result-card {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--primary-accent);
        }
        .result-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .type-company { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .type-event { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
        .type-article { background: rgba(168, 85, 247, 0.1); color: #7c3aed; }
        .type-job { background: rgba(249, 115, 22, 0.1); color: #f97316; }

        .result-image {
            width: 80px;
            height: 80px;
            border-radius: 0.75rem;
            object-fit: cover;
            background: #f1f5f9;
        }
        .pagination .page-link {
            border: none;
            color: #64748b;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 0.5rem;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--primary-accent);
            color: white;
        }
        .result-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        .result-meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.875rem;
            color: #64748b;
        }
    </style>
</head>
<body>

    <?php include_once('header.php') ?>

    <!-- Search Header -->
    <section class="search-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="fw-bold mb-3">Search Results</h1>
                    
                    <!-- Search Form -->
                    <form class="search-container mx-auto d-flex bg-white p-2 rounded-pill shadow-sm border" style="max-width: 600px;" method="GET">
                        <!-- Preserve other filters if needed, or simple reset -->
                        <div class="d-flex align-items-center flex-grow-1 px-3">
                            <i class="fas fa-search text-muted me-2"></i>
                            <input type="text" name="s" class="form-control border-0 shadow-none" placeholder="Search companies, events, news..." value="<?= htmlspecialchars($search_query) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-arrow-right"></i></button>
                    </form>
                    
                    <p class="text-muted mt-3 mb-0">
                        <?php if($search_query): ?>
                            Showing results for "<strong><?= htmlspecialchars($search_query) ?></strong>"
                        <?php else: ?>
                            Showing all results
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="mt-5 mb-5">
        <div class="container">
            <div class="row">
                <!-- Filters -->
                <div class="col-lg-3 mb-4">
                    <div class="search-filters">
                        <form method="GET">
                            <!-- Hidden Search Term -->
                            <input type="hidden" name="s" value="<?= htmlspecialchars($search_query) ?>">
                            
                            <h5 class="fw-bold mb-3">Filters</h5>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Type</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="type_company" value="1" id="filter-company" <?= in_array('company', $types) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="filter-company">Companies</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="type_event" value="1" id="filter-event" <?= in_array('event', $types) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="filter-event">Events</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="type_article" value="1" id="filter-article" <?= in_array('article', $types) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="filter-article">Articles</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="type_job" value="1" id="filter-job" <?= in_array('job', $types) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="filter-job">Jobs</label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Location</label>
                                <input type="text" name="loc" class="form-control" placeholder="e.g. Lagos" value="<?= htmlspecialchars($location_filter) ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Category (Slug)</label>
                                <input type="text" name="cat" class="form-control" placeholder="e.g. fintech" value="<?= htmlspecialchars($category_filter) ?>">
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>
                
                <!-- Results -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <p class="text-muted mb-0"><?= number_format($total_results) ?> results found</p>
                        <!-- Sorting UI (Just visual for now unless logic added) -->
                        <div>
                            <label class="form-label me-2 small text-muted">Sort by:</label>
                            <select class="form-select d-inline-block w-auto form-select-sm" onchange="window.location.href='?s=<?=urlencode($search_query)?>&sort='+this.value">
                                <option value="relevance" <?= $sort_by=='relevance'?'selected':'' ?>>Relevance</option>
                                <option value="newest" <?= $sort_by=='newest'?'selected':'' ?>>Newest</option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if (count($results) > 0): ?>
                        <?php foreach ($results as $row): 
                            // Determine Link and Meta based on Type
                            $link = '#';
                            $badgeClass = '';
                            $metaIcon1 = 'fa-map-marker-alt';
                            $metaIcon2 = 'fa-clock';
                            
                            if ($row['type'] === 'company') {
                                $link = "company/" . $row['url_slug'];
                                $badgeClass = 'type-company';
                                $metaIcon1 = 'fa-map-marker-alt'; // Location (City)
                                $metaIcon2 = 'fa-users'; // Team size
                            } elseif ($row['type'] === 'event') {
                                $link = "event-details/" . $row['url_slug'];
                                $badgeClass = 'type-event';
                                $metaIcon1 = 'fa-map-marker-alt'; // Venue
                                $metaIcon2 = 'fa-calendar-alt'; // Date
                            } elseif ($row['type'] === 'article') {
                                $link = "article/" . $row['url_slug'];
                                $badgeClass = 'type-article';
                                $metaIcon1 = 'fa-user-edit'; // Author
                                $metaIcon2 = 'fa-calendar-day'; // Published
                            } elseif ($row['type'] === 'job') {
                                $link = "job-details/" . $row['url_slug'];
                                $badgeClass = 'type-job';
                                $metaIcon1 = 'fa-map-marker-alt'; // Location
                                $metaIcon2 = 'fa-building'; // Company Name
                            }
                            
                            // Image Fallback
                            $img = !empty($row['image_url']) ? $row['image_url'] : 'https://placehold.co/100x100/f1f5f9/2563eb?text='.ucfirst($row['type']);
                        ?>
                        
                        <!-- Result Item -->
                        <div class="result-card">
                            <span class="result-type <?= $badgeClass ?>"><?= ucfirst($row['type']) ?></span>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img src="<?= htmlspecialchars($img) ?>" class="result-image" alt="Image">
                                </div>
                                <div class="col">
                                    <h4 class="fw-bold mb-2">
                                        <a href="<?= $link ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($row['title']) ?></a>
                                    </h4>
                                    <p class="text-muted mb-2 text-truncate" style="max-width: 600px;">
                                        <?= htmlspecialchars(substr(strip_tags($row['description']), 0, 150)) ?>...
                                    </p>
                                    
                                    <div class="result-meta">
                                        <!-- Meta 1 (Location/Author) -->
                                        <?php if(!empty($row['meta_1'])): ?>
                                        <div class="result-meta-item">
                                            <i class="fas <?= $metaIcon1 ?>"></i>
                                            <span><?= htmlspecialchars($row['meta_1']) ?></span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Meta 2 (Date/Size/Company) -->
                                        <?php if(!empty($row['meta_2'])): ?>
                                        <div class="result-meta-item">
                                            <i class="fas <?= $metaIcon2 ?>"></i>
                                            <span>
                                                <?php 
                                                    // Format if date
                                                    if (strtotime($row['meta_2']) && $row['type'] !== 'company') {
                                                        echo date('M d, Y', strtotime($row['meta_2']));
                                                    } else {
                                                        echo htmlspecialchars($row['meta_2']);
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-auto mt-3 mt-md-0">
                                    <a href="<?= $link ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4"><?= $row['action_text'] ?></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-5">
                            <img src="assets/images/empty.svg" alt="No results" style="width: 120px; opacity: 0.5;" class="mb-3">
                            <h4>No results found</h4>
                            <p class="text-muted">Try adjusting your search criteria or filters.</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Search results pagination" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?s=<?=urlencode($search_query)?>&page=<?=$page-1?>&loc=<?=urlencode($location_filter)?>" tabindex="-1">Previous</a>
                            </li>
                            
                            <!-- Page Numbers (Simple loop) -->
                            <?php for($i=1; $i<=$total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?s=<?=urlencode($search_query)?>&page=<?=$i?>&loc=<?=urlencode($location_filter)?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Next -->
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?s=<?=urlencode($search_query)?>&page=<?=$page+1?>&loc=<?=urlencode($location_filter)?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php include_once('footer.php')?>
</body>
</html>