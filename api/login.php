<?php
session_start();
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $sessionToken = bin2hex(random_bytes(32)); // 🔐 Token seguro

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['session_token'] = $sessionToken;

    // Guarda token en base de datos
    $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token) VALUES (?, ?)");
    $stmt->execute([$user['id'], $sessionToken]);

    echo json_encode(['message' => 'Login exitoso']);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales incorrectas']);
}
