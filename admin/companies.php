<?php
// 1. Include Config & Layout
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

$companies = [];
$total_pages = 0;
$total_companies = 0;
$error = null;

// 2. Handle Search & Filter
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

// 3. Pagination Setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$limit = 10;
$offset = ($page - 1) * $limit;

// 4. Build Query
$sql = "SELECT c.*, 
               cities.name as city_name,
               states.name as state_name,
               countries.name as country_name
        FROM companies c
        LEFT JOIN cities ON c.city_id = cities.id
        LEFT JOIN states ON c.state_id = states.id
        LEFT JOIN countries ON c.country_id = countries.id
        WHERE (c.name LIKE :search OR c.email LIKE :search)";

// Apply filter if needed
if ($filter == 'verified') {
    $sql .= " AND c.is_verified = 1";
} elseif ($filter == 'pending') {
    $sql .= " AND c.is_verified = 0";
}

$sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";

// 5. Execute Query
// ... previous code remains the same until the try block ...

try {
    // Main query with positional parameters
    $sql = "SELECT c.*, 
                   cities.name as city_name,
                   states.name as state_name,
                   countries.name as country_name
            FROM companies c
            LEFT JOIN cities ON c.city_id = cities.id
            LEFT JOIN states ON c.state_id = states.id
            LEFT JOIN countries ON c.country_id = countries.id
            WHERE (c.name LIKE ? OR c.email LIKE ?)";
    
    if ($filter == 'verified') {
        $sql .= " AND c.is_verified = 1";
    } elseif ($filter == 'pending') {
        $sql .= " AND c.is_verified = 0";
    }
    
    $sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $companies = $stmt->fetchAll();

    // Get Total Count for Pagination
    $countSql = "SELECT COUNT(*) as total FROM companies c 
                 WHERE (c.name LIKE ? OR c.email LIKE ?)";
    
    if ($filter == 'verified') {
        $countSql .= " AND c.is_verified = 1";
    } elseif ($filter == 'pending') {
        $countSql .= " AND c.is_verified = 0";
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(["%$search%", "%$search%"]);
    $result = $countStmt->fetch(PDO::FETCH_ASSOC);
    $total_companies = $result['total'];
    $total_pages = $total_companies > 0 ? ceil($total_companies / $limit) : 0;

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
    error_log($error);
}
?>

<main class="main-content">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Companies Directory</h2>
            <p class="text-muted mb-0">Manage all business listings.</p>
        </div>
        <div>
            <a href="add-company.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add Company</a>
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
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="filter" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Status</option>
                        <option value="verified" <?= $filter == 'verified' ? 'selected' : '' ?>>Verified Only</option>
                        <option value="pending" <?= $filter == 'pending' ? 'selected' : '' ?>>Pending Verification</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-light w-100 border">Go</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Companies Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Company</th>
                        <th>Location</th>
                        <th>Subscription</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($companies) > 0): ?>
                        <?php foreach ($companies as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; overflow:hidden;">
                                        <?php if($row['logo_url']): ?>
                                            <img src="../<?php echo htmlspecialchars($row['logo_url']); ?>" alt="Logo" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <i class="fas fa-building text-muted fa-lg"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt text-muted me-1 small"></i> 
                                <?php echo htmlspecialchars($row['city_name'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <?php if($row['subscription_tier'] == 'gold'): ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-crown"></i> Gold</span>
                                <?php elseif($row['subscription_tier'] == 'silver'): ?>
                                    <span class="badge bg-secondary">Silver</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border">Free</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['is_verified']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="edit-company.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="actions/delete-company.php" method="POST" class="d-inline">
                                        <?php csrf_field() ?>
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this company? All related data (reviews, events) will also be deleted.');" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="../assets/images/empty.svg" alt="No Data" style="width: 100px; opacity: 0.5;" class="mb-3 d-block mx-auto">
                                <p class="text-muted">No companies found. <a href="add-company.php">Add one now</a>.</p>
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