<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Sanitize Inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // 2. Simple Validation
    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=All fields are required");
        exit();
    }

    try {
        // 3. Check Database for User
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // 4. Verify Password & Role
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // Check if user is actually an admin
            if ($user['role'] !== 'admin') {
                header("Location: ../login.php?error=Access Denied. Admins only.");
                exit();
            }

            // Check if account is active
            if ($user['is_active'] == 0) {
                header("Location: ../login.php?error=Account is deactivated.");
                exit();
            }

            // 5. Set Session Variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // 6. Redirect to Dashboard
            header("Location: ../index.php");
            exit();

        } else {
            // Invalid Credentials
            header("Location: ../login.php?error=Invalid email or password");
            exit();
        }

    } catch (PDOException $e) {
        // Log error in production, show generic message to user
        header("Location: ../login.php?error=System Error. Try again.");
        exit();
    }
} else {
    // If someone tries to access this file directly without POST
    header("Location: ../login.php");
    exit();
}