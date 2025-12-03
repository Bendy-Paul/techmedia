<?php
session_start();
// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Tech Media Directory</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #3b82f6;
            --dark: #1e293b;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(37, 99, 235, 0.1);
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 30px;
            text-align: center;
            color: white;
        }
        .login-body {
            padding: 40px 30px;
        }
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-primary-custom:hover {
            background: var(--secondary);
            transform: translateY(-1px);
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .brand-font { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h3 class="brand-font fw-bold mb-0"><i class="fas fa-hexagon"></i> ADMIN PANEL</h3>
            <p class="small opacity-75 mb-0">Tech Media Directory</p>
        </div>
        <div class="login-body">
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger text-center small py-2">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="actions/auth-login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" placeholder="admin@techmedia.ng" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small text-muted fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-primary-custom">
                    Sign In <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.html" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Back to Website</a>
            </div>
        </div>
    </div>

</body>
</html>