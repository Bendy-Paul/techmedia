<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ... (Sanitize inputs as before: name, tagline, etc.) ...
    $name = trim($_POST['name']);
    $tagline = trim($_POST['tagline']);
    $description = trim($_POST['description']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $website = trim($_POST['website_url']);
    $city_id = !empty($_POST['city_id']) ? $_POST['city_id'] : null;
    $address = trim($_POST['address']);
    $year_founded = !empty($_POST['year_founded']) ? (int)$_POST['year_founded'] : null;
    $team_size = trim($_POST['team_size']);
    $starting_cost = !empty($_POST['starting_cost']) ? (float)$_POST['starting_cost'] : null;
    
    $social_links = json_encode($_POST['social'] ?? []);
    $profile_stats = json_encode($_POST['stats'] ?? []);
    $tier = $_POST['subscription_tier'];
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Slug Generation
    function create_slug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        return rtrim($string, '-');
    }
    $slug = create_slug($name) . '-' . rand(100, 999);

    // File Upload Directory
    $company_upload_dir = '../../uploads/companies/';
    if (!is_dir($company_upload_dir)) mkdir($company_upload_dir, 0777, true);

    // Handle Logo & Cover (Same as before)
    $logo_url = null;
    if (!empty($_FILES['logo']['name'])) {
        $logo_name = time() . '_logo_' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $company_upload_dir . $logo_name);
        $logo_url = 'uploads/companies/' . $logo_name;
    }
    $cover_url = null;
    if (!empty($_FILES['cover']['name'])) {
        $cover_name = time() . '_cover_' . basename($_FILES['cover']['name']);
        move_uploaded_file($_FILES['cover']['tmp_name'], $company_upload_dir . $cover_name);
        $cover_url = 'uploads/companies/' . $cover_name;
    }

    try {
        $pdo->beginTransaction();

        // 1. Insert Company
        $sql = "INSERT INTO companies (
            user_id, name, slug, tagline, description, email, phone, website_url, 
            city_id, address, year_founded, team_size, starting_cost, social_links, 
            profile_stats, subscription_tier, is_verified, is_featured, logo_url, cover_image_url
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_SESSION['user_id'], $name, $slug, $tagline, $description, $email, $phone, 
            $website, $city_id, $address, $year_founded, $team_size, $starting_cost, 
            $social_links, $profile_stats, $tier, $is_verified, $is_featured, $logo_url, $cover_url
        ]);
        $company_id = $pdo->lastInsertId();

        // 2. Insert Categories
        if (!empty($_POST['categories'])) {
            $cat_stmt = $pdo->prepare("INSERT INTO company_categories (company_id, category_id) VALUES (?, ?)");
            foreach ($_POST['categories'] as $cat_id) {
                $cat_stmt->execute([$company_id, $cat_id]);
            }
        }

        // 3. Insert Company Projects (Dynamic)
        if (!empty($_POST['projects'])) {
            $proj_stmt = $pdo->prepare("INSERT INTO company_projects (company_id, title, description) VALUES (?, ?, ?)");
            foreach ($_POST['projects'] as $project) {
                if (!empty($project['title'])) {
                    $proj_stmt->execute([$company_id, $project['title'], $project['description']]);
                }
            }
        }

        // 4. Insert Company Gallery (Multiple Files)
        if (!empty($_FILES['gallery']['name'][0])) {
            $gallery_stmt = $pdo->prepare("INSERT INTO company_galleries (company_id, image_url) VALUES (?, ?)");
            foreach ($_FILES['gallery']['name'] as $key => $val) {
                $file_name = time() . '_gal_' . basename($_FILES['gallery']['name'][$key]);
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $company_upload_dir . $file_name)) {
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