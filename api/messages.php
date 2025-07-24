<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];
$chatId = $_GET['chat_id'] ?? null;

if (!$chatId) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta chat_id']);
    exit;
}

// Verifica que el chat pertenezca al usuario
$stmt = $pdo->prepare("SELECT id FROM chats WHERE id = ? AND user_id = ?");
$stmt->execute([$chatId, $userId]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$stmt = $pdo->prepare("SELECT role, message FROM messages WHERE chat_id = ? ORDER BY created_at ASC");
$stmt->execute([$chatId]);

echo json_encode($stmt->fetchAll());
