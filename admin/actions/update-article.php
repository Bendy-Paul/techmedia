<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $status = $_POST['status'];

    // Handle Image Upload
    $img_sql = "";
    $params = [$title, $content, $status];

    if (!empty($_FILES['featured_image_upload']['name'])) {
        $upload_dir = '../../uploads/articles/';
        $file_name = time() . '_' . basename($_FILES['featured_image_upload']['name']);
        if (move_uploaded_file($_FILES['featured_image_upload']['tmp_name'], $upload_dir . $file_name)) {
            $img_sql = ", featured_image_url = ?";
            $params[] = 'uploads/articles/' . $file_name;
        }
    }

    $params[] = $id;

    try {
        $pdo->beginTransaction();

        // 1. Update Main Article
        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, status = ? $img_sql WHERE id = ?");
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

        $pdo->commit();
        header("Location: ../news.php?status=success");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>