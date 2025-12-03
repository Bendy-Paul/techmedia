<?php
session_start();
require '../includes/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Sanitize Input
    $name = trim($_POST['name']);
    $parent_id = trim($_POST['parent_id']);
    $icon_class = trim($_POST['icon_class']);

    // 2. Generate Slug Function
    function create_slug($string) {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9 -]/', '', $string);
        $string = str_replace(' ', '-', $string);
        return preg_replace('/-+/', '-', $string);
    }

    $slug = create_slug($name);

    // 3. Validation & Insertion
    if (!empty($name)) {
        try {
            // Check if slug exists to avoid duplicates
            $check = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $check->execute([$slug]);
            
            if ($check->rowCount() > 0) {
                // Append a random number if slug exists
                $slug .= '-' . rand(1, 99);
            }

            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon_class, parent_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $icon_class, $parent_id]);

            header("Location: ../categories.php?status=success");
            exit();

        } catch (PDOException $e) {
            header("Location: ../categories.php?status=error&msg=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: ../categories.php?status=error&msg=NameRequired");
        exit();
    }

} else {
    header("Location: ../categories.php");
    exit();
}
?>