<?php
session_start();
require '../includes/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    
    $id = (int)$_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: ../categories.php?status=success");
        exit();

    } catch (PDOException $e) {
        header("Location: ../categories.php?status=error");
        exit();
    }

} else {
    header("Location: ../categories.php");
    exit();
}
?>