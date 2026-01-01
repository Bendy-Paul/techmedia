<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (Basic fields collection similar to before) ...
    $name = trim($_POST['name']);
    $tagline = trim($_POST['tagline']);
    $description = trim($_POST['description']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $website = trim($_POST['website_url']);
    $city_id = !empty($_POST['city_id']) ? $_POST['city_id'] : null;
    $address = trim($_POST['address']);
    $year = !empty($_POST['year_founded']) ? (int)$_POST['year_founded'] : null;
    $size = trim($_POST['team_size']);
    $cost = !empty($_POST['starting_cost']) ? (float)$_POST['starting_cost'] : null;
    
    $social_links = json_encode($_POST['social'] ?? []);
    $profile_stats = json_encode($_POST['stats'] ?? []);
    
    $tier = $_POST['subscription_tier'];
    $verified = isset($_POST['is_verified']) ? 1 : 0;
    $featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle Stacks (JSON)
    $stack_ids = isset($_POST['stack_ids']) ? json_encode($_POST['stack_ids']) : null;

    // Slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . rand(100,999);

    // Files
    $upload_dir = '../../uploads/companies/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $logo_url = null;
    if (!empty($_FILES['logo']['name'])) {
        $fname = time() . '_L_' . basename($_FILES['logo']['name']);
        if(move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $fname)) $logo_url = 'uploads/companies/' . $fname;
    }
    $cover_url = null;
    if (!empty($_FILES['cover']['name'])) {
        $fname = time() . '_C_' . basename($_FILES['cover']['name']);
        if(move_uploaded_file($_FILES['cover']['tmp_name'], $upload_dir . $fname)) $cover_url = 'uploads/companies/' . $fname;
    }

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO companies (
            user_id, name, slug, tagline, description, email, phone, website_url, 
            city_id, address, year_founded, team_size, starting_cost, social_links, 
            profile_stats, subscription_tier, is_verified, is_featured, logo_url, cover_image_url, stack_ids
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_SESSION['user_id'], $name, $slug, $tagline, $description, $email, $phone, 
            $website, $city_id, $address, $year, $size, $cost, $social_links, 
            $profile_stats, $tier, $verified, $featured, $logo_url, $cover_url, $stack_ids
        ]);
        $company_id = $pdo->lastInsertId();

        // 2. Insert Categories
        if (!empty($_POST['categories'])) {
            $cat_stmt = $pdo->prepare("INSERT INTO company_categories (company_id, category_id) VALUES (?, ?)");
            foreach ($_POST['categories'] as $cat_id) {
                $cat_stmt->execute([$company_id, $cat_id]);
            }
        }

        // Save Projects
        if (!empty($_POST['projects'])) {
            $p_stmt = $pdo->prepare("INSERT INTO company_projects (company_id, title, description) VALUES (?, ?, ?)");
            foreach ($_POST['projects'] as $p) {
                if(!empty($p['title'])) $p_stmt->execute([$company_id, $p['title'], $p['description']]);
            }
        }

        // Save Branches (Locations)
        if (!empty($_POST['branches'])) {
            $b_stmt = $pdo->prepare("INSERT INTO company_locations (company_id, city_id, address) VALUES (?, ?, ?)");
            foreach ($_POST['branches'] as $b) {
                if(!empty($b['city_id'])) $b_stmt->execute([$company_id, $b['city_id'], $b['address']]);
            }
        }
        
        // 4. Insert Company Gallery (Multiple Files)
        if (!empty($_FILES['gallery']['name'][0])) {
            $gallery_stmt = $pdo->prepare("INSERT INTO company_galleries (company_id, image_url) VALUES (?, ?)");
            foreach ($_FILES['gallery']['name'] as $key => $val) {
                $file_name = time() . '_gal_' . basename($_FILES['gallery']['name'][$key]);
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $upload_dir . $file_name)) {
                    $gallery_stmt->execute([$company_id, 'uploads/companies/' . $file_name]);
                }
            }
        }

        $pdo->commit();
        header("Location: ../companies.php?status=success");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>