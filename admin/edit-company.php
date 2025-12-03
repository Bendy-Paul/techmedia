<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

if (!isset($_GET['id'])) {
    header("Location: companies.php");
    exit();
}
$id = (int)$_GET['id'];

// Fetch Company Data
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();
if (!$company) die("Company not found.");

// Fetch Related Data
$projects = $pdo->query("SELECT * FROM company_projects WHERE company_id = $id")->fetchAll();
$gallery_images = $pdo->query("SELECT * FROM company_galleries WHERE company_id = $id")->fetchAll();
$social = json_decode($company['social_links'] ?? '{}', true);
$stats = json_decode($company['profile_stats'] ?? '{}', true);

// Dropdowns
$all_categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$cities = $pdo->query("SELECT * FROM cities ORDER BY name ASC")->fetchAll();

// Selected Categories
$stmt_cat = $pdo->prepare("SELECT category_id FROM company_categories WHERE company_id = ?");
$stmt_cat->execute([$id]);
$selected_cats = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
?>


<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Company</h2>
            <p class="text-muted mb-0">Updating: <?= htmlspecialchars($company['name']) ?></p>
        </div>
        <a href="companies.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back</a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
        <div class="alert alert-danger">
            Error: <?= htmlspecialchars($_GET['msg'] ?? 'Unknown error') ?>
        </div>
    <?php endif; ?>

    <form action="actions/update-company.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $company['id'] ?>">
        
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Basic Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Company Details</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Company Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($company['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tagline</label>
                            <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($company['tagline'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea name="description" class="form-control" rows="6"><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                
                <!-- Projects Section (Dynamic) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Portfolio Projects</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addProject()">+ Add Project</button>
                    </div>
                    <div class="card-body" id="projects_container">
                        
                        <?php if(count($projects) > 0): ?>
                            <?php foreach($projects as $index => $proj): ?>
                            <div class="border rounded p-3 mb-3 position-relative existing-project" id="exist_proj_<?= $proj['id'] ?>">
                                <input type="hidden" name="existing_projects[<?= $proj['id'] ?>][id]" value="<?= $proj['id'] ?>">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="markProjectForDelete(<?= $proj['id'] ?>)"></button>
                                <div class="mb-2">
                                    <label class="small text-muted">Title</label>
                                    <input type="text" name="existing_projects[<?= $proj['id'] ?>][title]" class="form-control form-control-sm fw-bold" value="<?= htmlspecialchars($proj['title']) ?>">
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted">Description</label>
                                    <textarea name="existing_projects[<?= $proj['id'] ?>][description]" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($proj['description']) ?></textarea>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small text-center" id="no_projects_msg">No projects added yet.</p>
                        <?php endif; ?>

                        <!-- New projects will be appended here -->
                    </div>
                    <!-- Hidden input to store IDs of projects to delete -->
                    <input type="hidden" name="delete_project_ids" id="delete_project_ids">
                </div>

                <!-- Gallery Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Company Gallery</h6></div>
                    <div class="card-body">
                        <!-- Existing Images -->
                        <?php if(count($gallery_images) > 0): ?>
                        <div class="row g-2 mb-3">
                            <?php foreach($gallery_images as $img): ?>
                            <div class="col-3 position-relative" id="gal_img_<?= $img['id'] ?>">
                                <img src="../<?= htmlspecialchars($img['image_url']) ?>" class="img-thumbnail" style="height: 100px; width:100%; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 px-1" onclick="deleteGalleryImage(<?= $img['id'] ?>)">&times;</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="delete_gallery_ids" id="delete_gallery_ids">
                        <?php endif; ?>

                        <!-- Upload New -->
                        <label class="form-label fw-bold small text-muted">Add New Images</label>
                        <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                    </div>
                </div>

                <!-- Contact & Metrics (Updated) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Contact & Metrics</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($company['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($company['phone'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Website URL</label>
                            <input type="url" name="website_url" class="form-control" value="<?= htmlspecialchars($company['website_url'] ?? '') ?>">
                        </div>

                        <hr class="text-muted">

                        <!-- New Metrics Fields -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted">Year Founded</label>
                                <input type="number" name="year_founded" class="form-control" value="<?= htmlspecialchars($company['year_founded'] ?? '') ?>" placeholder="2015">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted">Team Size</label>
                                <select name="team_size" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="1-10" <?= ($company['team_size'] ?? '') == '1-10' ? 'selected' : '' ?>>1-10 Employees</option>
                                    <option value="11-50" <?= ($company['team_size'] ?? '') == '11-50' ? 'selected' : '' ?>>11-50 Employees</option>
                                    <option value="51-200" <?= ($company['team_size'] ?? '') == '51-200' ? 'selected' : '' ?>>51-200 Employees</option>
                                    <option value="200+" <?= ($company['team_size'] ?? '') == '200+' ? 'selected' : '' ?>>200+ Employees</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted">Starting Price (NGN)</label>
                                <input type="number" name="starting_cost" class="form-control" value="<?= htmlspecialchars($company['starting_cost'] ?? '') ?>" placeholder="500000">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location & Categories -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Location & Categories</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">City</label>
                                <select name="city_id" class="form-select">
                                    <option value="">Select City...</option>
                                    <?php foreach($cities as $city): ?>
                                        <option value="<?= $city['id'] ?>" <?= $city['id'] == $company['city_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($city['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Categories</label>
                                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                    <?php foreach($all_categories as $cat): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>"
                                            <?= in_array($cat['id'], $selected_cats) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Full Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Branding</h6></div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted d-block text-start">Current Logo</label>
                            <?php if($company['logo_url']): ?>
                                <img src="../<?= htmlspecialchars($company['logo_url']) ?>" class="img-thumbnail mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <p class="text-muted small">No logo uploaded.</p>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control form-control-sm mt-2">
                            <small class="text-muted d-block text-start mt-1">Upload new to replace.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted d-block text-start">Current Cover</label>
                            <?php if(!empty($company['cover_image_url'])): ?>
                                <img src="../<?= htmlspecialchars($company['cover_image_url']) ?>" class="img-thumbnail mb-2" style="width: 100%; height: 100px; object-fit: cover;">
                            <?php endif; ?>
                            <input type="file" name="cover" class="form-control form-control-sm mt-2">
                        </div>
                    </div>
                </div>

                <!-- Social Media Links (New) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Social Links</h6></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                <input type="text" name="social[twitter]" class="form-control" placeholder="Twitter Handle" value="<?= htmlspecialchars($social['twitter'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                <input type="text" name="social[linkedin]" class="form-control" placeholder="LinkedIn URL" value="<?= htmlspecialchars($social['linkedin'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                <input type="text" name="social[facebook]" class="form-control" placeholder="Facebook URL" value="<?= htmlspecialchars($social['facebook'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Stats (New) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Profile Stats (Manual)</h6></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-0">Response Rate (%)</label>
                            <input type="number" name="stats[response_rate]" class="form-control form-control-sm" value="<?= htmlspecialchars($stats['response_rate'] ?? '90') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-0">Completeness (%)</label>
                            <input type="number" name="stats[completeness]" class="form-control form-control-sm" value="<?= htmlspecialchars($stats['completeness'] ?? '80') ?>">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Settings</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Subscription Tier</label>
                            <select name="subscription_tier" class="form-select">
                                <option value="free" <?= $company['subscription_tier'] == 'free' ? 'selected' : '' ?>>Free</option>
                                <option value="silver" <?= $company['subscription_tier'] == 'silver' ? 'selected' : '' ?>>Silver</option>
                                <option value="gold" <?= $company['subscription_tier'] == 'gold' ? 'selected' : '' ?>>Gold</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="verifyCheck" <?= $company['is_verified'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="verifyCheck">Verify Listing</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featureCheck" <?= $company['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="featureCheck">Feature on Homepage</label>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Update Listing</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
</div>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let newProjectCount = 0;
    
    function addProject() {
        // Remove "No projects" message if exists
        const noMsg = document.getElementById('no_projects_msg');
        if(noMsg) noMsg.style.display = 'none';

        const container = document.getElementById('projects_container');
        const html = `
            <div class="border rounded p-3 mb-3 position-relative new-project-row" id="new_proj_${newProjectCount}">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeNewProject(${newProjectCount})"></button>
                <div class="mb-2">
                    <input type="text" name="new_projects[${newProjectCount}][title]" class="form-control form-control-sm fw-bold" placeholder="New Project Title">
                </div>
                <div class="mb-0">
                    <textarea name="new_projects[${newProjectCount}][description]" class="form-control form-control-sm" rows="2" placeholder="Description..."></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        newProjectCount++;
    }

    function removeNewProject(id) {
        document.getElementById('new_proj_' + id).remove();
    }

    function markProjectForDelete(id) {
        if(confirm('Delete this project?')) {
            document.getElementById('exist_proj_' + id).style.display = 'none';
            // Add ID to hidden input
            let input = document.getElementById('delete_project_ids');
            let ids = input.value ? input.value.split(',') : [];
            ids.push(id);
            input.value = ids.join(',');
        }
    }

    function deleteGalleryImage(id) {
        if(confirm('Remove image?')) {
            document.getElementById('gal_img_' + id).style.display = 'none';
            let input = document.getElementById('delete_gallery_ids');
            let ids = input.value ? input.value.split(',') : [];
            ids.push(id);
            input.value = ids.join(',');
        }
    }
</script>
</body>
</html>