<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Due to foreign key constraints (ON DELETE CASCADE), 
        // deleting the company automatically removes linked categories, reviews, etc.
        $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: ../companies.php?status=success");
    } catch (PDOException $e) {
        header("Location: ../companies.php?status=error");
    }
} else {
    header("Location: ../companies.php");
}
exit();
?>