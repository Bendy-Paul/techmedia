<?php
require 'includes/wertyuytre.php';

// --- 1. Setup & Helpers ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Articles per page
$offset = ($page - 1) * $limit;
$search = $_GET['s'] ?? '';
$cat_filter = $_GET['cat'] ?? '';

// --- 2. Fetch Featured Article ---
// Only show on first page and when no specific search/filter is applied
$featured = null;
if ($page == 1) {
    $fStmt = $pdo->query("SELECT * FROM articles WHERE status='published' AND is_featured=1 ORDER BY published_at DESC LIMIT 1");
    $featured = $fStmt->fetch();
}

// --- 3. Build Query for Main List ---
$params = [];
$sql = "SELECT a.*, u.full_name as author_name 
        FROM articles a 
        LEFT JOIN users u ON a.author_id = u.id 
        WHERE a.status = 'published'";

// Exclude featured article from the main list if it exists
if ($featured) {
    $sql .= " AND a.id != " . $featured['id'];
}

// Search Filter
if (!empty($search)) {
    $sql .= " AND (a.title LIKE ? OR a.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Category Filter
if (!empty($cat_filter)) {
    $sql .= " AND a.id IN (
        SELECT article_id FROM article_categories ac 
        JOIN categories c ON ac.category_id = c.id 
        WHERE c.slug = ?
    )";
    $params[] = $cat_filter;
}

$sql .= " ORDER BY a.published_at DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// --- 4. Pagination Count ---
$countSql = "SELECT COUNT(*) FROM articles a WHERE a.status='published'";
if ($featured) $countSql .= " AND a.id != " . $featured['id'];
// For simplicity in this snippet, let's just run a count query that mimics the main one without limit
$countParams = $params; // Copy params
$countStmt = $pdo->prepare(str_replace("SELECT a.*, u.full_name as author_name", "SELECT COUNT(*)", substr($sql, 0, strpos($sql, "ORDER BY"))));
$countStmt->execute($countParams);
$total_articles = $countStmt->fetchColumn();
$total_pages = ceil($total_articles / $limit);


// --- 5. Sidebar Data ---
$cats = $pdo->query("SELECT * FROM categories WHERE type='article' ORDER BY name ASC")->fetchAll();
// Fallback if no 'article' types defined yet
if (empty($cats)) {
    $cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC LIMIT 10")->fetchAll();
}
$tags = $pdo->query("SELECT * FROM tags ORDER BY RAND() LIMIT 15")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech News & Insights | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>

    <style>
        /* News Hero */
        .news-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 120px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .news-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.05"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
            background-size: cover;
        }
        .news-hero-content {
            position: relative;
            z-index: 2;
        }

        /* News Card */
        .news-card {
            background: var(--bg-card);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            transition: 0.3s;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }
        .news-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        .news-body {
            padding: 25px;
        }
        .news-category {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 12px;
        }
        .news-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }
        .news-tag {
            background: rgba(37, 99, 235, 0.05);
            color: var(--text-muted);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
        }
        .news-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Category Filter */
        .category-filter {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            margin-bottom: 30px;
        }
        .filter-btn {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            margin: 5px;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .filter-btn:hover, .filter-btn.active {
            background: var(--primary-accent);
            color: white;
        }

        /* Sidebar */
        .sidebar-widget {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            margin-bottom: 30px;
            position: sticky;
            top: 80px;
        }
        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .tag-item {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-decoration: none;
            transition: 0.3s;
        }
        .tag-item:hover {
            background: var(--primary-accent);
            color: white;
        }
    </style>
</head>
<body>
    <?php include_once('event-ticker.php'); ?>
    <?php include_once('header.php'); ?>    
    <script>
        document.getElementById('news').classList.add('active');
        document.getElementById('news-mobile').classList.add('active');
    </script>

    <!-- News Hero -->
    <section class="news-hero">
        <div class="container news-hero-content">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <h1 class="display-4 fw-bold mb-4">Tech News & Insights</h1>
                    <p class="lead mb-4">Stay updated with the latest trends, innovations, and stories from Nigeria's booming tech ecosystem.</p>
                    <!-- <div class="input-group input-group-lg mx-auto" style="max-width: 500px;">
                        <input type="text" class="form-control" placeholder="Search articles..." value="<?= htmlspecialchars($search) ?>" onkeypress="if(event.key === 'Enter') window.location.href='?s='+this.value">
                        <button class="btn btn-primary" type="button" onclick="window.location.href='?s='+this.previousElementSibling.value"><i class="fas fa-search"></i></button>
                    </div> -->
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <!-- Featured Article -->
        <?php if ($featured): ?>
        <div class="news-card mb-5" data-aos="fade-up" data-aos-delay="100">
            <?php 
                $featImg = !empty($featured['featured_image_url']) ? $featured['featured_image_url'] : 'https://placehold.co/800x400/f1f5f9/2563eb?text=Featured+Tech+News';
            ?>
            <img src="<?= htmlspecialchars($featImg) ?>" class="news-img" alt="<?= htmlspecialchars($featured['title']) ?>">
            <div class="news-body">
                <span class="news-category">Featured</span>
                <h2 class="fw-bold mb-3"><?= htmlspecialchars($featured['title']) ?></h2>
                <p class="text-muted mb-3"><?= htmlspecialchars(substr(strip_tags($featured['content']), 0, 160)) ?>...</p>
                
                <!-- Featured Article Tags -->
                <?php
                    $ftStmt = $pdo->prepare("SELECT t.name FROM tags t JOIN article_tags at ON t.id = at.tag_id WHERE at.article_id = ? LIMIT 4");
                    $ftStmt->execute([$featured['id']]);
                    $fTags = $ftStmt->fetchAll(PDO::FETCH_COLUMN);
                ?>
                <div class="news-tags">
                    <?php foreach($fTags as $tag): ?>
                        <span class="news-tag">#<?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">                    
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Filter News (AJAX) -->
                <form id="news-filter-form" class="category-filter" data-aos="fade-up">
                    <h5 class="fw-bold mb-3">Filter by Category</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="filter-btn" data-category="">All</button>
                        <?php foreach($cats as $cat): ?>
                            <button type="button" class="filter-btn" data-category="<?= htmlspecialchars($cat['slug']) ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="Search articles...">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <input type="hidden" name="category" id="news-category-input" value="">
                </form>

                <!-- News Grid (AJAX) -->
                <div class="row g-4" id="news-list">
                    <!-- News will be loaded here by JS -->
                </div>

                <!-- Pagination (AJAX) -->
                <nav aria-label="Page navigation" class="mt-5">
                    <div id="news-pagination" class="d-flex justify-content-center"></div>
                </nav>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories Widget -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="200">
                    <h5 class="fw-bold mb-4">Categories</h5>
                    <ul class="list-unstyled">
                        <?php foreach($cats as $cat): ?>
                        <li class="mb-2">
                            <a href="?cat=<?= htmlspecialchars($cat['slug']) ?>" class="text-decoration-none d-flex justify-content-between">
                                <span><?= htmlspecialchars($cat['name']) ?></span> 
                                <!-- Optional: Add count logic if desired -->
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Tags Widget -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="300">
                    <h5 class="fw-bold mb-4">Popular Tags</h5>
                    <div class="tag-cloud">
                        <?php foreach($tags as $tag): ?>
                            <a href="?s=<?= urlencode($tag['name']) ?>" class="tag-item"><?= htmlspecialchars($tag['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Newsletter Widget -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="400">
                    <h5 class="fw-bold mb-3">Stay Updated</h5>
                    <p class="text-muted small mb-3">Get the latest tech news and insights delivered to your inbox.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary">Subscribe</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




<br><br>
    <?php include_once('footer.php'); ?>

</body>
<script src="assets/js/news-filter.js?<?= time() ?>"></script>
<script>
    // AJAX category button logic
    document.addEventListener('DOMContentLoaded', function () {
        const catBtns = document.querySelectorAll('.filter-btn');
        const catInput = document.getElementById('news-category-input');
        catBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                catBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                catInput.value = this.getAttribute('data-category');
                document.getElementById('news-filter-form').dispatchEvent(new Event('submit'));
            });
        });
    });
</script>
</body>
</html>