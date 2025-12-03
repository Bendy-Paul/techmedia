<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $status = ($_GET['action'] == 'approve') ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE reviews SET is_approved = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        header("Location: ../reviews.php?status=success");
    } catch (PDOException $e) {
        header("Location: ../reviews.php?status=error");
    }
} else {
    header("Location: ../reviews.php");
}
exit();
?>