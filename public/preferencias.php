<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/csrf.php';

// Si el usuario envía el formulario, guardamos la preferencia en una cookie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check (se asume que app/csrf.php define check_csrf())
    check_csrf();

    $tema = $_POST['tema'] ?? 'claro';
    // validar valores aceptados
    if (!in_array($tema, ['claro', 'oscuro'], true)) {
        $tema = 'claro';
    }
    // Guardar cookie por 30 días
    setcookie('tema', $tema, time() + 3600 * 24 * 30, '/');
    header('Location: index.php');
    exit;
}

// Leer preferencia actual (si existe)
$temaActual = $_COOKIE['tema'] ?? 'claro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preferencias</title>
</head>
<body>
    <h1>Preferencias de visualización</h1>

    <form method="POST">
        <?= csrf_field() ?>
        <label for="tema">Tema:</label>
        <select name="tema" id="tema">
            <option value="claro" <?= $temaActual === 'claro' ? 'selected' : '' ?>>Claro</option>
            <option value="oscuro" <?= $temaActual === 'oscuro' ? 'selected' : '' ?>>Oscuro</option>
        </select>
        <button type="submit">Guardar</button>
    </form>

    <p><a href="index.php">Volver al inicio</a></p>
</body>
</html>