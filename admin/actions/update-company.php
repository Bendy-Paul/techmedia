<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token()) {
    $id = (int)$_POST['id'];
    
    // ... (Capture standard fields like name, email, etc. same as before) ...
    // Simplified for this response to focus on new logic
    $name = trim($_POST['name']);
    $tagline = trim($_POST['tagline']);
    $description = trim($_POST['description']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $website = trim($_POST['website_url']);
    $city_id = !empty($_POST['city_id']) ? $_POST['city_id'] : null;
    $address = trim($_POST['address']);
    
    // 2. New Fields
    $year_founded = !empty($_POST['year_founded']) ? (int)$_POST['year_founded'] : null;
    $team_size = trim($_POST['team_size']);
    $starting_cost = !empty($_POST['starting_cost']) ? (float)$_POST['starting_cost'] : null;
    
    // 3. JSON Fields
    // Collecting nested arrays from the form (e.g. social[twitter])
    $social_links = json_encode($_POST['social'] ?? []);
    $profile_stats = json_encode($_POST['stats'] ?? []);

    $tier = $_POST['subscription_tier'];
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Stack IDs
    $stack_ids = isset($_POST['stack_ids']) ? json_encode($_POST['stack_ids']) : null;
    
    // File Uploads (Logo/Cover)
    $upload_dir = '../../uploads/companies/';
    $logo_sql = "";
    $cover_sql = "";
    $params = [
        $name, $tagline, $description, $email, $phone, $website, $city_id, $address,
        $year_founded, $team_size, $starting_cost, $social_links, $profile_stats,
        $tier, $is_verified, $is_featured, $stack_ids
    ];

    if (!empty($_FILES['logo']['name'])) {
        $logo_name = time() . '_logo_' . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name)) {
            $logo_sql = ", logo_url = ?";
            $params[] = 'uploads/companies/' . $logo_name;
        }
    }
    if (!empty($_FILES['cover']['name'])) {
        $cover_name = time() . '_cover_' . basename($_FILES['cover']['name']);
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $upload_dir . $cover_name)) {
            $cover_sql = ", cover_image_url = ?";
            $params[] = 'uploads/companies/' . $cover_name;
        }
    }
    $params[] = $id;

    try {
        $pdo->beginTransaction();

        // Update Core (Add other fields back in production)
        $sql = "UPDATE companies SET 
                name = ?, tagline = ?, description = ?, email = ?, phone = ?, website_url = ?, 
                city_id = ?, address = ?, year_founded = ?, team_size = ?, starting_cost = ?,
                social_links = ?, profile_stats = ?, subscription_tier = ?, is_verified = ?, is_featured = ?, stack_ids=?
                $logo_sql $cover_sql
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Handle Branches
        // 1. Add New
        if (!empty($_POST['new_branches'])) {
            $ins_br = $pdo->prepare("INSERT INTO company_locations (company_id, city_id, address) VALUES (?, ?, ?)");
            foreach ($_POST['new_branches'] as $nb) {
                if(!empty($nb['city_id'])) $ins_br->execute([$id, $nb['city_id'], $nb['address']]);
            }
        }
        // 2. Update Existing
        if (!empty($_POST['existing_branches'])) {
            $upd_br = $pdo->prepare("UPDATE company_locations SET address=? WHERE id=? AND company_id=?");
            foreach ($_POST['existing_branches'] as $eid => $eb) {
                $upd_br->execute([$eb['address'], $eid, $id]);
            }
        }
        // 3. Delete
        if (!empty($_POST['delete_branch_ids'])) {
            $del_ids = explode(',', $_POST['delete_branch_ids']);
            $del_br = $pdo->prepare("DELETE FROM company_locations WHERE id=? AND company_id=?");
            foreach ($del_ids as $did) $del_br->execute([$did, $id]);
        }
        
        // 2. Handle Projects
        // A. Update Existing
        if (!empty($_POST['existing_projects'])) {
            $upd_proj = $pdo->prepare("UPDATE company_projects SET title=?, description=? WHERE id=? AND company_id=?");
            foreach ($_POST['existing_projects'] as $proj_id => $data) {
                $upd_proj->execute([$data['title'], $data['description'], $proj_id, $id]);
            }
        }
        // B. Add New
        if (!empty($_POST['new_projects'])) {
            $ins_proj = $pdo->prepare("INSERT INTO company_projects (company_id, title, description) VALUES (?, ?, ?)");
            foreach ($_POST['new_projects'] as $new_p) {
                if(!empty($new_p['title'])) $ins_proj->execute([$id, $new_p['title'], $new_p['description']]);
            }
        }
        // C. Delete Removed
        if (!empty($_POST['delete_project_ids'])) {
            $p_ids = explode(',', $_POST['delete_project_ids']);
            $del_proj = $pdo->prepare("DELETE FROM company_projects WHERE id = ? AND company_id = ?");
            foreach ($p_ids as $pid) $del_proj->execute([$pid, $id]);
        }

        // 3. Handle Gallery
        // A. Add New Images
        if (!empty($_FILES['gallery']['name'][0])) {
            $gal_stmt = $pdo->prepare("INSERT INTO company_galleries (company_id, image_url) VALUES (?, ?)");
            foreach ($_FILES['gallery']['name'] as $key => $val) {
                $fname = time() . '_gal_' . basename($_FILES['gallery']['name'][$key]);
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $upload_dir . $fname)) {
                    $gal_stmt->execute([$id, 'uploads/companies/' . $fname]);
                }
            }
        }
        // B. Delete Images
        if (!empty($_POST['delete_gallery_ids'])) {
            $g_ids = explode(',', $_POST['delete_gallery_ids']);
            $del_gal = $pdo->prepare("DELETE FROM company_galleries WHERE id = ? AND company_id = ?");
            foreach ($g_ids as $gid) {
                // Ideally, unlink() the file here too to save space
                $del_gal->execute([$gid, $id]);
            }
        }

        // 4. Update Categories
        $pdo->prepare("DELETE FROM company_categories WHERE company_id = ?")->execute([$id]);
        if (!empty($_POST['categories'])) {
            $cat_stmt = $pdo->prepare("INSERT INTO company_categories (company_id, category_id) VALUES (?, ?)");
            foreach ($_POST['categories'] as $cat_id) {
                $cat_stmt->execute([$id, $cat_id]);
            }
        }

        $pdo->commit();
        header("Location: ../companies.php?status=success");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die($e->getMessage());
    }
}
?>