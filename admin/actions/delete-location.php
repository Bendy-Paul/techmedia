<?php
session_start();
require '../includes/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['type']) && isset($_GET['id'])) {
    
    $type = $_GET['type'];
    $id = (int)$_GET['id'];

    try {
        if ($type === 'country') {
            $stmt = $pdo->prepare("DELETE FROM countries WHERE id = ?");
        } elseif ($type === 'state') {
            $stmt = $pdo->prepare("DELETE FROM states WHERE id = ?");
        } elseif ($type === 'city') {
            $stmt = $pdo->prepare("DELETE FROM cities WHERE id = ?");
        } else {
            // Invalid type
            header("Location: ../locations.php?status=error");
            exit();
        }

        // Execute the prepared statement
        if (isset($stmt)) {
            $stmt->execute([$id]);
        }

        header("Location: ../locations.php?status=success");
        exit();

    } catch (PDOException $e) {
        header("Location: ../locations.php?status=error");
        exit();
    }

} else {
    header("Location: ../locations.php");
    exit();
}
?>