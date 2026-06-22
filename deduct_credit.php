<?php
// deduct_credit.php - Deduct 1 credit from user
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? 0;

if (!$userId) {
    echo json_encode(['success' => false]);
    exit;
}

$credits = getUserCredits($userId);

if ($credits > 0) {
    deductCredit($userId);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
