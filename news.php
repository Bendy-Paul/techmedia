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
                    <div class="input-group input-group-lg mx-auto" style="max-width: 500px;">
                        <input type="text" class="form-control" placeholder="Search articles..." value="<?= htmlspecialchars($search) ?>" onkeypress="if(event.key === 'Enter') window.location.href='?s='+this.value">
                        <button class="btn btn-primary" type="button" onclick="window.location.href='?s='+this.previousElementSibling.value"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Category Filter -->
                <div class="category-filter" data-aos="fade-up">
                    <h5 class="fw-bold mb-3">Filter by Category</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= $url_path ?>/news" class="filter-btn <?= empty($cat_filter) ? 'active' : '' ?>">All</a>
                        <?php foreach($cats as $cat): ?>
                            <a href="?cat=<?= htmlspecialchars($cat['slug']) ?>" class="filter-btn <?= $cat_filter == $cat['slug'] ? 'active' : '' ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

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
                        
                        <div class="news-meta">
                            <span><i class="fas fa-user me-1"></i> By <?= htmlspecialchars($featured['author_name'] ?? 'Editorial') ?></span>
                            <span><i class="fas fa-calendar me-1"></i> <?= date('F j, Y', strtotime($featured['published_at'])) ?></span>
                            <span><i class="fas fa-clock me-1"></i> <?= ceil(str_word_count(strip_tags($featured['content'])) / 200) ?> min read</span>
                        </div>
                        <a href="<?= $url_path ?>/article/<?= htmlspecialchars($featured['slug']) ?>" class="btn btn-primary mt-3">Read Full Article</a>
                    </div>
                </div>
                <?php endif; ?>
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

    <section class="news-section container">
        <!-- News Grid -->
        <div class="row g-4">
            <?php if (count($articles) > 0): ?>
                <?php foreach($articles as $article): ?>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="news-card h-100">
                            <?php 
                                $artImg = !empty($article['featured_image_url']) ? $article['featured_image_url'] : 'https://placehold.co/600x400/f1f5f9/2563eb?text=News';
                            ?>
                            <img src="<?= htmlspecialchars($artImg) ?>" class="news-img" alt="<?= htmlspecialchars($article['title']) ?>">
                            <div class="news-body">
                                <span class="news-category">News</span>
                                <h4 class="fw-bold mb-2"><?= htmlspecialchars($article['title']) ?></h4>
                                <p class="text-muted small mb-3"><?= htmlspecialchars(substr(strip_tags($article['content']), 0, 120)) ?>...</p>
                                
                                <?php
                                    $tStmt = $pdo->prepare("SELECT t.name FROM tags t JOIN article_tags at ON t.id = at.tag_id WHERE at.article_id = ? LIMIT 3");
                                    $tStmt->execute([$article['id']]);
                                    $artTags = $tStmt->fetchAll(PDO::FETCH_COLUMN);
                                ?>
                                <div class="news-tags">
                                    <?php foreach($artTags as $tag): ?>
                                        <span class="news-tag">#<?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="news-meta">
                                    <span><i class="fas fa-calendar me-1"></i> <?= date('M d, Y', strtotime($article['published_at'])) ?></span>
                                    <span><i class="fas fa-clock me-1"></i> <?= ceil(str_word_count(strip_tags($article['content'])) / 200) ?> min</span>
                                </div>
                                <a href="<?= $url_path ?>/article/<?= htmlspecialchars($article['slug']) ?>" class="btn btn-outline-primary btn-sm mt-2">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted lead">No articles found.</p>
                    <a href="<?= $url_path ?>/news" class="btn btn-outline-primary">View All News</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?><?= $search ? '&s='.$search : '' ?><?= $cat_filter ? '&cat='.$cat_filter : '' ?>" tabindex="-1">Previous</a>
                </li>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= $search ? '&s='.$search : '' ?><?= $cat_filter ? '&cat='.$cat_filter : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?><?= $search ? '&s='.$search : '' ?><?= $cat_filter ? '&cat='.$cat_filter : '' ?>">Next</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </section>


<br><br>
    <?php include_once('footer.php'); ?>

    <script>
        // Category filter functionality (Optional: Keeps button active state visual)
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>