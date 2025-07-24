<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();

header('Content-Type: application/json');

$chat = require __DIR__ . '/../bootstrap.php';
$userId = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
$question = $data['question'] ?? '';

if (!$question) {
    http_response_code(400);
    echo json_encode(['error' => 'La pregunta está vacía']);
    exit;
}

// Crear nuevo chat si no hay uno activo
if (!isset($_SESSION['chat_id'])) {
    $stmt = $pdo->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
    $stmt->execute([$userId, "Nuevo Chat"]);
    $_SESSION['chat_id'] = $pdo->lastInsertId();
}

$chatId = $_SESSION['chat_id'];

// Guardar mensaje del usuario
$stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'user', ?)");
$stmt->execute([$chatId, $question]);

// Obtener respuesta del bot
$answer = $chat->getResponse($question);

// Guardar respuesta
$stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'bot', ?)");
$stmt->execute([$chatId, $answer]);

echo json_encode([
    'question' => $question,
    'answer' => $answer
]);
