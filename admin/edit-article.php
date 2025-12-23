<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

if (!isset($_GET['id'])) {
    header("Location: news.php");
    exit();
}
$id = (int)$_GET['id'];

// Fetch Article Data
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) die("Article not found.");

// Fetch Selected Companies
$sel_comps = $pdo->prepare("SELECT company_id FROM article_companies WHERE article_id = ?");
$sel_comps->execute([$id]);
$selected_companies = $sel_comps->fetchAll(PDO::FETCH_COLUMN);

// Fetch Selected Events
$sel_events = $pdo->prepare("SELECT event_id FROM article_events WHERE article_id = ?");
$sel_events->execute([$id]);
$selected_events = $sel_events->fetchAll(PDO::FETCH_COLUMN);


// Fetch Selected Tags
$sel_tags = $pdo->prepare("SELECT tag_id FROM article_tags WHERE article_id = ?");
$sel_tags->execute([$id]);
$selected_tags = $sel_tags->fetchAll(PDO::FETCH_COLUMN);

// Selected Categories
$stmt_cat = $pdo->prepare("SELECT category_id FROM article_categories WHERE article_id = ?");
$stmt_cat->execute([$id]);
$selected_cats = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);

// Fetch All Options
$all_categories = $pdo->query("SELECT * FROM categories WHERE type='article' ORDER BY name ASC")->fetchAll();
$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC")->fetchAll();
$events = $pdo->query("SELECT id, title FROM events ORDER BY title ASC")->fetchAll();
$tags = $pdo->query("SELECT id, name FROM tags ORDER BY name ASC")->fetchAll();
?>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Article</h2>
            <p class="text-muted mb-0">Editing: <?= htmlspecialchars($article['title']) ?></p>
        </div>
        <a href="news.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back</a>
    </div>

    <form action="actions/update-article.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $article['id'] ?>">
        
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Title</label>
                            <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($article['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Content</label>
                            <textarea name="content" class="form-control" id="ckeditor-editor" rows="15" ><?= htmlspecialchars($article['content']) ?></textarea>
                            <!-- CKEditor 5 WYSIWYG Editor -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    ClassicEditor
                                        .create(document.querySelector('#ckeditor-editor'))
                                        .catch(error => {
                                            console.error(error);
                                        });
                                });
                            </script>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Tagged Companies</h6></div>
                            <div class="card-body">
                                <div class="border rounded p-2 bg-light" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach($companies as $company): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="companies[]" value="<?= $company['id'] ?>" id="comp_<?= $company['id'] ?>"
                                            <?= in_array($company['id'], $selected_companies) ? 'checked' : '' ?>>
                                            <label class="form-check-label small" for="comp_<?= $company['id'] ?>">
                                                <?= htmlspecialchars($company['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Tagged Events</h6></div>
                            <div class="card-body">
                                <div class="border rounded p-2 bg-light" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach($events as $event): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="events[]" value="<?= $event['id'] ?>" id="comp_<?= $event['id'] ?>"
                                            <?= in_array($event['id'], $selected_events) ? 'checked' : '' ?>>
                                            <label class="form-check-label small" for="comp_<?= $event['id'] ?>">
                                                <?= htmlspecialchars($event['title']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Publishing</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" <?= $article['is_featured'] ? 'checked' : '' ?>  id="featureCheck">
                                <label class="form-check-label" for="featureCheck">Feature</label>
                            </div>
                        </div>
                        <div class="mb-3">
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
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?= $article['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $article['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                            </select>
                        </div>
                        
                        <!-- Featured Image with Gallery Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Featured Image</label>
                            
                            <!-- Hidden input to store selected image URL -->
                            <input type="hidden" name="selected_image" id="selected_image_input">
                            
                            <!-- Image Preview Area -->
                            <div id="image_preview_container" class="mb-2 <?= empty($article['featured_image_url']) ? 'd-none' : '' ?>">
                                <img src="<?= !empty($article['featured_image_url']) ? '../' . htmlspecialchars($article['featured_image_url']) : '' ?>" id="image_preview" class="img-fluid rounded border" style="max-height: 150px;">
                                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeImage()">Remove</button>
                            </div>

                            <!-- Buttons -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#mediaGalleryModal">
                                    <i class="fas fa-images me-1"></i> Choose from Gallery
                                </button>
                                <div class="text-center text-muted small my-1">- OR -</div>
                                <input type="file" name="featured_image_upload" class="form-control form-control-sm">
                                <small class="text-muted">Upload new to replace.</small>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Update Article</button>
                    </div>
                </div>


                <!-- Topic Tags -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Article Tags (#)</h6></div>
                    <div class="card-body">
                        <input type="text" name="new_tags" class="form-control mb-2" placeholder="Add tags separated by comma (e.g. AI, Funding)">
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach($tags as $tag): ?>
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="checkbox" name="tags[]" value="<?= $tag['id'] ?>" id="tag_<?= $tag['id'] ?>"
                                    <?= in_array($tag['id'], $selected_tags) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="tag_<?= $tag['id'] ?>">#<?= htmlspecialchars($tag['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<!-- Media Gallery Modal -->
<div class="modal fade" id="mediaGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2" id="gallery_grid">
                    <?php
                    // Simple logic to scan uploads folder for images to show in gallery
                    $upload_path = '../uploads/articles/';
                    $web_path = 'uploads/articles/';
                    if(is_dir($upload_path)){
                        $files = scandir($upload_path);
                        foreach($files as $file){
                            if($file !== '.' && $file !== '..'){
                                echo '<div class="col-3">';
                                echo '<img src="../'.$web_path.$file.'" class="img-thumbnail gallery-img" style="cursor:pointer; height:100px; object-fit:cover; width:100%;" onclick="selectImage(\''.$web_path.$file.'\')">';
                                echo '</div>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function selectImage(url) {
        document.getElementById('selected_image_input').value = url;
        document.getElementById('image_preview').src = '../' + url;
        document.getElementById('image_preview_container').classList.remove('d-none');
        
        // Close modal
        var myModalEl = document.getElementById('mediaGalleryModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        modal.hide();
    }

    function removeImage() {
        document.getElementById('selected_image_input').value = '';
        document.getElementById('image_preview').src = '';
        document.getElementById('image_preview_container').classList.add('d-none');
    }
</script>
</body>
</html>