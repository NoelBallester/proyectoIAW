<?php
require_once __DIR__ . '/../app/auth.php';
require_login();

// Si quieres mostrar el nombre del usuario:
require_once __DIR__ . '/../app/pdo.php';
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
    <title>Panel principal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        nav a, nav form { margin-right: 1em; display:inline-block; }
        button { padding: 0.5em 1em; }
    </style>
</head>
<body>
    <h1>Bienvenido al gestor de incidencias</h1>
    <p>Has iniciado sesión como <strong><?= htmlspecialchars($username) ?></strong></p>

    <nav>
        <a href="lista_tickets.php">📋 Listado de incidencias</a>
        <a href="editar_ticket.php">➕ Crear incidencia</a>
        <a href="borrar_ticket.php">🗑️ Borrar incidencia</a>
        <a href="preferencias.php">⚙️ Preferencias</a>
        <form action="logout.php" method="post" style="display:inline">
            <button type="submit">🚪 Cerrar sesión</button>
        </form>
    </nav>
</body>
</html>