<?php
// admin/includes/db.php

// 1. Database Credentials
// note: In a real live server, these should be in a separate config file or environment variables.
$host = 'localhost';
$db   = 'tech_media';
$user = 'root';      // Default XAMPP/WAMP user
$pass = '';          // Default XAMPP password is empty. MAMP is 'root'
$charset = 'utf8mb4'; // Supports all characters (including Emojis 🚀)

// 2. Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 3. PDO Options (The Security Layer)
$options = [
    // Throw an error exception whenever a SQL query fails (Critical for debugging)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Return rows as Associative Arrays (e.g., $row['name'] instead of $row[0])
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Turn off emulation mode for "real" prepared statements (Prevents SQL Injection)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // 4. Create the Connection
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 5. Handle Connection Errors Gracefully
    // In production, log this error to a file instead of showing it to the user
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>