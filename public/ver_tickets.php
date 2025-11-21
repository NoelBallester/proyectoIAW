<?php
// Cargar dependencias
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';

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

// Asegurar token CSRF en caso de formularios futuros
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$created = $ticket['created_at'] ?? $ticket['creado'] ?? '—';
$updated = $ticket['updated_at'] ?? $ticket['actualizado'] ?? null;
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
        <p><strong>Título:</strong> <?= htmlspecialchars($ticket['titulo'] ?? $ticket['title'] ?? '') ?></p>
        <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($ticket['descripcion'] ?? $ticket['description'] ?? '')) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($ticket['estado'] ?? $ticket['status'] ?? '') ?></p>
        <p><strong>Creado en:</strong> <?= htmlspecialchars($created) ?></p>
        <?php if (!empty($updated)): ?>
            <p><strong>Actualizado en:</strong> <?= htmlspecialchars($updated) ?></p>
        <?php endif; ?>
    </div>

    <div class="acciones">
        <a href="editar_ticket.php?id=<?= urlencode($ticket['id']) ?>">Editar</a>
        |
        <a href="borrar_ticket.php?id=<?= urlencode($ticket['id']) ?>" onclick="return confirm('¿Estás seguro de que quieres borrar este ticket?')">Borrar</a>
        |
        <a href="lista_tickets.php">Volver al listado</a>
    </div>
</body>
</html>

$pdo = getPDO();

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "ID de incidencia inválido.";
    exit;
}

$id = intval($_GET['id']);

// Buscar ticket
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    http_response_code(404);
    echo "Incidencia no encontrada.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detalle de incidencias #<?= htmlspecialchars($ticket['id']) ?></title>
</head>
<body>
    <h1>Incidencia #<?= htmlspecialchars($ticket['id']) ?></h1

    <div class="detalle">
        <p><strong>Titulo:</strong> <?= htmlspecialchars($ticket['titulo']) ?></p>
        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($ticket['descripcion'])) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($ticket['estado']) ?></p>
        <p><strong>Creado en:</strong> <?= htmlspecialchars($ticket['created_at']) ?></p>
        <?php if ($ticket['updated_at']): ?>
            <p><strong>Actualizado en:</strong> <?= htmlspecialchars($ticket['updated_at']) ?></p>
        <?php endif; ?>
    </div>

    <div class="acciones">
        <a href="editar_ticket.php?id=<?= htmlspecialchars($ticket['id']) ?>">Editar</a> |
        <a href="borrar_ticket.php?id=<?= htmlspecialchars($ticket['id']) ?>" onclick="return confirm('¿Estás seguro de que quieres borrar este ticket?')">Borrar</a>
        <a href="lista_tickets.php">Volver al listado</a>

    </div>
</body>
</html>