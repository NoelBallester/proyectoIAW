<?php
// Cargar dependencias
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/csrf.php';

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
    <title>Detalle de incidencia #<?= htmlspecialchars($ticket['id']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>body{font-family:Arial,Helvetica,sans-serif;line-height:1.4} .detalle{max-width:800px}</style>
</head>
<body>
    <h1>Incidencia #<?= htmlspecialchars($ticket['id']) ?></h1>

    <div class="detalle">
    <p><strong>Título:</strong> <?= htmlspecialchars($ticket['titulo']) ?></p>
    <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($ticket['descripcion'])) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($ticket['estado']) ?></p>
        <p><strong>Creado en:</strong> <?= htmlspecialchars($created) ?></p>
        <?php if (!empty($updated)): ?>
            <p><strong>Actualizado en:</strong> <?= htmlspecialchars($updated) ?></p>
        <?php endif; ?>
    </div>

    <div class="acciones">
    <a href="editar_ticket.php?id=<?= urlencode($ticket['id']) ?>">Editar</a>
        |
        <form action="borrar_ticket.php" method="post" style="display:inline" onsubmit="return confirm('¿Estás seguro de que quieres borrar este ticket?')">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>">
            <button type="submit">Borrar</button>
        </form>
        |
        <a href="lista_tickets.php">Volver al listado</a>
    </div>
</body>
</html>