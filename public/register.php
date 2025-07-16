<?php
require_once '../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    if ($stmt->fetch()) {
        echo "El usuario ya existe.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute(['username' => $username, 'password' => $password]);
        echo "Registro exitoso. <a href='login.php'>Iniciar sesión</a>";
        exit;
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Nombre de usuario" required><br>
    <input type="password" name="password" placeholder="Contraseña" required><br>
    <button type="submit">Registrarse</button>
</form>
<a href="login.php">Ya tengo cuenta</a>
