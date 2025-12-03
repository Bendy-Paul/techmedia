<?php
session_start();
require '../includes/db.php';

if (isset($_GET['id']) && $_SESSION['role'] === 'admin') {
    $id = (int)$_GET['id'];
    
    // Deleting the article will automatically delete links in junction tables 
    // due to ON DELETE CASCADE in your SQL schema.
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: ../news.php?status=success");
} else {
    header("Location: ../news.php");
}
exit();
?>