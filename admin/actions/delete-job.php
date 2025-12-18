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
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../jobs.php?status=success");
    } catch (PDOException $e) {
        header("Location: ../jobs.php?status=error");
    }
} else {
    header("Location: ../jobs.php");
}
exit();
?>