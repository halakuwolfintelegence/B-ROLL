<?php
// deduct_credit.php - Deduct 1 credit
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? 0;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$credits = getUserCredits($userId);

if ($credits > 0) {
    $result = deductCredit($userId);
    if ($result) {
        $newCredits = getUserCredits($userId);
        $_SESSION['user_credits'] = $newCredits;
        
        // Update cookie
        if (isset($_SESSION['user_id'])) {
            $sessionData = [
                'user_id' => $_SESSION['user_id'],
                'email' => $_SESSION['email'] ?? '',
                'username' => $_SESSION['username'] ?? '',
                'user_credits' => $newCredits
            ];
            setcookie('user_session', json_encode($sessionData), time() + (86400 * 365), '/');
        }
        
        echo json_encode(['success' => true, 'credits' => $newCredits]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to deduct credit']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Insufficient credits']);
}
?>
