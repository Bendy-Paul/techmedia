<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Basic Fields
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $status = $_POST['status'];
    $author_id = $_SESSION['user_id'];
    
    // Generate Slug
    function create_slug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        return rtrim($string, '-');
    }
    $slug = create_slug($title) . '-' . time(); // Append time to ensure uniqueness

    // 2. Handle Featured Image
    $featured_image_url = null;

    // Check if user uploaded a NEW file
    if (!empty($_FILES['featured_image_upload']['name'])) {
        $upload_dir = '../../uploads/articles/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES['featured_image_upload']['name']);
        if (move_uploaded_file($_FILES['featured_image_upload']['tmp_name'], $upload_dir . $file_name)) {
            $featured_image_url = 'uploads/articles/' . $file_name;
        }
    } 
    // If no upload, check if they selected one from gallery
    elseif (!empty($_POST['selected_image'])) {
        $featured_image_url = $_POST['selected_image'];
    }

    try {
        $pdo->beginTransaction();

        // 3. Insert Article
        $sql = "INSERT INTO articles (author_id, title, slug, content, featured_image_url, status, published_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$author_id, $title, $slug, $content, $featured_image_url, $status]);
        $article_id = $pdo->lastInsertId();

        // 4. Handle Company Tags (Junction Table)
        if (!empty($_POST['companies'])) {
            $comp_stmt = $pdo->prepare("INSERT INTO article_companies (article_id, company_id) VALUES (?, ?)");
            foreach ($_POST['companies'] as $comp_id) {
                $comp_stmt->execute([$article_id, $comp_id]);
            }
        }

        if (!empty($_POST['events'])) {
            $event_stmt = $pdo->prepare("INSERT INTO article_events (article_id, event_id) VALUES (?, ?)");
            foreach ($_POST['events'] as $eveny_id) {
                $event_stmt->execute([$article_id, $eveny_id]);
            }
        }

        // 5. Handle Topic Tags
        // A. Existing Tags
        if (!empty($_POST['tags'])) {
            $tag_stmt = $pdo->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
            foreach ($_POST['tags'] as $tag_id) {
                $tag_stmt->execute([$article_id, $tag_id]);
            }
        }

        // B. New Tags (Create on fly)
        if (!empty($_POST['new_tags'])) {
            $new_tags = explode(',', $_POST['new_tags']);
            $check_tag = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
            $insert_tag = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
            $link_tag = $pdo->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");

            foreach ($new_tags as $tag_name) {
                $tag_name = trim($tag_name);
                if(empty($tag_name)) continue;

                // Check if exists
                $check_tag->execute([$tag_name]);
                $existing = $check_tag->fetch();

                if ($existing) {
                    $tag_id = $existing['id'];
                } else {
                    $insert_tag->execute([$tag_name]);
                    $tag_id = $pdo->lastInsertId();
                }
                
                // Link to article
                try {
                    $link_tag->execute([$article_id, $tag_id]);
                } catch(PDOException $e) {
                    // Ignore duplicates if tag already linked
                }
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