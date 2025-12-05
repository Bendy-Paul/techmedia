<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

// Fetch Categories & Locations for Dropdowns
$categories = $pdo->query("SELECT * FROM categories WHERE type='company' ORDER BY name ASC")->fetchAll();
$cities = $pdo->query("SELECT * FROM cities ORDER BY name ASC")->fetchAll();
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Add New Company</h2>
            <p class="text-muted mb-0">Create a new business listing manually.</p>
        </div>
        <a href="companies.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back</a>
    </div>

    <form action="actions/save-company.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Left Column: Basic Info -->
            <div class="col-md-8">
                <!-- Basic Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Company Details</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Company Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tagline (Short Bio)</label>
                            <input type="text" name="tagline" class="form-control" placeholder="e.g. Building the future of payments">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Full Description</label>
                            <textarea name="description" class="form-control" rows="6"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Contact & Metrics -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Contact & Metrics</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Website URL</label>
                            <input type="url" name="website_url" class="form-control" placeholder="https://">
                        </div>
                        
                        <hr class="text-muted">
                        
                        <!-- New Fields Added Here -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted">Year Founded</label>
                                <input type="number" name="year_founded" class="form-control" placeholder="2015">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted">Team Size</label>
                                <select name="team_size" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="1-10">1-10 Employees</option>
                                    <option value="11-50">11-50 Employees</option>
                                    <option value="51-200">51-200 Employees</option>
                                    <option value="200+">200+ Employees</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted">Starting Price (NGN)</label>
                                <input type="number" name="starting_cost" class="form-control" placeholder="500000">
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- Projects Section (New) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Portfolio Projects</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addProject()">+ Add Project</button>
                    </div>
                    <div class="card-body" id="projects_container">
                        <!-- Project Template (Hidden) -->
                        <!-- JS will insert rows here -->
                    </div>
                </div>

                <!-- Gallery Section (New) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Company Gallery</h6></div>
                    <div class="card-body">
                        <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                        <div class="form-text">Upload office photos or team pictures (Multiple allowed).</div>
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
                                        <option value="<?= $city['id'] ?>"><?= htmlspecialchars($city['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Categories</label>
                                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                    <?php foreach($categories as $cat): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>">
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
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Media & Settings -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Branding</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Logo (Square)</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Cover Image</label>
                            <input type="file" name="cover" class="form-control" accept="image/*">
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
                                <input type="text" name="social[twitter]" class="form-control" placeholder="Twitter Handle">
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                <input type="text" name="social[linkedin]" class="form-control" placeholder="LinkedIn URL">
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                <input type="text" name="social[facebook]" class="form-control" placeholder="Facebook URL">
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
                            <input type="number" name="stats[response_rate]" class="form-control form-control-sm" value="90">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-0">Completeness (%)</label>
                            <input type="number" name="stats[completeness]" class="form-control form-control-sm" value="80">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Settings</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Subscription Tier</label>
                            <select name="subscription_tier" class="form-select">
                                <option value="free">Free</option>
                                <option value="silver">Silver</option>
                                <option value="gold">Gold</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="verifyCheck">
                            <label class="form-check-label" for="verifyCheck">Verify Listing</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featureCheck">
                            <label class="form-check-label" for="featureCheck">Feature on Homepage</label>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Create Listing</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
</div> <!-- Close Wrapper -->
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let projectCount = 0;
    
    function addProject() {
        const container = document.getElementById('projects_container');
        const html = `
            <div class="border rounded p-3 mb-3 position-relative project-row" id="project_${projectCount}">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeProject(${projectCount})"></button>
                <div class="mb-2">
                    <input type="text" name="projects[${projectCount}][title]" class="form-control form-control-sm fw-bold" placeholder="Project Title">
                </div>
                <div class="mb-0">
                    <textarea name="projects[${projectCount}][description]" class="form-control form-control-sm" rows="2" placeholder="Short description..."></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        projectCount++;
    }

    function removeProject(id) {
        document.getElementById('project_' + id).remove();
    }
    
    // Add one empty project row on load
    addProject();
</script>
</body>
</html>