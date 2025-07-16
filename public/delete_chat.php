<?php
session_start();
require_once '../bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$chatId = $_GET['id'] ?? null;
if ($chatId) {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE chat_id = ?");
    $stmt->execute([$chatId]);

    $stmt = $pdo->prepare("DELETE FROM chats WHERE id = ? AND user_id = ?");
    $stmt->execute([$chatId, $_SESSION['user_id']]);
}

header("Location: index.php");
exit;
