<?php
session_start();
require '../includes/db.php';

if (isset($_POST['id']) && $_SESSION['role'] === 'admin' && verify_csrf_token()) {
    $id = (int)$_POST['id'];
    
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