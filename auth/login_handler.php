<?php
session_start();
require_once '../bootstrap.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
     $_SESSION['nombre'] = $user['nombre'];

    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token) VALUES (:user_id, :token)");
    $stmt->execute(['user_id' => $user['id'], 'token' => $token]);

    $_SESSION['session_token'] = $token;
    header("Location: /CHATAPP/public/index.php");
    exit;
} else {
    echo "Credenciales incorrectas.";
}
