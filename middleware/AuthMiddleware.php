<?php

class AuthMiddleware {
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once __DIR__ . '/../bootstrap.php';

        // Validar sesión con base de datos
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM sessions WHERE user_id = :user_id AND session_token = :token");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'] ?? null,
            'token' => $_SESSION['session_token'] ?? null
        ]);

        if (!$stmt->fetch()) {
            // ⚠️ En vez de redirigir, devuelve JSON
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
    }
}
