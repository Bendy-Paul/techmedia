<?php
class ReviewManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Check if user has already reviewed this item
    public function hasReviewed($userId, $itemType, $itemId) {
        $stmt = $this->pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND item_type = ? AND item_id = ?");
        $stmt->execute([$userId, $itemType, $itemId]);
        return $stmt->fetchColumn(); // Returns ID or false
    }

    // Get all approved reviews for an item
    public function getReviews($itemType, $itemId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.full_name, u.avatar_url 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.item_type = ? AND r.item_id = ? AND r.is_approved = 1 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$itemType, $itemId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a specific review (for editing)
    public function getReview($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a review
    public function addReview($userId, $itemType, $itemId, $rating, $comment) {
        // Optional: Check if already reviewed to prevent duplicates
        if ($this->hasReviewed($userId, $itemType, $itemId)) {
            return ['status' => false, 'message' => 'You have already reviewed this item.'];
        }

        $stmt = $this->pdo->prepare("INSERT INTO reviews (user_id, item_type, item_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, ?, 1)"); 
        // Note: is_approved=1 for auto-approve, or 0 for moderation
        if ($stmt->execute([$userId, $itemType, $itemId, $rating, $comment])) {
            return ['status' => true, 'message' => 'Review submitted successfully!'];
        }
        return ['status' => false, 'message' => 'Database error.'];
    }

    // Update a review
    public function updateReview($reviewId, $userId, $rating, $comment) {
        $stmt = $this->pdo->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$rating, $comment, $reviewId, $userId])) {
            return ['status' => true, 'message' => 'Review updated.'];
        }
        return ['status' => false, 'message' => 'Update failed.'];
    }
    
    // Delete a review
    public function deleteReview($reviewId, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$reviewId, $userId])) {
            return ['status' => true, 'message' => 'Review deleted.'];
        }
        return ['status' => false, 'message' => 'Delete failed.'];
    }
    
    // Get Average Rating
    public function getAverageRating($itemType, $itemId) {
        $stmt = $this->pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE item_type = ? AND item_id = ? AND is_approved = 1");
        $stmt->execute([$itemType, $itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>