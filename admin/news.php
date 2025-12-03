<?php
// 1. Include Config & Layout
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

// 2. Handle Search & Filter
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

// 3. Pagination Setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// 4. Build Query
$sql = "SELECT a.*, u.full_name as author_name 
        FROM articles a
        LEFT JOIN users u ON a.author_id = u.id
        WHERE (a.title LIKE :search)";

if ($filter == 'published') {
    $sql .= " AND a.status = 'published'";
} elseif ($filter == 'draft') {
    $sql .= " AND a.status = 'draft'";
}

$sql .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";

// 5. Execute Query
try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll();

    // Get Total Count for Pagination
    $countSql = "SELECT COUNT(*) FROM articles WHERE title LIKE ?";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(["%$search%"]);
    $total_articles = $countStmt->fetchColumn();
    $total_pages = ceil($total_articles / $limit);

} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<main class="main-content">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">News & Articles</h2>
            <p class="text-muted mb-0">Manage blog posts and news updates.</p>
        </div>
        <div>
            <a href="add-article.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Write Article</a>
            <button class="btn btn-outline-primary d-lg-none ms-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php 
                if ($_GET['status'] == 'success') echo "Action completed successfully!";
                else echo "An error occurred.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Search & Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search title..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="filter" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Status</option>
                        <option value="published" <?= $filter == 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= $filter == 'draft' ? 'selected' : '' ?>>Drafts</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-light w-100 border">Go</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Article</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Published</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($articles) > 0): ?>
                        <?php foreach ($articles as $row): ?>
                        <tr>
                            <td class="ps-4" style="max-width: 300px;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded me-3" style="width: 50px; height: 50px; overflow:hidden;">
                                        <?php if($row['featured_image_url']): ?>
                                            <img src="../<?php echo htmlspecialchars($row['featured_image_url']); ?>" alt="Img" style="width:100%; height:100%; object-fit:cover;" loading="lazy">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark text-truncate"><?php echo htmlspecialchars($row['title']); ?></div>
                                        <small class="text-muted">Slug: <?php echo htmlspecialchars($row['slug']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-user-circle text-muted me-1 small"></i> 
                                <?php echo htmlspecialchars($row['author_name'] ?? 'Unknown'); ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'published'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($row['views']); ?></td>
                            <td class="text-muted small">
                                <?php echo $row['published_at'] ? date('M d, Y', strtotime($row['published_at'])) : '-'; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="edit-article.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="actions/delete-article.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure? This cannot be undone.');" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="text-muted mb-0">No articles found. <a href="add-article.php">Write your first post</a>.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="card-footer bg-white py-3">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>">Previous</a>
                    </li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>

</main>
</div> <!-- Close Wrapper -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>