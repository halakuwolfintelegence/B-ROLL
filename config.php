<?php
// config.php - Complete with Session Fix
session_start();

// ===== SESSION FIX =====
// Check if session is empty or expired
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_session'])) {
    // Try to restore session from cookie
    $sessionData = json_decode($_COOKIE['user_session'], true);
    if ($sessionData) {
        $_SESSION['user_id'] = $sessionData['user_id'];
        $_SESSION['email'] = $sessionData['email'];
        $_SESSION['username'] = $sessionData['username'];
        $_SESSION['user_credits'] = $sessionData['user_credits'];
    }
}

// Save session to cookie on every page load
if (isset($_SESSION['user_id'])) {
    $sessionData = [
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'username' => $_SESSION['username'],
        'user_credits' => $_SESSION['user_credits']
    ];
    setcookie('user_session', json_encode($sessionData), time() + (86400 * 7), '/'); // 7 days
}

$usersFile = __DIR__ . '/users.json';

// Initialize users file if not exists
if (!file_exists($usersFile)) {
    $defaultData = [
        'users' => [
            [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'credits' => 999,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ],
        'next_id' => 2
    ];
    file_put_contents($usersFile, json_encode($defaultData, JSON_PRETTY_PRINT));
}

// ===== USER FUNCTIONS =====
function getUsers() {
    global $usersFile;
    if (!file_exists($usersFile)) return [];
    $data = json_decode(file_get_contents($usersFile), true);
    return $data['users'] ?? [];
}

function getNextId() {
    global $usersFile;
    $data = json_decode(file_get_contents($usersFile), true);
    return $data['next_id'] ?? 1;
}

function updateNextId($id) {
    global $usersFile;
    $data = json_decode(file_get_contents($usersFile), true);
    $data['next_id'] = $id;
    file_put_contents($usersFile, json_encode($data, JSON_PRETTY_PRINT));
}

function getUserByEmail($email) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}

function getUserById($id) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user;
        }
    }
    return null;
}

function createUser($username, $email, $password) {
    global $usersFile;
    $data = json_decode(file_get_contents($usersFile), true);
    
    $userId = $data['next_id']++;
    $user = [
        'id' => $userId,
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'credits' => 10,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $data['users'][] = $user;
    file_put_contents($usersFile, json_encode($data, JSON_PRETTY_PRINT));
    return $userId;
}

function updateUserCredits($userId, $credits) {
    global $usersFile;
    $data = json_decode(file_get_contents($usersFile), true);
    
    foreach ($data['users'] as &$user) {
        if ($user['id'] == $userId) {
            $user['credits'] += $credits;
            file_put_contents($usersFile, json_encode($data, JSON_PRETTY_PRINT));
            return true;
        }
    }
    return false;
}

function getUserCredits($userId) {
    $user = getUserById($userId);
    return $user ? $user['credits'] : 0;
}

function deductCredit($userId) {
    $credits = getUserCredits($userId);
    if ($credits > 0) {
        return updateUserCredits($userId, -1);
    }
    return false;
}

// ===== API CONFIG =====
$config = [
    'pexels_api_key' => 'hPfLL2XaPl3rVFEHXNaQbZstXrX1vZMSxmuvN9tqrAwbpXSZhdVL3Blm',
    'pixabay_api_key' => '56395196-037a4e0daa26799bb7627b4f3'
];

$configFile = __DIR__ . '/config_data.json';
if (file_exists($configFile)) {
    $savedConfig = json_decode(file_get_contents($configFile), true);
    if ($savedConfig) {
        $config = array_merge($config, $savedConfig);
    }
}

function saveConfig($pexelsKey, $pixabayKey) {
    global $configFile;
    $data = [
        'pexels_api_key' => $pexelsKey,
        'pixabay_api_key' => $pixabayKey
    ];
    return file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT));
}
?>
