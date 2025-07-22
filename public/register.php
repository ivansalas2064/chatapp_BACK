<?php
require_once '../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono = trim($_POST['telefono']);
    $sexo = $_POST['sexo'];
    $edad = (int) $_POST['edad'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    if ($password !== $confirmar) 
    {
        echo "❌ Las contraseñas no coinciden.";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si ya existe el usuario
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    if ($stmt->fetch()) 
    {
        echo "❌ El nombre de usuario ya está registrado.";
    } 
    else 
    {
        $stmt = $pdo->prepare("INSERT INTO users (nombre, apellido_paterno, apellido_materno, fecha_nacimiento, telefono, sexo, edad, username, password) 
        VALUES (:nombre, :apellido_paterno, :apellido_materno, :fecha_nacimiento, :telefono, :sexo, :edad, :username, :password)");

        $stmt->execute([
            'nombre' => $nombre,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'fecha_nacimiento' => $fecha_nacimiento,
            'telefono' => $telefono,
            'sexo' => $sexo,
            'edad' => $edad,
            'username' => $username,
            'password' => $password_hash
        ]);

        echo "✅ Registro exitoso. <a href='login.php'>Iniciar sesión</a>";
        exit;
    }
}
?>

<h2> Registro de Usuario </h2>
<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre(s)" required> <br>
    <input type="text" name="apellido_paterno" placeholder="Apellido Paterno" required> <br>
    <input type="text" name="apellido_materno" placeholder="Apellido Materno" required> <br>

    <input type="date" name="fecha_nacimiento" required> <br>
    <input type="text" name="telefono" placeholder="Teléfono" required> <br>

    <input type="number" name="edad" placeholder="Edad" min="1" required> <br>

    <select name="sexo" required>
        <option value=""> Selecciona tu sexo </option>
        <option value="Masculino"> Masculino </option>
        <option value="Femenino"> Femenino </option>
        <option value="Otro"> Otro </option>
    </select> <br> <br>

    <input type="text" name="username" placeholder="Correo Electronico" required> <br>
    <input type="password" name="password" placeholder="Contraseña" required> <br>
    <input type="password" name="confirmar" placeholder="Confirmar contraseña" required> <br>

    <button type="submit"> Registrarme </button>
</form>

<a href="login.php"> Ya tengo cuenta </a>
