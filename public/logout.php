<?php
session_start();
require_once '../bootstrap.php';

if (isset($_SESSION['user_id'], $_SESSION['session_token'])) {
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = :user_id AND session_token = :token");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'token' => $_SESSION['session_token']
    ]);
}

session_destroy();
header("Location: login.php");
exit;
