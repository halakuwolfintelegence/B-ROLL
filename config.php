<?php
// config.php - Complete Configuration with Lifetime Cookies
session_start();

// ===== SESSION/COOKIE MANAGEMENT =====
// Check if user is logged in via session
$isLoggedIn = false;
$userCredits = 0;
$username = '';
$userId = 0;

// First check session
if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? '';
    $userCredits = $_SESSION['user_credits'] ?? 0;
} 
// If session empty, check cookie (lifetime storage)
elseif (isset($_COOKIE['user_session'])) {
    $sessionData = json_decode($_COOKIE['user_session'], true);
    if ($sessionData && isset($sessionData['user_id'])) {
        // Restore session from cookie
        $_SESSION['user_id'] = $sessionData['user_id'];
        $_SESSION['email'] = $sessionData['email'];
        $_SESSION['username'] = $sessionData['username'];
        $_SESSION['user_credits'] = $sessionData['user_credits'];
        
        $isLoggedIn = true;
        $userId = $sessionData['user_id'];
        $username = $sessionData['username'];
        $userCredits = $sessionData['user_credits'];
    }
}

// If still not logged in, check remember token
if (!$isLoggedIn && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $users = getUsers();
    foreach ($users as $user) {
        if (isset($user['remember_token']) && $user['remember_token'] === $token) {
            // Auto login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_credits'] = $user['credits'];
            
            // Refresh cookie
            $sessionData = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'username' => $user['username'],
                'user_credits' => $user['credits']
            ];
            setcookie('user_session', json_encode($sessionData), time() + (86400 * 365), '/'); // 1 Year
            setcookie('remember_token', $token, time() + (86400 * 365), '/'); // 1 Year
            
            $isLoggedIn = true;
            $userId = $user['id'];
            $username = $user['username'];
            $userCredits = $user['credits'];
            break;
        }
    }
}

// Update credit display from database if needed
if ($isLoggedIn && $userId > 0) {
    $dbCredits = getUserCredits($userId);
    if ($dbCredits != $userCredits) {
        $userCredits = $dbCredits;
        $_SESSION['user_credits'] = $dbCredits;
        // Update cookie
        $sessionData = [
            'user_id' => $userId,
            'email' => $_SESSION['email'] ?? '',
            'username' => $username,
            'user_credits' => $dbCredits
        ];
        setcookie('user_session', json_encode($sessionData), time() + (86400 * 365), '/');
    }
}

// ===== JSON STORAGE =====
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

function updateRememberToken($userId, $token) {
    global $usersFile;
    $data = json_decode(file_get_contents($usersFile), true);
    
    foreach ($data['users'] as &$user) {
        if ($user['id'] == $userId) {
            $user['remember_token'] = $token;
            file_put_contents($usersFile, json_encode($data, JSON_PRETTY_PRINT));
            return true;
        }
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
