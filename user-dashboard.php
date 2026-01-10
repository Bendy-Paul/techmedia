<?php
session_start();
require 'includes/wertyuytre.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company_owner') {
    // header("Location: ../login.php");
    // exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$status = $_SESSION['user_status'];

// ... (Rest of your existing dashboard logic: fetching company etc.)
// For brevity, I'm just adding the verification block at the top of the HTML body

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Dashboard</title>
    <?php include 'links.php'; ?>
    <!-- ... styles ... -->
</head>
<body>
    <!-- ... Sidebar ... -->
    
    <div class="main-content">
        <div class="container-fluid">
            
            <?php if ($status === 0): ?>
                <div class="alert alert-warning mb-4">
                    <h4><i class="fas fa-envelope me-2"></i> Verify Your Email</h4>
                    <p>Please verify your email address to publish listings.</p>
                    
                    <!-- Sim Button -->
                    <form action="simulate-verify.php" method="POST" class="d-inline">
                        <button type="submit" class="btn btn-sm btn-outline-dark mt-2">Simulate Verification</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- ... Rest of dashboard content ... -->
            
        </div>
    </div>
    <!-- ... Scripts ... -->
</body>
</html>