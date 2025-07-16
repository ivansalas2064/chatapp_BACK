<?php
require_once '../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $edad = (int)$_POST['edad'];
    $sexo = $_POST['sexo'];
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    if ($stmt->fetch()) {
        echo "El usuario ya existe.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (nombre, edad, sexo, username, password) VALUES (:nombre, :edad, :sexo, :username, :password)");
        $stmt->execute(['nombre' => $nombre, 'edad' => $edad, 'sexo' => $sexo, 'username' => $username, 'password' => $password]);
        echo "Registro exitoso. <a href='login.php'>Iniciar sesión</a>";
        exit;
    }
}
?>

<h2> Registro </h2>
<form method="POST">

    <input type="text" name="nombre" placeholder="Ingrese porfavor su nombre completo" required> <br>

    <input type="number" name="edad" placeholder="Edad" min="1" required> <br>
 
    <select name="sexo" required>
        <option value=""> Selecciona tu Sexo </option>
        <option value="Masculino"> Masculino </option>
        <option value="Femenino"> Femenino </option>
        <option value="Otro"> Otro </option>
    </select> <br> <br>

    <input type="text" name="username" placeholder="Nombre de usuario" required> <br>

    <input type="password" name="password" placeholder="Contraseña" required> <br>
    
    <button type="submit">Registrarse</button>
</form>
<a href="login.php">Ya tengo cuenta</a>
