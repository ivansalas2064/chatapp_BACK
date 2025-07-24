<?php
session_start();
require_once __DIR__ . '/../bootstrap.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ? AND session_token = ?");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['session_token']]);
}

session_destroy();

header('Content-Type: application/json');
echo json_encode(['message' => 'Sesión cerrada']);
