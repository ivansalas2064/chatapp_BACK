<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
AuthMiddleware::check();
require_once __DIR__. '/../bootstrap.php';

$userId = $_SESSION['user_id'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $actual = $_POST['actual'];
    $nueva = $_POST['nueva'];
    $confirmar = $_POST['confirmar'];

    $stmt = $pdo -> prepare("SELECT password FROM users WHERE id = ?");
    $stmt -> execute([$userId]);
    $user = $stmt -> fetch();

    if (!password_verify($actual, $user['password']))
    {
        $mensaje = "❌ La contraseña actual no es correcta.";
    }
    elseif ($nueva !== $confirmar)
    {
        $mensaje = "❌ Las contraseñas nuevas no coinciden.";
    }
    else
    {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $pdo -> prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt -> execute([$hash, $userId]);
        $mensaje = "✅ Contraseña actualizada con éxito.";
    }
}
?>

<h2> Cambiar Contraseña </h2>

<?php if ($mensaje): ?>
    <p> <?= $mensaje ?> </p>
<?php endif; ?>


<form method="POST">
    <input type="password" name="actual" placeholder="Contraseña actual" required> <br>
    <input type="password" name="nueva" placeholder="Nueva contraseña" required> <br>
    <input type="password" name="confirmar" placeholder="Confirmar nueva contraseña" required> <br>
    <button type="submit">Actualizar contraseña</button>
</form>

<br>
<a href="profile.php">⬅️ Volver a mi perfil </a>