<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$jobs = [];
$total_jobs = 0;
$total_pages = 1;
$error = null;

// Create search term with wildcards
$searchTerm = "%$search%";

// Use unique parameter names for each occurrence
$sql = "SELECT j.*, c.name as company_name 
        FROM jobs j 
        LEFT JOIN companies c ON j.company_id = c.id 
        WHERE j.title LIKE :search1 OR c.name LIKE :search2 
        ORDER BY j.created_at DESC 
        LIMIT :limit OFFSET :offset";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search1', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':search2', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $jobs = $stmt->fetchAll();

    // Use unique parameter names here too
    $countSql = "SELECT COUNT(*) FROM jobs j 
                 LEFT JOIN companies c ON j.company_id = c.id 
                 WHERE j.title LIKE :search1 OR c.name LIKE :search2";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->bindValue(':search1', $searchTerm, PDO::PARAM_STR);
    $countStmt->bindValue(':search2', $searchTerm, PDO::PARAM_STR);
    $countStmt->execute();
    $total_jobs = $countStmt->fetchColumn();
    $total_pages = ceil($total_jobs / $limit);

} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
    // For debugging, uncomment:
    // echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>$error</div>";
}
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Job Board</h2>
            <p class="text-muted mb-0">Manage job listings.</p>
        </div>
        <a href="add-job.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Post Job</a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_GET['status'] == 'success' ? "Action completed successfully!" : "An error occurred."; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search jobs..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-light w-100 border">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Company</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Posted</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($jobs) > 0): ?>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= htmlspecialchars($job['title']) ?></td>
                            <td><?= htmlspecialchars($job['company_name']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($job['job_type']) ?></span></td>
                            <td>
                                <?php if (strtotime($job['expires_at']) > time()): ?>
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">Expired</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                            <td class="text-end pe-4">
                                <a href="edit-job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                <a href="actions/delete-job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this job?');"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No jobs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($total_pages > 1): ?>
        <div class="card-footer bg-white py-3">
            <ul class="pagination justify-content-center mb-0">
                <?php for($i=1; $i<=$total_pages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
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