<?php
session_start();
require 'includes/wertyuytre.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Update user status to active (1)
    $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
    $stmt->execute([$user_id]);
    
    // Update session
    $_SESSION['user_status'] = 'active';
    
    // Redirect back to dashboard
    if ($_SESSION['user_role'] == 'company_owner') {
        header("Location: dashboard/index.php");
    } else {
        header("Location: user-dashboard.php");
    }
    exit();

} catch (PDOException $e) {
    die("Error verifying user.");
}
?>