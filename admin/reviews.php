<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

// Pagination & Filter
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$filter = $_GET['filter'] ?? 'all';

// Build Query
$sql = "SELECT r.*, c.name as company_name, u.full_name as user_name 
        FROM reviews r 
        JOIN companies c ON r.company_id = c.id 
        JOIN users u ON r.user_id = u.id 
        WHERE 1=1";

if ($filter == 'pending') {
    $sql .= " AND r.is_approved = 0";
} elseif ($filter == 'approved') {
    $sql .= " AND r.is_approved = 1";
}

$sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetchAll();

    // Counts
    $pending_count = $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0")->fetchColumn();
    $total_reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    $total_pages = ceil($total_reviews / $limit);

} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Reviews Management</h2>
            <p class="text-muted mb-0">Monitor and moderate user feedback.</p>
        </div>
        <button class="btn btn-outline-primary d-lg-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex gap-2">
            <a href="?filter=all" class="btn btn-sm <?= $filter=='all'?'btn-primary':'btn-light' ?>">All Reviews</a>
            <a href="?filter=pending" class="btn btn-sm <?= $filter=='pending'?'btn-primary':'btn-light' ?>">
                Pending <span class="badge bg-white text-primary ms-1"><?= $pending_count ?></span>
            </a>
            <a href="?filter=approved" class="btn btn-sm <?= $filter=='approved'?'btn-primary':'btn-light' ?>">Published</a>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Company</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th style="width: 40%;">Comment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($reviews) > 0): ?>
                        <?php foreach ($reviews as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($row['company_name']) ?></td>
                            <td class="small"><?= htmlspecialchars($row['user_name']) ?></td>
                            <td>
                                <div class="text-warning small">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= $row['rating'] ? '' : 'text-muted opacity-25' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted d-block text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($row['comment']) ?>">
                                    <?= htmlspecialchars($row['comment']) ?>
                                </small>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <?php if($row['is_approved']): ?>
                                    <span class="badge bg-success-subtle text-success">Live</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <?php if(!$row['is_approved']): ?>
                                    <a href="actions/approve-review.php?id=<?= $row['id'] ?>&action=approve" class="btn btn-sm btn-success" title="Approve"><i class="fas fa-check"></i></a>
                                <?php else: ?>
                                    <a href="actions/approve-review.php?id=<?= $row['id'] ?>&action=hide" class="btn btn-sm btn-warning text-white" title="Hide"><i class="fas fa-eye-slash"></i></a>
                                <?php endif; ?>
                                
                                <a href="actions/delete-review.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Permanently delete this review?');"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No reviews found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($total_pages > 1): ?>
        <div class="card-footer bg-white py-3">
            <ul class="pagination justify-content-center mb-0">
                <?php for($i=1; $i<=$total_pages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&filter=<?= $filter ?>"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>