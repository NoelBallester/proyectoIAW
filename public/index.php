<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT username FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$username = $user['username'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Gestor de Incidencias</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
    <div class="container">
        <div class="header">
            <h1>🎯 Gestor de Incidencias</h1>
            <div class="user-info">👤 Bienvenido, <strong><?= htmlspecialchars($username) ?></strong></div>
            <div class="nav">
                <a href="lista_tickets.php">📋 Incidencias</a>
                <a href="editar_ticket.php">➕ Nueva</a>
                <a href="preferencias.php">⚙️ Preferencias</a>
            </div>
        </div>
        <div class="content">
            <div class="grid cards">
                <a href="lista_tickets.php" class="card primary">
                    <span class="icon">📋</span>
                    <div class="title">Listado</div>
                    <div class="desc">Ver y gestionar incidencias</div>
                </a>
                <a href="editar_ticket.php" class="card">
                    <span class="icon">➕</span>
                    <div class="title">Crear Incidencia</div>
                    <div class="desc">Registrar nueva incidencia</div>
                </a>
                <a href="preferencias.php" class="card">
                    <span class="icon">⚙️</span>
                    <div class="title">Preferencias</div>
                    <div class="desc">Elegir tema y ajustes</div>
                </a>
            </div>
            <div style="text-align:center; margin-top:30px;">
                <form action="logout.php" method="post">
                    <button type="submit" class="btn-primary">🚪 Cerrar Sesión</button>
                </form>
            </div>
            <footer>© <?= date('Y') ?> Gestor de Incidencias</footer>
        </div>
    </div>
</body>
</html>