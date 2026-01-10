<?php
// Expects $item_type and $item_id to be set before including
if (!isset($item_type) || !isset($item_id)) return;

require_once 'review-manager.php';
$reviewMgr = new ReviewManager($pdo);

// Fetch Data
$reviews = $reviewMgr->getReviews($item_type, $item_id);
$stats = $reviewMgr->getAverageRating($item_type, $item_id);
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : 0;
$has_reviewed = $is_logged_in ? $reviewMgr->hasReviewed($user_id, $item_type, $item_id) : false;

// If user has reviewed, get their review for editing
$my_review = null;
if ($has_reviewed) {
    // We fetch it from the reviews array to save a query if possible, 
    // or query specifically if pagination was used (but here we fetch all)
    foreach ($reviews as $r) {
        if ($r['user_id'] == $user_id) {
            $my_review = $r;
            break;
        }
    }
}
?>

<div class="reviews-section py-4" id="reviews">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Reviews & Ratings</h4>
            <div class="text-warning">
                <span class="fw-bold fs-5"><?= number_format($stats['avg_rating'], 1) ?></span>
                <?php for($i=1; $i<=5; $i++): ?>
                    <i class="<?= $i <= round($stats['avg_rating']) ? 'fas' : 'far' ?> fa-star"></i>
                <?php endfor; ?>
                <span class="text-muted small ms-2">(<?= $stats['count'] ?> reviews)</span>
            </div>
        </div>
        
        <?php if ($is_logged_in && !$has_reviewed): ?>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#reviewForm">
                Write a Review
            </button>
        <?php elseif (!$is_logged_in): ?>
            <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-primary">Login to Review</a>
        <?php endif; ?>
    </div>

    <!-- Review Form (Collapsible) -->
    <div class="collapse mb-4 <?= (isset($_GET['edit_review'])) ? 'show' : '' ?>" id="reviewForm">
        <div class="card bg-light border-0 p-4">
            <h5 class="mb-3"><?= $has_reviewed ? 'Edit Your Review' : 'Share your experience' ?></h5>
            <form action="<?= $url_path ?>/api/handle-review.php" method="POST">
                <input type="hidden" name="action" value="<?= $has_reviewed ? 'edit' : 'submit' ?>">
                <input type="hidden" name="item_type" value="<?= htmlspecialchars($item_type) ?>">
                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item_id) ?>">
                <?php if ($has_reviewed): ?>
                    <input type="hidden" name="review_id" value="<?= $my_review['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Rating</label>
                    <div class="rating-input">
                        <?php $current_rating = $has_reviewed ? $my_review['rating'] : 5; ?>
                        <?php for($i=1; $i<=5; $i++): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="r<?= $i ?>" value="<?= $i ?>" <?= $current_rating == $i ? 'checked' : '' ?>>
                                <label class="form-check-label" for="r<?= $i ?>"><?= $i ?> <i class="fas fa-star text-warning"></i></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Comment</label>
                    <textarea name="comment" class="form-control" rows="3" required><?= $has_reviewed ? htmlspecialchars($my_review['comment']) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-success fw-bold"><?= $has_reviewed ? 'Update Review' : 'Submit Review' ?></button>
            </form>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="reviews-list">
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
            <div class="d-flex mb-4 border-bottom pb-3">
                <div class="me-3">
                    <?php if (!empty($review['avatar_url'])): ?>
                        <img src="<?= htmlspecialchars($review['avatar_url']) ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 50px; height: 50px;">
                            <?= substr($review['full_name'], 0, 1) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($review['full_name']) ?></h6>
                            <div class="text-warning small mb-2">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                <?php endfor; ?>
                                <span class="text-muted ms-2"><?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <?php if ($is_logged_in && $review['user_id'] == $user_id): ?>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item" type="button" data-bs-toggle="collapse" data-bs-target="#reviewForm">Edit</button></li>
                                <li>
                                    <form action="api/handle-review.php" method="POST" onsubmit="return confirm('Delete this review?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                        <button class="dropdown-item text-danger">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted text-center py-4 bg-light rounded">No reviews yet. Be the first to share your experience!</p>
        <?php endif; ?>
    </div>
</div>