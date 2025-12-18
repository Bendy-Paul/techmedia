<?php
require 'includes/wertyuytre.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_active'] == 0) {
                $error = "Your account has been deactivated.";
            } else {
                // Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin/index.php");
                } elseif ($user['role'] == 'company_owner') {
                    header("Location: dashboard/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tech Media Directory</title>
    <?php include_once('links.php'); ?>
    <style>
        body { background-color: #f8fafc; }
        .auth-card {
            max-width: 450px;
            margin: 80px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 40px;
            border: 1px solid rgba(37, 99, 235, 0.1);
        }
        .form-control { padding: 12px; border-radius: 8px; }
        .btn-primary { padding: 12px; font-weight: 600; border-radius: 8px; }
    </style>
</head>
<body>
    <?php include_once('header.php'); ?>

    <div class="container">
        <div class="auth-card">
            <h3 class="text-center fw-bold mb-4">Welcome Back</h3>
            
            <?php if($error): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label class="form-label small fw-bold text-muted">Password</label>
                        <a href="forgot-password.php" class="small text-decoration-none">Forgot?</a>
                    </div>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                <div class="text-center small">
                    Don't have an account? <a href="register.php" class="text-decoration-none">Register</a>
                </div>
            </form>
        </div>
    </div>

    <?php include_once('footer.php'); ?>
</body>
</html>