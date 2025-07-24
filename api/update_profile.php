<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);

// Validar que haya datos
if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// Construir campos actualizables
$fields = [
    'nombre',
    'apellido_paterno',
    'apellido_materno',
    'edad',
    'sexo',
    'fecha_nacimiento',
    'telefono'
];

// Construir SQL dinámico
$updates = [];
$values = [];

foreach ($fields as $field) {
    if (isset($data[$field])) {
        $updates[] = "$field = ?";
        $values[] = $data[$field];
    }
}

// Si se desea actualizar contraseña
if (!empty($data['password'])) {
    $updates[] = "password = ?";
    $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
}

if (empty($updates)) {
    http_response_code(400);
    echo json_encode(['error' => 'No se proporcionaron campos para actualizar']);
    exit;
}

$values[] = $userId;
$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($values);

echo json_encode(['message' => 'Perfil actualizado correctamente']);
