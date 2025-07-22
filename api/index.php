<?php
// /api/index.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Cambia esto por seguridad en producción
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../bootstrap.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Detectar endpoint (por ejemplo /api/auth/login)
$resource = $uri[count($uri) - 2] ?? null;
$action = $uri[count($uri) - 1] ?? null;

switch ($resource) {
    case 'auth':
        require_once __DIR__ . '/routes/auth.php';
        break;
    case 'chats':
        require_once __DIR__ . '/routes/chats.php';
        break;
    case 'user':
        require_once __DIR__ . '/routes/user.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
}
