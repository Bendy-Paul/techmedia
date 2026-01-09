<?php
// 1. Include Config & Layout
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

// 2. Fetch Categories
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<main class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Category Management</h2>
            <p class="text-muted mb-0">Manage business categories for your directory.</p>
        </div>
        <!-- Mobile Sidebar Toggle -->
        <button class="btn btn-outline-primary d-lg-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php 
                if ($_GET['status'] == 'success') echo "Action completed successfully!";
                else echo "An error occurred. Please try again.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Add Category Form -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">Add New Category</h6>
                </div>
                <div class="card-body">
                    <form action="actions/save-category.php" method="POST">
                        <?php csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Parent</label>
                            <select name="parent_id" class="form-select">
                                <option value="0">-- None --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Category Type</label>
                            <select name="type" class="form-select">
                                <option value="article">Article</option>
                                <option value="company">Company</option>
                                <option value="event">Event</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Category Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Fintech" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Icon Class (FontAwesome)</label>
                            <input type="text" name="icon_class" class="form-control" placeholder="e.g. fas fa-wallet">
                            <div class="form-text">Use free FontAwesome 5 class names.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Category</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Categories List -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">All Categories</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Icon</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="<?php echo htmlspecialchars($category['icon_class'] ?? 'fas fa-tag'); ?> text-primary"></i>
                                    </div>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($category['name']); ?></td>
                                <td class="text-muted small"><?php echo htmlspecialchars($category['slug']); ?></td>
                                <td class="text-end pe-4">
                                    <!-- Delete Button -->
                                    <form action="actions/delete-category.php" method="post" class="d-inline">
                                        <?php csrf_field() ?>
                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-light text-danger" onclick="return confirm('Are you sure? Companies in this category will lose this tag.');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</main>
</div> <!-- Close .admin-wrapper -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>