<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

// Handle Add/Delete Logic directly here for simplicity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_stack'])) {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon_class']);
    $cat = trim($_POST['category']);
    if (!empty($name)) {
        $pdo->prepare("INSERT INTO stacks (name, icon_class, category) VALUES (?, ?, ?)")->execute([$name, $icon, $cat]);
        echo "<script>window.location.href='stacks.php';</script>";
    }
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM stacks WHERE id = ?")->execute([(int)$_GET['delete']]);
    echo "<script>window.location.href='stacks.php';</script>";
}

$stacks = $pdo->query("SELECT * FROM stacks ORDER BY category, name")->fetchAll();
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Manage Tech Stacks</h2>
    </div>

    <div class="row">
        <!-- Add Stack Form -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Add New Tool</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small text-muted">Tool Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Docker" required>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Icon Class (FontAwesome)</label>
                            <input type="text" name="icon_class" class="form-control" placeholder="e.g. fab fa-docker">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Category</label>
                            <select name="category" class="form-select">
                                <option value="Frontend">Frontend</option>
                                <option value="Backend">Backend</option>
                                <option value="Mobile">Mobile</option>
                                <option value="DevOps">DevOps</option>
                                <option value="Design">Design</option>
                                <option value="Data">Data/AI</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <button type="submit" name="add_stack" class="btn btn-primary w-100">Add Tool</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stacks List -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Icon</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stacks as $s): ?>
                            <tr>
                                <td class="ps-4"><i class="<?= htmlspecialchars($s['icon_class'] ?? 'fas fa-code') ?> text-muted fa-lg"></i></td>
                                <td class="fw-bold"><?= htmlspecialchars($s['name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($s['category']) ?></span></td>
                                <td class="text-end pe-4">
                                    <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this tool?')"><i class="fas fa-trash-alt"></i></a>
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
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>