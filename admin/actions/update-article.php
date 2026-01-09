<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token()) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $status = $_POST['status'];
    $feature_status = isset($_POST['is_featured']) ? 1 : 0;

    // Handle Image Upload
    $img_sql = "";
    $params = [$title, $content, $status, $feature_status];

     // Check if user uploaded a NEW file

    if (!empty($_FILES['featured_image_upload']['name'])) {
        $upload_dir = '../../uploads/articles/';
        $file_name = time() . '_' . basename($_FILES['featured_image_upload']['name']);
        if (move_uploaded_file($_FILES['featured_image_upload']['tmp_name'], $upload_dir . $file_name)) {
            $img_sql = ", featured_image_url = ?";
            $params[] = 'uploads/articles/' . $file_name;
        }
    }
        elseif (!empty($_POST['selected_image'])) {
        $featured_image_url = $_POST['selected_image'];
            $img_sql = ", featured_image_url = ?";
            $params[] = $featured_image_url;
    }

    $params[] = $id;

    try {
        $pdo->beginTransaction();

        // 1. Update Main Article
        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, status = ?, is_featured = ? $img_sql WHERE id = ?");
        $stmt->execute($params);

        // 2. Update Companies (Reset & Re-insert)
        $pdo->prepare("DELETE FROM article_companies WHERE article_id = ?")->execute([$id]);
        if (!empty($_POST['companies'])) {
            $comp_stmt = $pdo->prepare("INSERT INTO article_companies (article_id, company_id) VALUES (?, ?)");
            foreach ($_POST['companies'] as $cid) {
                $comp_stmt->execute([$id, $cid]);
            }
        }

        // 3. Update Tags (Reset & Re-insert) - Assuming only existing tags for edit simplicity
        // You can add "create new tag on edit" logic here if needed, same as save-article.php
        $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?")->execute([$id]);
        if (!empty($_POST['tags'])) {
            $tag_stmt = $pdo->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
            foreach ($_POST['tags'] as $tid) {
                $tag_stmt->execute([$id, $tid]);
            }
        }

        $pdo->prepare("DELETE FROM article_categories WHERE article_id = ?")->execute([$id]);
        if (!empty($_POST['categories'])) {
            $cat_stmt = $pdo->prepare("INSERT INTO article_categories (article_id, category_id) VALUES (?, ?)");
            foreach ($_POST['categories'] as $cat_id) {
                $cat_stmt->execute([$id, $cat_id]);
            }
        }

        $pdo->commit();
        header("Location: ../news.php?status=success");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>