<?php

class AuthMiddleware {
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once __DIR__ . '/../bootstrap.php';

        // Ahora podemos acceder a $GLOBALS['pdo']
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM sessions WHERE user_id = :user_id AND session_token = :token");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'token' => $_SESSION['session_token']
        ]);

        if (!$stmt->fetch()) {
            header("Location: /localhost/CHATAPP/public/");
            exit;
        }
    }
}
