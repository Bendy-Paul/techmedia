<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $title = trim($_POST['title']);
    $desc = $_POST['description'];
    
    $comp_id = !empty($_POST['company_id']) ? $_POST['company_id'] : null;
    $comp_name = trim($_POST['company_name_manual']);
    
    // If company selected from dropdown, fetch its name
    if ($comp_id) {
        $c = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
        $c->execute([$comp_id]);
        $comp_name = $c->fetchColumn();
    }

    $type = isset($_POST['job_type']) ? $_POST['job_type'] : 'Full-time';
    $location = isset($_POST['location']) ? trim($_POST['location']) : 'Not Specified';
    $is_remote = isset($_POST['is_remote']) ? 1 : 0;
    $salary = isset($_POST['salary_range']) ? trim($_POST['salary_range']) : 'Not Specified';
    $level = isset($_POST['experience_level']) ? $_POST['experience_level'] : 'Not Specified';
    $link = trim($_POST['apply_link']);
    $skills = trim($_POST['skills']) ;
    $expiry = !empty($_POST['expires_at']) ? $_POST['expires_at'] : date('Y-m-d', strtotime('+30 days'));
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_urgent = isset($_POST['is_urgent']) ? 1 : 0;

    function create_slug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        return rtrim($string, '-');
    }
    $slug = create_slug($title . '-' . $comp_name) . '-' . rand(100,999);

    try {
        $sql = "INSERT INTO jobs (
                    company_id, company_name, title, slug, description, job_type, location, 
                    is_remote, salary_range, experience_level, apply_link, skills, 
                    expires_at, is_featured, is_urgent
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $comp_id, $comp_name, $title, $slug, $desc, $type, $location, 
            $is_remote, $salary, $level, $link, $skills, 
            $expiry, $is_featured, $is_urgent
        ]);

        header("Location: ../jobs.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>