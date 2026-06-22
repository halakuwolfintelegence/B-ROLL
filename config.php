<?php
// config.php - Configuration file with database
session_start();

// Database Configuration
$DB_HOST = 'localhost';
$DB_NAME = 'video_engine';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Default API keys
$config = [
    'pexels_api_key' => 'hPfLL2XaPl3rVFEHXNaQbZstXrX1vZMSxmuvN9tqrAwbpXSZhdVL3Blm',
    'pixabay_api_key' => '56395196-037a4e0daa26799bb7627b4f3'
];

// Load saved config from file
$configFile = __DIR__ . '/config_data.json';
if (file_exists($configFile)) {
    $savedConfig = json_decode(file_get_contents($configFile), true);
    if ($savedConfig) {
        $config = array_merge($config, $savedConfig);
    }
}

// Function to save config
function saveConfig($pexelsKey, $pixabayKey) {
    global $configFile;
    $data = [
        'pexels_api_key' => $pexelsKey,
        'pixabay_api_key' => $pixabayKey
    ];
    return file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT));
}

// User Functions
function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser($username, $email, $password) {
    global $pdo;
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, credits) VALUES (?, ?, ?, 10)");
    return $stmt->execute([$username, $email, $hashed]);
}

function updateUserCredits($userId, $credits) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET credits = credits + ? WHERE id = ?");
    return $stmt->execute([$credits, $userId]);
}

function deductCredit($userId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET credits = credits - 1 WHERE id = ? AND credits > 0");
    return $stmt->execute([$userId]);
}

function getUserCredits($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT credits FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['credits'] : 0;
}
?>
