<?php
require 'includes/wertyuytre.php';
session_start();

// 1. Rate Limiting (Session Based)
if (!isset($_SESSION['last_submit'])) {
    $_SESSION['last_submit'] = 0;
}
$current_time = time();

// Generate Math CAPTCHA if not set
if (!isset($_SESSION['captcha_result'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_result'] = $num1 + $num2;
    $captcha_label = "$num1 + $num2 = ?";
} else {
    // We'll regenerate below if needed.
    // For simplicity in single file, let's just use what's in session or regen if empty
    // But we need the label to display
    // Let's assume user hasn't submitted yet or page reloaded
    // We can't easily reconstruct the label from just the result without storing operands
    // So let's regenerate on GET to be safe/simple
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $_SESSION['captcha_result'] = $num1 + $num2;
        $captcha_label = "$num1 + $num2 = ?";
    } else {
        $captcha_label = "Solve the math below"; // Generic label on POST error re-display
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: user-dashboard");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 2. Honeypot Check
    // If the hidden 'website' field is filled, it's a bot.
    if (!empty($_POST['website'])) {
        // Silently fail (redirect to success page or index to fool bot)
        header("Location: index.php"); 
        exit();
    }

    // 3. Rate Limit Check (e.g., 5 seconds between submissions)
    if ($current_time - $_SESSION['last_submit'] < 5) {
        $error = "You are doing that too quickly. Please wait a moment.";
    } else {
        $_SESSION['last_submit'] = $current_time;

        // 4. CAPTCHA Check
        if (!isset($_POST['captcha']) || intval($_POST['captcha']) !== $_SESSION['captcha_result']) {
            $error = "Incorrect math answer. Please try again.";
        } else {
            // Process Form
            $name = trim($_POST['full_name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $role = $_POST['role']; 

            if (empty($name) || empty($email) || empty($password)) {
                $error = "All fields are required.";
            } elseif ($password !== $confirm_password) {
                $error = "Passwords do not match.";
            } else {
                // Check email
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $error = "Email already registered.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    try {
                        // Create PENDING User
                        $status = 'pending'; // Or 'active' if you want immediate full access
                        
                        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, 0)"); // Assuming is_active column exists and 0 = pending
                        // Or if you have a 'status' column:
                        // $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)");
                        // $stmt->execute([$name, $email, $hashed_password, $role, 'pending']);
                        
                        // Let's assume standard 'is_active' based on previous context, 0 for pending email
                        $stmt->execute([$name, $email, $hashed_password, $role]);
                        $user_id = $pdo->lastInsertId();

                        // Auto-Login the user (as pending)
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_role'] = $role;
                        $_SESSION['user_status'] = 0;

                        // Send Verification Email (Simulated)
                        // In real app: send_verification_email($email, $user_id);

                        // Redirect to Dashboard (Logic there handles pending state)
                        if ($role == 'company_owner') {
                            header("Location: dashboard/index.php");
                        } else {
                            header("Location: user-dashboard"); // Or wherever users go
                        }
                        exit();

                    } catch (PDOException $e) {
                        $error = "Registration failed. Please try again.";
                    }
                }
            }
        }
    }
    
    // Regenerate CAPTCHA on POST (fail or success) to prevent replay
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_result'] = $num1 + $num2;
    $captcha_label = "$num1 + $num2 = ?";
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
        
        /* Honeypot hidden field */
        .website-field { display: none; }
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

            <form action="" method="POST">
                
                <!-- Honeypot Field (Hidden from users, visible to bots) -->
                <div class="website-field">
                    <label>If you are human, leave this field blank.</label>
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">I want to...</label>
                    <select name="role" class="form-select">
                        <option value="user">Explore Tech Companies & Jobs</option>
                        <option value="company_owner">List My Company / Post Jobs</option>
                    </select>
                </div>

                <!-- Math CAPTCHA -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Security Check: <?= $captcha_label ?></label>
                    <input type="number" name="captcha" class="form-control" placeholder="Enter result" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Sign Up</button>
                <div class="text-center small">
                    Already have an account? <a href="login.php" class="text-decoration-none">Login</a>
                </div>
            </form>
        </div>
    </div>

    <?php include_once('footer.php'); ?>
</body>
</html>