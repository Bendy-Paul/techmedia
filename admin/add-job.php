<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC")->fetchAll();
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Post New Job</h2>
            <p class="text-muted mb-0">Create a new job listing.</p>
        </div>
        <a href="jobs.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back</a>
    </div>

    <form action="actions/save-job.php" method="POST">
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Job Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Company</label>
                            <div class="input-group">
                                <select name="company_id" class="form-select">
                                    <option value="">-- Manual Entry --</option>
                                    <?php foreach($companies as $comp): ?>
                                        <option value="<?= $comp['id'] ?>"><?= htmlspecialchars($comp['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="company_name_manual" class="form-control" placeholder="Or type company name">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea name="description" class="form-control" rows="8" required></textarea>
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
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Lagos">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_remote" value="1" id="remoteCheck">
                            <label class="form-check-label" for="remoteCheck">Remote</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Salary Range</label>
                            <input type="text" name="salary_range" class="form-control" placeholder="e.g. ₦150k - ₦250k">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Experience Level</label>
                            <select name="experience_level" class="form-select">
                                <option value="Entry Level">Entry Level</option>
                                <option value="Mid Level" selected>Mid Level</option>
                                <option value="Senior">Senior</option>
                                <option value="Executive">Executive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Apply Link / Email</label>
                            <input type="text" name="apply_link" class="form-control" placeholder="https://... or mailto:...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Skills (Comma separated)</label>
                            <input type="text" name="skills" class="form-control" placeholder="React, PHP, Design">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Expiry Date</label>
                            <input type="date" name="expires_at" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                        </div>
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featCheck">
                            <label class="form-check-label" for="featCheck">Featured Job</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_urgent" value="1" id="urgCheck">
                            <label class="form-check-label" for="urgCheck">Urgent Hiring</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Post Job</button>
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