<?php
// login.php - Handle login with lifetime cookies
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$remember = $data['remember'] ?? true;

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

if (md5($password) === $user['password']) {
    $passwordValid = true;
}

if (!$passwordValid && password_verify($password, $user['password'])) {
    $passwordValid = true;
}

if ($passwordValid) {
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_credits'] = $user['credits'];
    
    // Set lifetime cookie (1 year)
    $sessionData = [
        'user_id' => $user['id'],
        'email' => $user['email'],
        'username' => $user['username'],
        'user_credits' => $user['credits']
    ];
    
    $expiry = $remember ? time() + (86400 * 365) : time() + (86400 * 7);
    setcookie('user_session', json_encode($sessionData), $expiry, '/');
    
    // Generate remember token
    $token = bin2hex(random_bytes(32));
    setcookie('remember_token', $token, $expiry, '/');
    updateRememberToken($user['id'], $token);
    
    echo json_encode(['success' => true, 'credits' => $user['credits']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
}
?>
