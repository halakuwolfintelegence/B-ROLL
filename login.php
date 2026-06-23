<?php
// login.php - Handle login with session fix
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit;
}

$user = getUserByEmail($email);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Check password (MD5 or bcrypt)
$passwordValid = false;

// Check MD5
if (md5($password) === $user['password']) {
    $passwordValid = true;
}

// Check bcrypt
if (!$passwordValid && password_verify($password, $user['password'])) {
    $passwordValid = true;
}

if ($passwordValid) {
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_credits'] = $user['credits'];
    
    // Save to cookie for persistence
    $sessionData = [
        'user_id' => $user['id'],
        'email' => $user['email'],
        'username' => $user['username'],
        'user_credits' => $user['credits']
    ];
    setcookie('user_session', json_encode($sessionData), time() + (86400 * 7), '/');
    
    echo json_encode(['success' => true, 'credits' => $user['credits']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
}
?>
