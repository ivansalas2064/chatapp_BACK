<?php
require_once __DIR__ . '/../bootstrap.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Recoger los campos
$nombre = $data['nombre'] ?? '';
$apellido_paterno = $data['apellido_paterno'] ?? '';
$apellido_materno = $data['apellido_materno'] ?? '';
$edad = $data['edad'] ?? null;
$sexo = $data['sexo'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
$telefono = $data['telefono'] ?? '';

// Validar campos obligatorios
if (!$nombre || !$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Los campos nombre, username y password son obligatorios']);
    exit;
}

// Verificar si el username ya existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'El nombre de usuario ya está registrado']);
    exit;
}

// Hashear contraseña
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$stmt = $pdo->prepare("INSERT INTO users (
    nombre, apellido_paterno, apellido_materno, edad, sexo, username, password, fecha_nacimiento, telefono
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
    $nombre,
    $apellido_paterno,
    $apellido_materno,
    $edad,
    $sexo,
    $username,
    $hashedPassword,
    $fecha_nacimiento,
    $telefono
]);

echo json_encode(['message' => 'Usuario registrado exitosamente']);
