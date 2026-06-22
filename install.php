<?php
// install.php - Run this once to create database and tables

$DB_HOST = 'localhost';
$DB_NAME = 'video_engine';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $DB_NAME");
    $pdo->exec("USE $DB_NAME");
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            credits INT DEFAULT 10,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create scripts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS scripts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            script_text TEXT NOT NULL,
            videos_generated INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    echo "✅ Database and tables created successfully!<br>";
    echo "🔑 Default credits: 10 per user<br>";
    echo "<a href='index.php'>Go to Main Page</a>";
    
} catch(PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
?>
