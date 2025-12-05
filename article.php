<?php 
    include_once('./includes/wertyuytre.php'); 
    session_start();
            // Get the article slug from the URL
        if (!isset($_GET['slug'])) {
            header("Location: news.php");
            exit();
        }

        $slug = $_GET['slug'];

        // 1. Fetch the Main Article
        $stmt = $pdo->prepare("
            SELECT a.*, u.full_name as author_name, u.avatar_url as author_avatar
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.slug = ? AND a.status = 'published'
        ");
        $stmt->execute([$slug]);
        $article = $stmt->fetch();

        if (!$article) {
            // In a real app, redirect to a 404 page
            header("Location: news.php");
            exit();
        }

        // Increment View Count (Simple logic)
        $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article['id']]);

        // 2. Fetch Article Tags
        $tagsStmt = $pdo->prepare("
            SELECT t.name, t.id 
            FROM tags t
            JOIN article_tags at ON t.id = at.tag_id
            WHERE at.article_id = ?
        ");
        $tagsStmt->execute([$article['id']]);
        $tags = $tagsStmt->fetchAll();

        // 3. Fetch Related Articles (Based on similar tags or random)
        $relatedStmt = $pdo->prepare("
            SELECT id, title, slug, featured_image_url, published_at 
            FROM articles 
            WHERE id != ? AND status = 'published' 
            ORDER BY published_at DESC 
            LIMIT 3
        ");
        $relatedStmt->execute([$article['id']]);
        $related_articles = $relatedStmt->fetchAll();

        // 4. Fetch Categories for Sidebar (Standard Widget)
        $categories = $pdo->query("
            SELECT c.name, c.slug, COUNT(cc.company_id) as count 
            FROM categories c 
            LEFT JOIN company_categories cc ON c.id = cc.category_id 
            GROUP BY c.id 
            ORDER BY count DESC 
            LIMIT 6
        ")->fetchAll();

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> | Tech Media Directory</title>
    
    <?php include_once('links.php'); ?>
    <style>
        /* Article Hero */
        .article-hero {
            background: linear-gradient(135deg, #b3d0f6ff 0%, #f8fafc 100%);
            padding: 100px 0 40px;
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .article-category {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-accent);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .article-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 25px 0;
        }

        .article-tag {
            background: rgba(37, 99, 235, 0.05);
            color: var(--text-muted);
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            text-decoration: none;
            transition: 0.3s;
        }

        .article-tag:hover {
            background: var(--primary-accent);
            color: white;
        }

        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        
        /* Ensure images inside content are responsive */
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin: 20px 0;
        }

        .article-content h2 {
            margin-top: 40px;
            margin-bottom: 20px;
            color: var(--text-main);
            font-weight: 700;
        }

        .article-content p {
            margin-bottom: 20px;
        }
        
        .article-content ul, .article-content ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }
        
        .article-content li {
            margin-bottom: 10px;
        }

        .article-image-featured {
            border-radius: 16px;
            overflow: hidden;
            margin: 30px 0;
            box-shadow: var(--shadow-light);
        }

        .author-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(37, 99, 235, 0.1);
            margin: 40px 0;
        }

        .author-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary-accent);
            margin-right: 20px;
            overflow: hidden;
        }
        
        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        .related-article {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
        }
        
        .related-article:hover h6 {
            color: var(--primary-accent);
        }

        .related-article:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .related-img {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            background: #f1f5f9;
            margin-right: 15px;
            flex-shrink: 0;
            object-fit: cover;
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

        /* Footer */
        footer {
            background: #f1f5f9;
            border-top: 1px solid rgba(37, 99, 235, 0.1);
            padding: 80px 0 30px;
        }

        .footer-link {
            display: block;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .footer-link:hover { color: var(--primary-accent); }

        .social-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-main); text-decoration: none;
            transition: 0.3s;
        }

        .social-btn:hover { background: var(--primary-accent); color: #fff; }

        @media (max-width: 768px) {
            .article-hero {
                padding: 80px 0 30px;
            }
        }
    </style>
</head>
<body>
    
    <?php include_once('event-ticker.php')?>
    <?php include_once('header.php')?>
    <script>
        document.getElementById('news').classList.add('active')
        document.getElementById('news-mobile').classList.add('active')
    </script>

    <!-- Article Hero -->
    <section class="article-hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <span class="article-category">Tech News</span>
                    <h1 class="display-5 fw-bold mb-4"><?= htmlspecialchars($article['title']) ?></h1>
                    
                    <div class="article-meta">
                        <span><i class="fas fa-user me-1"></i> By <?= htmlspecialchars($article['author_name'] ?? 'Editorial Team') ?></span>
                        <span><i class="fas fa-calendar me-1"></i> <?= date('F j, Y', strtotime($article['published_at'])) ?></span>
                        <!-- Calculating approximate read time -->
                        <?php 
                            $word_count = str_word_count(strip_tags($article['content']));
                            $read_time = ceil($word_count / 200); 
                        ?>
                        <span><i class="fas fa-clock me-1"></i> <?= $read_time ?> min read</span>
                        <span><i class="fas fa-eye me-1"></i> <?= number_format($article['views']) ?> views</span>
                    </div>

                    <div class="article-tags">
                        <?php foreach($tags as $tag): ?>
                            <a href="news.php?tag=<?= urlencode($tag['name']) ?>" class="article-tag">#<?= htmlspecialchars($tag['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <div class="row">
            <!-- Article Content -->
            <div class="col-lg-8">
                <article class="article-content" data-aos="fade-up">
                    <?php if($article['featured_image_url']): ?>
                    <div class="article-image-featured">
                        <img src="<?= htmlspecialchars($article['featured_image_url']) ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($article['title']) ?>">
                    </div>
                    <?php endif; ?>

                    <!-- Output the HTML content directly from the DB (Ensure input was sanitized on save if allowing HTML) -->
                    <?= $article['content'] ?>
                    
                </article>

                <!-- Author Bio -->
                <div class="author-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="d-flex">
                        <div class="author-avatar">
                            <?php if(!empty($article['author_avatar'])): ?>
                                <img src="<?= htmlspecialchars($article['author_avatar']) ?>" alt="<?= htmlspecialchars($article['author_name']) ?>">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-2"><?= htmlspecialchars($article['author_name'] ?? 'Editorial Team') ?></h4>
                            <p class="text-muted mb-2">Content Creator & Tech Enthusiast</p>
                            <p class="small text-muted">Passionate about uncovering the latest trends in the African tech ecosystem.</p>
                        </div>
                    </div>
                </div>

                <!-- Social Share -->
                <div class="d-flex justify-content-between align-items-center py-4 border-top border-bottom" data-aos="fade-up" data-aos-delay="200">
                    <div>
                        <h5 class="fw-bold mb-2">Share this article</h5>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title']) ?>&url=<?= urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="social-btn"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied!'); return false;" class="social-btn"><i class="fas fa-link"></i></a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="300">
                    <h5 class="fw-bold mb-4">Directory Categories</h5>
                    <ul class="list-unstyled">
                        <?php foreach($categories as $cat): ?>
                        <li class="mb-2">
                            <a href="categories.php?slug=<?= htmlspecialchars($cat['slug']) ?>" class="text-decoration-none d-flex justify-content-between">
                                <span><?= htmlspecialchars($cat['name']) ?></span> 
                                <span class="text-muted"><?= $cat['count'] ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Related Articles -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="200">
                    <h5 class="fw-bold mb-4">Related Articles</h5>
                    
                    <?php if(count($related_articles) > 0): ?>
                        <?php foreach($related_articles as $related): ?>
                        <a href="article.php?slug=<?= $related['slug'] ?>" class="related-article">
                            <img src="<?= htmlspecialchars($related['featured_image_url'] ?? 'https://placehold.co/80x60') ?>" class="related-img" alt="Thumb">
                            <div>
                                <h6 class="fw-bold mb-1 small"><?= htmlspecialchars($related['title']) ?></h6>
                                <p class="text-muted small mb-0"><?= date('M d, Y', strtotime($related['published_at'])) ?></p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">No related articles found.</p>
                    <?php endif; ?>
                </div>

                <!-- Ad Container -->
                <div class="ad-container" data-aos="fade-left" data-aos-delay="100" style="position: sticky; top:100px">
                    <div class="card border-0 shadow-sm p-4 text-center bg-light">
                        <h5 class="text-muted mb-2">Advertisement</h5>
                        <p class="text-muted small mb-3">Looking to reach thousands of tech professionals?</p>
                        <a href="media-kit.html" class="btn btn-sm btn-outline-primary">Advertise With Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include_once('footer.php')?>
</body>
</html>