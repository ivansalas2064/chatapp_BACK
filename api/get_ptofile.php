<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

$stmt = $pdo->prepare("SELECT id, nombre, apellido_paterno, apellido_materno, edad, sexo, username, fecha_nacimiento, telefono FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode($user);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
}
