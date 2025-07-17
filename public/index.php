<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();
$chat = require __DIR__ . '/../bootstrap.php';

$userId = $_SESSION['user_id'];
$nombre = $_SESSION['nombre'] ?? 'Usuario';

// Cambiar de chat si viene por GET
if (isset($_GET['chat_id'])) {
    $_SESSION['chat_id'] = (int) $_GET['chat_id'];
}

// Crear nuevo chat si no hay uno activo
if (!isset($_SESSION['chat_id'])) {
    $stmt = $pdo->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
    $stmt->execute([$userId, "Nuevo Chat"]);
    $_SESSION['chat_id'] = $pdo->lastInsertId();
}
$chatId = $_SESSION['chat_id'];

$question = $_POST['question'] ?? '';
$answer = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $question) {
    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'user', ?)");
    $stmt->execute([$chatId, $question]);

    $answer = $chat->getResponse($question);

    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'bot', ?)");
    $stmt->execute([$chatId, $answer]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con Ollama</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 800px;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img {
            width: 150px;
            transition: transform 0.3s;
        }

        .logo img:hover {
            transform: rotate(-5deg) scale(1.05);
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .answer-box {
            margin-top: 25px;
            padding: 15px;
            background: #f0f7f0;
            border-left: 4px solid #4CAF50;
            border-radius: 0 8px 8px 0;
        }

        .message {
            margin-bottom: 10px;
        }

        .chat-history {
            margin-top: 30px;
        }

        .chat-history h3 {
            margin-bottom: 10px;
        }

        .sidebar {
            margin-top: 30px;
        }

        .chat-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-title a {
            text-decoration: none;
            color: red;
            font-size: 14px;
        }

        .user-info {
            margin-bottom: 15px;
            text-align: right;
        }

    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <img src="https://registry.npmmirror.com/@lobehub/icons-static-png/latest/files/light/ollama.png">
    </div>

    <div class="user-info">
        <strong>Bienvenido, <?= htmlspecialchars($nombre) ?>!</strong> |
        <a href="logout.php">Cerrar sesión</a>
    </div>

    <form method="POST">
        <label for="question">Ingresa lo que deseas preguntar:</label>
        <input type="text" name="question" value="<?= htmlspecialchars($question) ?>" required>
        <input type="submit" value="Enviar">
    </form>

    <div class="chat-history">
        <h3>Conversación actual:</h3>
        <?php
        $stmt = $pdo->prepare("SELECT role, message FROM messages WHERE chat_id = ? ORDER BY created_at ASC");
        $stmt->execute([$chatId]);
        $messages = $stmt->fetchAll();

        if ($messages):
            foreach ($messages as $msg):
                $label = $msg['role'] === 'user' ? '🧑 Tú:' : '🤖 Bot:';
                echo "<div class='message'><strong>$label</strong> " . htmlspecialchars($msg['message']) . "</div>";
            endforeach;
        else:
            echo "<p>Este chat está vacío aún.</p>";
        endif;
        ?>
    </div>

    <div class="sidebar">
        <h3>Chats anteriores:</h3>
        <ul>
        <?php
            $stmt = $pdo->prepare("SELECT * FROM chats WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            while ($chatRow = $stmt->fetch()) {
                echo "<li class='chat-title'>
                        <a href='?chat_id={$chatRow['id']}'>" . htmlspecialchars($chatRow['title']) . "</a>
                        <a href='delete_chat.php?id={$chatRow['id']}' onclick='return confirm(\"¿Eliminar este chat?\")'>🗑️</a>
                      </li>";
            }
        ?>
        </ul>
    </div>
</div>
</body>
</html>
