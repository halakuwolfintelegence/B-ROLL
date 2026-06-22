<?php
// register.php - Handle registration
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields required']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

// Check if user exists
if (getUserByEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}

if (createUser($username, $email, $password)) {
    echo json_encode(['success' => true, 'message' => 'Account created!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}
?>
