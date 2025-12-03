<?php
// admin/create-admin.php
require 'includes/db.php'; // Ensure this points to your DB connection file

// 1. Define Admin Credentials
$name = "Super Admin";
$email = "admin@techmedia.ng";
$password = "admin123"; // This is the password you will use to login

// 2. Hash the password (Critical for security)
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // 3. Prepare SQL Statement
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, 1)");
    
    // 4. Execute
    $stmt->execute([$name, $email, $password_hash, 'admin']);
    
    echo "✅ Admin User Created Successfully!<br>";
    echo "Email: $email<br>";
    echo "Password: $password";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>