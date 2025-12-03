<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

// Pagination & Search
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

// Build Query
$sql = "SELECT e.*, c.name as city_name 
        FROM events e 
        LEFT JOIN cities c ON e.city_id = c.id 
        WHERE e.title LIKE :search";

if ($filter == 'upcoming') {
    $sql .= " AND e.start_datetime >= NOW()";
} elseif ($filter == 'past') {
    $sql .= " AND e.start_datetime < NOW()";
}

$sql .= " ORDER BY e.start_datetime DESC LIMIT :limit OFFSET :offset";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll();

    // Total Count
    $countSql = "SELECT COUNT(*) FROM events WHERE title LIKE ?";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(["%$search%"]);
    $total_events = $countStmt->fetchColumn();
    $total_pages = ceil($total_events / $limit);

} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Events Directory</h2>
            <p class="text-muted mb-0">Manage conferences, hackathons, and meetups.</p>
        </div>
        <a href="add-event.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add Event</a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search events..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="filter" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Events</option>
                        <option value="upcoming" <?= $filter == 'upcoming' ? 'selected' : '' ?>>Upcoming Only</option>
                        <option value="past" <?= $filter == 'past' ? 'selected' : '' ?>>Past Events</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-light w-100 border">Go</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Event</th>
                        <th>Type</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($events) > 0): ?>
                        <?php foreach ($events as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; overflow:hidden;">
                                        <?php if($row['banner_image_url']): ?>
                                            <img src="../<?= htmlspecialchars($row['banner_image_url']) ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <i class="fas fa-calendar-alt text-muted fa-lg"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                        <?= htmlspecialchars($row['title']) ?>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border text-capitalize"><?= htmlspecialchars($row['event_type']) ?></span></td>
                            <td>
                                <div class="small text-dark fw-bold"><?= date('M d, Y', strtotime($row['start_datetime'])) ?></div>
                                <div class="small text-muted"><?= date('h:i A', strtotime($row['start_datetime'])) ?></div>
                            </td>
                            <td>
                                <?php if($row['is_virtual']): ?>
                                    <span class="badge bg-info-subtle text-info"><i class="fas fa-video me-1"></i> Virtual</span>
                                <?php else: ?>
                                    <small><i class="fas fa-map-marker-alt text-muted me-1"></i> <?= htmlspecialchars($row['city_name'] ?? 'TBD') ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $now = new DateTime();
                                    $start = new DateTime($row['start_datetime']);
                                    if ($start > $now) echo '<span class="badge bg-success">Upcoming</span>';
                                    else echo '<span class="badge bg-secondary">Past</span>';
                                ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="edit-event.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                <a href="actions/delete-event.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this event?');"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No events found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
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