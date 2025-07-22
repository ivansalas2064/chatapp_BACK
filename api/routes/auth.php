<?php
require_once __DIR__ . '/../../bootstrap.php';
session_start();

// Leer datos JSON desde el cuerpo de la petición
$data = json_decode(file_get_contents("php://input"), true);

// Ruta y método actual
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Función de respuesta JSON
function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// LOGIN
if (strpos($uri, '/api/auth/login') !== false && $method === 'POST') {
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Generar token
        $token = bin2hex(random_bytes(32));

        // Guardar en base de datos
        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token) VALUES (?, ?)");
        $stmt->execute([$user['id'], $token]);

        // Guardar en sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['session_token'] = $token;

        json_response([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'username' => $user['username']
            ]
        ]);
    } else {
        json_response(['error' => 'Credenciales incorrectas'], 401);
    }
}

// REGISTRO
if (strpos($uri, '/api/auth/register') !== false && $method === 'POST') {
    $nombre            = $data['nombre'] ?? '';
    $apellido_paterno  = $data['apellido_paterno'] ?? '';
    $apellido_materno  = $data['apellido_materno'] ?? '';
    $telefono          = $data['telefono'] ?? '';
    $fecha_nacimiento  = $data['fecha_nacimiento'] ?? '';
    $username          = $data['username'] ?? '';
    $password          = $data['password'] ?? '';
    $confirmar         = $data['confirmar'] ?? '';

    if ($password !== $confirmar) {
        json_response(['error' => 'Las contraseñas no coinciden'], 400);
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        json_response(['error' => 'Este usuario ya existe'], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (nombre, apellido_paterno, apellido_materno, telefono, fecha_nacimiento, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido_paterno, $apellido_materno, $telefono, $fecha_nacimiento, $username, $hash]);

    json_response(['message' => 'Usuario creado correctamente 🎉']);
}

// Si no coincide ninguna ruta
json_response(['error' => 'Ruta no válida'], 404);
