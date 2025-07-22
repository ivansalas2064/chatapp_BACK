<?php
// /api/routes/user.php

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

// GET /api/user - obtener perfil
if ($method === 'GET' && preg_match('#/api/user$#', $uri)) {
    $stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno, telefono, fecha_nacimiento, username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    json_response($user);
}

// PUT /api/user - actualizar perfil
if ($method === 'PUT' && preg_match('#/api/user$#', $uri)) {
    $nombre = $input['nombre'] ?? '';
    $apellido_paterno = $input['apellido_paterno'] ?? '';
    $apellido_materno = $input['apellido_materno'] ?? '';
    $telefono = $input['telefono'] ?? '';
    $fecha_nacimiento = $input['fecha_nacimiento'] ?? '';

    $stmt = $pdo->prepare("UPDATE users SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, telefono = ?, fecha_nacimiento = ? WHERE id = ?");
    $stmt->execute([$nombre, $apellido_paterno, $apellido_materno, $telefono, $fecha_nacimiento, $userId]);

    json_response(['message' => 'Perfil actualizado correctamente']);
}

json_response(['error' => 'Ruta no válida en /user'], 404);
