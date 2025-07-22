<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();
require_once __DIR__ . '/../bootstrap.php';

$userId = $_SESSION['user_id'];

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno, fecha_nacimiento, telefono, sexo, edad, username FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono = trim($_POST['telefono']);
    $sexo = $_POST['sexo'];
    $edad = (int) $_POST['edad'];

    $stmt = $pdo->prepare("UPDATE users SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, fecha_nacimiento = ?, telefono = ?, sexo = ?, edad = ? WHERE id = ?");

    $stmt->execute([$nombre, $apellido_paterno, $apellido_materno, $fecha_nacimiento, $telefono, $sexo, $edad, $userId]);

    $mensaje = "✅ Información actualizada correctamente.";

    // Volver a consultar los datos actualizados
    $stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno, fecha_nacimiento, telefono, sexo, edad, username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}
?>

<h2> Mi perfil </h2>
<?php if (isset($mensaje)) echo "<p>$mensaje</p>"; ?>

<form method="POST">
    <label> Nombre(s): </label> <br>
    <input type="text" name="nombre" value="<?= htmlspecialchars($user['nombre'] ?? '') ?>" required> <br>

    <label> Apellido Paterno: </label> <br>
    <input type="text" name="apellido_paterno" value="<?= htmlspecialchars($user['apellido_paterno'] ?? '') ?>" required> <br>

    <label>Apellido Materno:</label><br>
    <input type="text" name="apellido_materno" value="<?= htmlspecialchars($user['apellido_materno'] ?? '') ?>" required> <br>

    <label>Fecha de Nacimiento:</label><br>
    <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($user['fecha_nacimiento'] ?? '') ?>" required> <br>

    <label> Teléfono: </label> <br>
    <input type="text" name="telefono" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>" required> <br>

    <label>Edad:</label><br>
    <input type="number" name="edad" value="<?= htmlspecialchars($user['edad'] ?? '') ?>" required> <br>

    <label> Sexo: </label> <br>
    <select name="sexo" required>
        <option value="Masculino" <?= ($user['sexo'] ?? '') === 'Masculino' ? 'selected' : '' ?>> Masculino </option>
        <option value="Femenino" <?= ($user['sexo'] ?? '') === 'Femenino' ? 'selected' : '' ?>> Femenino </option>
        <option value="Otro" <?= ($user['sexo'] ?? '') === 'Otro' ? 'selected' : '' ?>> Otro </option>
    </select> <br> <br>

    <label> Usuario: </label> <br>
    <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled> <br> <br>

    <button type="submit"> Actualizar perfil </button>
</form>

<br>
<a href=" change_password.php"> 🔒 Cambiar contraseña </a> |
<a href="index.php">⬅️ Volver al chat </a>
