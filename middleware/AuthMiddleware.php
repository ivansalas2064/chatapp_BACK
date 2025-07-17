<?php

class AuthMiddleware {
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once __DIR__ . '/../bootstrap.php';

        // Validar sesión con la base de datos
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM sessions WHERE user_id = :user_id AND session_token = :token");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'] ?? null,
            'token' => $_SESSION['session_token'] ?? null
        ]);

        // Si no es válida, redirigir a login
        if (!$stmt->fetch()) {
            header("Location: /CHATAPP/public/login.php");
            exit;
        }
    }
}
