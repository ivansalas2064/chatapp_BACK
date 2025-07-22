<?php
// /api/routes/chats.php

require_once __DIR__ . '/../../bootstrap.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$input = json_decode(file_get_contents("php://input"), true);

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    json_response(['error' => 'No autorizado'], 401);
}

$userId = $_SESSION['user_id'];

// GET /api/chats - listar todos los chats del usuario
if ($method === 'GET' && preg_match('#/api/chats$#', $uri)) {
    $stmt = $pdo->prepare("SELECT id, title, created_at FROM chats WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $chats = $stmt->fetchAll();
    json_response($chats);
}

// GET /api/chats/{id} - obtener mensajes de un chat
if ($method === 'GET' && preg_match('#/api/chats/(\d+)$#', $uri, $matches)) {
    $chatId = $matches[1];
    $stmt = $pdo->prepare("SELECT message, role, created_at FROM messages WHERE chat_id = ? ORDER BY created_at ASC");
    $stmt->execute([$chatId]);
    $messages = $stmt->fetchAll();
    json_response($messages);
}

// POST /api/chats - crear nuevo chat
if ($method === 'POST' && preg_match('#/api/chats$#', $uri)) {
    $stmt = $pdo->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
    $stmt->execute([$userId, 'Nuevo Chat']);
    $chatId = $pdo->lastInsertId();
    json_response(['chat_id' => $chatId]);
}

// POST /api/chats/{id}/message - enviar pregunta y recibir respuesta
if ($method === 'POST' && preg_match('#/api/chats/(\d+)/message$#', $uri, $matches)) {
    $chatId = $matches[1];
    $question = $input['question'] ?? '';

    if (!$question) json_response(['error' => 'Pregunta vacía'], 400);

    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'user', ?)");
    $stmt->execute([$chatId, $question]);

    $answer = $chat->getResponse($question);

    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'bot', ?)");
    $stmt->execute([$chatId, $answer]);

    // Cambiar título si es "Nuevo Chat"
    $stmt = $pdo->prepare("SELECT title FROM chats WHERE id = ?");
    $stmt->execute([$chatId]);
    $tituloActual = $stmt->fetchColumn();
    if ($tituloActual === "Nuevo Chat") {
        $palabras = explode(' ', trim($question));
        $titulo = implode(' ', array_slice($palabras, 0, 6));
        $titulo .= count($palabras) > 6 ? '...' : '';
        $stmt = $pdo->prepare("UPDATE chats SET title = ? WHERE id = ?");
        $stmt->execute([$titulo, $chatId]);
    }

    json_response(['answer' => $answer]);
}

json_response(['error' => 'Ruta no válida en /chats'], 404);
