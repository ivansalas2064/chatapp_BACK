<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();
$chat = require __DIR__ . '/../bootstrap.php';

$userId = $_SESSION['user_id'];
$nombre = $_SESSION['nombre'] ?? 'Usuario';

// Cambiar de chat si viene por GET
if (isset($_GET['chat_id'])) 
    {
        $_SESSION['chat_id'] = (int) $_GET['chat_id'];
    }

// Crear un nuevo chat si viene ?new=1
if (isset($_GET['new']) && $_GET['new'] == '1') 
    {
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
        $stmt->execute([$userId, "Nuevo Chat"]);
        $_SESSION['chat_id'] = $pdo->lastInsertId();

        // Redirigir a index limpio
        header("Location: index.php");
        exit;
    }

// Crear nuevo chat si no hay uno activo
if (!isset($_SESSION['chat_id'])) 
    {
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
        $stmt->execute([$userId, "Nuevo Chat"]);
        $_SESSION['chat_id'] = $pdo->lastInsertId();
    }

$chatId = $_SESSION['chat_id'];

$question = $_POST['question'] ?? '';
$answer = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $question) 
    {
        $stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'user', ?)");
        $stmt->execute([$chatId, $question]);

        $answer = $chat->getResponse($question);

        $stmt = $pdo->prepare("INSERT INTO messages (chat_id, role, message) VALUES (?, 'bot', ?)");
        $stmt->execute([$chatId, $answer]);

        // Cambiar título si es nuevo chat
        $stmt = $pdo->prepare("SELECT title FROM chats WHERE id = ?");
        $stmt->execute([$chatId]);
        $tituloActual = $stmt->fetchColumn();

        if ($tituloActual === "Nuevo Chat") 
            {
                $palabras = explode(' ', trim($question));
                $tituloBreve = implode(' ', array_slice($palabras, 0, 6));
                $tituloBreve .= count($palabras) > 6 ? '...' : '';
                $stmt = $pdo->prepare("UPDATE chats SET title = ? WHERE id = ?");
                $stmt->execute([$tituloBreve, $chatId]);
            }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con Ollama</title>
    <style>
        body 
        {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .layout 
        {
            display: flex;
            width: 100%;
            max-width: 1200px;
            gap: 20px;
        }

        .sidebar 
        {
            width: 250px;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 15px;
            height: fit-content;
        }

        .sidebar h3 
        {
            margin-top: 0;
        }

        .sidebar ul 
        {
            list-style: none;
            padding-left: 0;
        }

        .sidebar li 
        {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .sidebar a 
        {
            text-decoration: none;
        }

        .chat-link 
        {
            color: #d60000;
            font-weight: bold;
            flex-grow: 1;
        }

        .delete-btn 
        {
            font-size: 14px;
            margin-left: 8px;
        }

        .chat-area 
        {
            flex: 1;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .logo 
        {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img 
        {
            width: 150px;
            transition: transform 0.3s;
        }

        .logo img:hover 
        {
            transform: rotate(-5deg) scale(1.05);
        }

        .user-info 
        {
            margin-bottom: 15px;
            text-align: right;
        }

        .chat-history 
        {
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .message 
        {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            border-radius: 8px;
        }

        .chat-product 
        {
            background: #ffffff;
            border: 1px solid #ccc;
            border-left: 5px solid #4CAF50;
            padding: 16px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }

        .chat-product h4 
        {
            margin-top: 0;
            color: #222;
        }

        .chat-product a 
        {
            color: #0070c9;
            font-weight: bold;
            text-decoration: none;
        }

        .chat-product a:hover 
        {
            text-decoration: underline;
        }

        .chat-product img 
        {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 8px;
        }

        .question-form 
        {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }

        input[type="text"] 
        {
            width: 100%;
            padding: 12px;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] 
        {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        input[type="submit"]:hover 
        {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Mis chats</h3>
        <form method="get" action="index.php" style="margin-bottom: 15px;">
    <input type="hidden" name="new" value="1">
    <button type="submit" style="
        width: 100%;
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
    ">
        ➕ Nuevo chat
    </button>
        </form>
        <ul>
            <?php
            $stmt = $pdo->prepare("SELECT c.id, c.title, COUNT(m.id) as total FROM chats c LEFT JOIN messages m ON c.id = m.chat_id WHERE c.user_id = ? GROUP BY c.id ORDER BY c.created_at DESC");
            $stmt->execute([$userId]);
            while ($chatRow = $stmt->fetch()) 
                {
                    $titulo = ($chatRow['total'] > 0) ? htmlspecialchars($chatRow['title']) : 'Nuevo Chat';
                    echo 
                    "<li>
                        <a href='?chat_id={$chatRow['id']}' class='chat-link'>$titulo</a>
                        <a href='delete_chat.php?id={$chatRow['id']}' class='delete-btn'>🗑️</a>
                    </li>";
                }
    ?>
        </ul>
    </div>

    <!-- Chat Area -->
    <div class="chat-area">
        <div class="logo">
            <img src="https://registry.npmmirror.com/@lobehub/icons-static-png/latest/files/light/ollama.png">
        </div>

        <div class="user-info">
            <strong>Bienvenido, <?= htmlspecialchars($nombre) ?>!</strong> |
            <a href="profile.php">Perfil</a> |
            <a href="logout.php">Cerrar sesión</a> |
        </div>

        <div class="chat-history">
            <h3>Conversación actual:</h3>
            <?php
            $stmt = $pdo->prepare("SELECT role, message FROM messages WHERE chat_id = ? ORDER BY created_at ASC");
            $stmt->execute([$chatId]);
            $messages = $stmt->fetchAll();

            if ($messages):
                foreach ($messages as $msg):
                    $label = $msg['role'] === 'user' ? '🧑 Tú:' : '🤖 Bot:';
                    $content = htmlspecialchars($msg['message']);
                    if ($msg['role'] === 'bot' && str_contains($msg['message'], 'amazon.')) 
                        {
                            echo "<div class='chat-product'><strong>$label</strong><br>" . nl2br($content) . "</div>";
                        } 
                        else 
                            {
                                echo "<div class='message'><strong>$label</strong><br>" . nl2br($content) . "</div>";
                        }
                        endforeach;
                        else:
                                echo "<p>Este chat está vacío aún.</p>";
                        endif; ?>
        </div>
        <form method="POST" class="question-form">
            <input type="text" name="question" placeholder="Escribe tu pregunta..." required>
            <input type="submit" value="Enviar">
        </form>
    </div>
</div>
</body>
</html>
