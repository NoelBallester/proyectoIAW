<?php
// Cargar dependencias
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/theme.php';

$pdo = getPDO();

// Validar ID de forma segura
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id <= 0) {
    http_response_code(400);
    echo "ID de incidencia inválido.";
    exit;
}

// Buscar ticket (ignorar tickets borrados)
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ? AND deleted_at IS NULL');
$stmt->execute([$id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    http_response_code(404);
    echo "Incidencia no encontrada.";
    exit;
}

$created = $ticket['created_at'] ?? '—';
$updated = $ticket['updated_at'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Incidencia #<?= htmlspecialchars($ticket['id']) ?> - Detalle</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
<div class="container">
    <div class="header">
        <h1>🗂️ Incidencia #<?= htmlspecialchars($ticket['id']) ?></h1>
        <div class="nav">
            <a href="lista_tickets.php">📋 Listado</a>
            <a href="editar_ticket.php?id=<?= urlencode($ticket['id']) ?>">✏️ Editar</a>
            <a href="preferencias.php">⚙️ Preferencias</a>
            <a href="index.php">🏠 Inicio</a>
        </div>
    </div>
    <div class="content">
        <div class="panel" style="padding:25px; box-shadow:none; background:var(--color-surface);">
            <p><strong>Título:</strong> <?= htmlspecialchars($ticket['titulo']) ?></p>
            <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($ticket['descripcion'])) ?></p>
            <p><strong>Estado:</strong> <span class="status"><?= htmlspecialchars($ticket['estado']) ?></span></p>
            <p><strong>Creado:</strong> <?= htmlspecialchars($created) ?><?php if (!empty($updated)): ?> • <strong>Actualizado:</strong> <?= htmlspecialchars($updated) ?><?php endif; ?></p>
        </div>
        <div style="margin-top:25px;" class="actions-inline">
            <a href="editar_ticket.php?id=<?= urlencode($ticket['id']) ?>" class="btn-secondary">✏️ Editar</a>
            <form action="borrar_ticket.php" method="post" onsubmit="return confirm('¿Borrar definitivamente?')" style="display:inline-block; margin:0 10px;">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>">
                <button type="submit">🗑️ Borrar</button>
            </form>
            <a href="lista_tickets.php" class="btn-secondary">← Volver</a>
        </div>
    </div>
</div>
</body>
</html>