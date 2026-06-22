<?php
// config.php - JSON Storage (No Database)
session_start();

// User storage file
$usersFile = __DIR__ . '/users.json';

// Initialize users file
if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([
        'users' => [
            [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'credits' => 999,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ],
        'next_id' => 2
    ], JSON_PRETTY_PRINT));
}

function getUsers() {
    global $usersFile;
    if (!file_exists($usersFile)) return [];
    $data = json_decode(file_get_contents($usersFile), true);
    return $data['users'] ?? [];
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
    
    $user = [
        'id' => $data['next_id']++,
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'credits' => 10,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $data['users'][] = $user;
    file_put_contents($usersFile, json_encode($data, JSON_PRETTY_PRINT));
    return true;
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
    return updateUserCredits($userId, -1);
}

// API keys config
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
