<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $stmt = $pdo->prepare("SELECT id, title FROM chats WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
        $stmt->execute([$userId, "Nuevo Chat"]);
        echo json_encode(['chat_id' => $pdo->lastInsertId()]);
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $chatId = $data['id'] ?? null;

        if ($chatId) {
            $stmt = $pdo->prepare("DELETE FROM chats WHERE id = ? AND user_id = ?");
            $stmt->execute([$chatId, $userId]);
            echo json_encode(['message' => 'Chat eliminado']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falta el ID del chat']);
        }
        break;
}
