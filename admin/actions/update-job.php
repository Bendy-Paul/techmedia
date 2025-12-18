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
    $desc = $_POST['description'];
    
    $comp_id = !empty($_POST['company_id']) ? $_POST['company_id'] : null;
    $comp_name = trim($_POST['company_name_manual']);
    
    if ($comp_id) {
        $c = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
        $c->execute([$comp_id]);
        $comp_name = $c->fetchColumn();
    }

    $type = $_POST['job_type'];
    $location = trim($_POST['location']);
    $is_remote = isset($_POST['is_remote']) ? 1 : 0;
    $salary = trim($_POST['salary_range']);
    $level = $_POST['experience_level'];
    $link = trim($_POST['apply_link']);
    $skills = trim($_POST['skills']);
    $expiry = $_POST['expires_at'];
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_urgent = isset($_POST['is_urgent']) ? 1 : 0;

    try {
        $sql = "UPDATE jobs SET 
                company_id = ?, company_name = ?, title = ?, description = ?, 
                job_type = ?, location = ?, is_remote = ?, salary_range = ?, 
                experience_level = ?, apply_link = ?, skills = ?, expires_at = ?, 
                is_featured = ?, is_urgent = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $comp_id, $comp_name, $title, $desc, 
            $type, $location, $is_remote, $salary, 
            $level, $link, $skills, $expiry, 
            $is_featured, $is_urgent, $id
        ]);

        header("Location: ../jobs.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>