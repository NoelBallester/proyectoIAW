<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/theme.php';

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
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>⚙️ Preferencias</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
<div class="container">
    <div class="header">
        <h1>⚙️ Preferencias</h1>
        <div class="nav">
            <a href="index.php">🏠 Inicio</a>
            <a href="lista_tickets.php">📋 Incidencias</a>
            <a href="editar_ticket.php">➕ Nueva</a>
        </div>
    </div>
    <div class="content wide">
        <form method="POST" class="form-wrapper" style="background:var(--color-surface); padding:30px; border-radius:var(--radius-lg);">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="tema">Selecciona tema:</label>
                <select name="tema" id="tema">
                    <option value="claro" <?= $temaActual === 'claro' ? 'selected' : '' ?>> Claro</option>
                    <option value="oscuro" <?= $temaActual === 'oscuro' ? 'selected' : '' ?>> Oscuro</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">💾 Guardar Preferencia</button>
        </form>
        <footer style="margin-top:30px;">Tema actual: <strong><?= htmlspecialchars($temaActual) ?></strong></footer>
    </div>
</div>
</body>
</html>
