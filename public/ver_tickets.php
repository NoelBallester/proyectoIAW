<?php
// Cargar dependencias
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/theme.php';

$pdo = getPDO();

// Total de tickets activos
$totalStmt = $pdo->query('SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL');
$totalTickets = (int)$totalStmt->fetchColumn();

// Determinar posición y ID según parámetros: id o pos
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$pos = filter_input(INPUT_GET, 'pos', FILTER_VALIDATE_INT);

if ($id === false) { $id = null; }
if ($pos === false) { $pos = null; }

if ($id === null && ($pos === null || $pos <= 0)) {
    // Por defecto mostrar primer ticket
    $pos = 1;
}

// Si se indica pos (posición 1..total) obtener el ticket por offset ordenado por id
if ($id === null && $pos !== null) {
    if ($pos > $totalTickets) { $pos = $totalTickets; }
    $offset = $pos - 1;
    $stmtPos = $pdo->prepare('SELECT * FROM tickets WHERE deleted_at IS NULL ORDER BY id ASC LIMIT 1 OFFSET ?');
    $stmtPos->execute([$offset]);
    $ticket = $stmtPos->fetch(PDO::FETCH_ASSOC);
    if (!$ticket) {
        http_response_code(404);
        echo 'Incidencia no encontrada.';
        exit;
    }
    $id = (int)$ticket['id'];
    $position = $pos; // posición solicitada
} else {
    // Modo basado en id
    if ($id === null || $id <= 0) {
        http_response_code(400);
        echo 'ID de incidencia inválido.';
        exit;
    }
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$ticket) {
        http_response_code(404);
        echo 'Incidencia no encontrada.';
        exit;
    }
    // Calcular posición del id actual en el orden ascendente
    $posStmt = $pdo->prepare('SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL AND id <= ?');
    $posStmt->execute([$id]);
    $position = (int)$posStmt->fetchColumn();
}

$created = $ticket['created_at'] ?? '—';
$updated = $ticket['updated_at'] ?? null;

// IDs para navegación por posición (no dependen de huecos)
$hasPrev = $position > 1;
$hasNext = $position < $totalTickets;
$firstPos = 1;
$lastPos = $totalTickets;
$prevPos = $hasPrev ? $position - 1 : null;
$nextPos = $hasNext ? $position + 1 : null;
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
        <h1> Incidencia #<?= htmlspecialchars($ticket['id']) ?></h1>
        <div class="nav">
            <a href="lista_tickets.php"> Listado</a>
            <a href="editar_ticket.php?id=<?= urlencode($ticket['id']) ?>"> Editar</a>
            <a href="preferencias.php"> Preferencias</a>
            <a href="index.php"> Inicio</a>
        </div>
    </div>
    <div class="content wide">
        <div class="panel" style="padding:25px; box-shadow:none; background:var(--color-surface);">
            <p><strong>Título:</strong> <?= htmlspecialchars($ticket['titulo']) ?></p>
            <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($ticket['descripcion'])) ?></p>
            <p><strong>Estado:</strong> <span class="status"><?= htmlspecialchars($ticket['estado']) ?></span></p>
            <p><strong>Creado:</strong> <?= htmlspecialchars($created) ?><?php if (!empty($updated)): ?> • <strong>Actualizado:</strong> <?= htmlspecialchars($updated) ?><?php endif; ?></p>
        </div>
        <div style="margin-top:25px;" class="actions-inline">
            <a href="editar_ticket.php?id=<?= urlencode($ticket['id']) ?>" class="btn-secondary"> Editar</a>
            <form action="borrar_ticket.php" method="post" onsubmit="return confirm('¿Borrar definitivamente?')" style="display:inline-block; margin:0 10px;">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>">
                <button type="submit"> Borrar</button>
            </form>
            <a href="lista_tickets.php" class="btn-secondary">← Volver</a>
        </div>
        <div class="pagination" style="margin-top:35px;">
            <?php if ($hasPrev): ?>
                <a href="ver_tickets.php?pos=<?= $firstPos ?>" title="Primer"></a>
                <a href="ver_tickets.php?pos=<?= $prevPos ?>" title="Anterior"></a>
            <?php else: ?>
                <strong style="opacity:.4"></strong>
                <strong style="opacity:.4"></strong>
            <?php endif; ?>
            <strong><?= $position ?> / <?= $totalTickets ?></strong>
            <?php if ($hasNext): ?>
                <a href="ver_tickets.php?pos=<?= $nextPos ?>" title="Siguiente"></a>
                <a href="ver_tickets.php?pos=<?= $lastPos ?>" title="Último"></a>
            <?php else: ?>
                <strong style="opacity:.4"></strong>
                <strong style="opacity:.4"></strong>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
