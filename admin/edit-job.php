<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

if (!isset($_GET['id'])) {
    header("Location: jobs.php");
    exit();
}
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) die("Job not found.");

$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC")->fetchAll();
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Job</h2>
            <p class="text-muted mb-0">Editing: <?= htmlspecialchars($job['title']) ?></p>
        </div>
        <a href="jobs.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back</a>
    </div>

    <form action="actions/update-job.php" method="POST">
        <input type="hidden" name="id" value="<?= $job['id'] ?>">
        <?php csrf_field() ?>

        
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Job Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($job['title']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Company</label>
                            <div class="input-group">
                                <select name="company_id" class="form-select">
                                    <option value="">-- Manual Entry --</option>
                                    <?php foreach($companies as $comp): ?>
                                        <option value="<?= $comp['id'] ?>" <?= $job['company_id'] == $comp['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($comp['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="company_name_manual" class="form-control" placeholder="Or type company name" value="<?= empty($job['company_id']) ? htmlspecialchars($job['company_name']) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea name="description" class="form-control" rows="8" required><?= htmlspecialchars($job['description']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Job Details</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Type</label>
                            <select name="job_type" class="form-select">
                                <?php 
                                $types = ['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'];
                                foreach($types as $t): ?>
                                    <option value="<?= $t ?>" <?= $job['job_type'] == $t ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Location</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($job['location']) ?>">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_remote" value="1" id="remoteCheck" <?= $job['is_remote'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="remoteCheck">Remote</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Salary Range</label>
                            <input type="text" name="salary_range" class="form-control" value="<?= htmlspecialchars($job['salary_range'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Experience Level</label>
                            <select name="experience_level" class="form-select">
                                <?php 
                                $levels = ['Entry Level', 'Mid Level', 'Senior', 'Executive'];
                                foreach($levels as $l): ?>
                                    <option value="<?= $l ?>" <?= $job['experience_level'] == $l ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Apply Link / Email</label>
                            <input type="text" name="apply_link" class="form-control" value="<?= htmlspecialchars($job['apply_link'] ?? '') ?>">
                        </div>
                         <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Skills</label>
                            <input type="text" name="skills" class="form-control" value="<?= htmlspecialchars($job['skills'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Expiry Date</label>
                            <input type="date" name="expires_at" class="form-control" value="<?= date('Y-m-d', strtotime($job['expires_at'])) ?>">
                        </div>
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featCheck" <?= $job['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="featCheck">Featured Job</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_urgent" value="1" id="urgCheck" <?= $job['is_urgent'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="urgCheck">Urgent Hiring</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Update Job</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>