<?php
require 'includes/wertyuytre.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: $url_path/");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // 'user' or 'company_owner'

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role]);
                
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } catch (PDOException $e) {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Tech Media Directory</title>
    <?php include_once('links.php'); ?>
    <style>
        body { background-color: #f8fafc; }
        .auth-card {
            max-width: 500px;
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
            <h3 class="text-center fw-bold mb-4">Create Account</h3>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">I want to...</label>
                    <select name="role" class="form-select">
                        <option value="user">Explore Tech Companies & Jobs</option>
                        <option value="company_owner">List My Company / Post Jobs</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Sign Up</button>
                <div class="text-center small">
                    Already have an account? <a href="login" class="text-decoration-none">Login</a>
                </div>
            </form>
        </div>
    </div>

    <?php include_once('footer.php'); ?>
</body>
</html>