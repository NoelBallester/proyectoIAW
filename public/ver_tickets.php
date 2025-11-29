<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: ver_tickets.php
// DESCRIPCIÓN: Vista detallada de una incidencia con navegación.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';

$pdo = getPDO();

// --- 1. OBTENER TOTAL DE TICKETS ---
// Necesitamos saber cuántos hay para mostrar "Ticket X de Y" y controlar el botón "Siguiente".
$totalStmt = $pdo->query('SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL');
$totalTickets = (int)$totalStmt->fetchColumn();

// Si no hay tickets, mostramos error y paramos.
if ($totalTickets === 0) {
    die("No hay incidencias registradas.");
}

// --- 2. GESTIÓN DE LA POSICIÓN ---
// Este script permite navegar por ID (?id=5) o por POSICIÓN (?pos=1 para el primero).
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$pos = filter_input(INPUT_GET, 'pos', FILTER_VALIDATE_INT);

// CASO A: Entramos por Posición (Navegación Anterior/Siguiente)
if ($pos !== null) {
    // Aseguramos que la posición esté dentro de los límites (entre 1 y el total)
    if ($pos < 1) $pos = 1;
    if ($pos > $totalTickets) $pos = $totalTickets;

    // Calculamos el OFFSET para SQL (La fila 1 es offset 0)
    $offset = $pos - 1;

    // Buscamos el ID del ticket que ocupa esa posición
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE deleted_at IS NULL ORDER BY id ASC LIMIT 1 OFFSET :offset');
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Guardamos el ID real para usarlo luego
    $id = (int)$ticket['id'];
    $position = $pos;

// CASO B: Entramos por ID (Desde el listado)
} elseif ($id !== null) {
    // Buscamos el ticket por su ID
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = :id AND deleted_at IS NULL');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        die("Error: La incidencia solicitada no existe.");
    }

    // Calculamos qué posición ocupa este ID en la lista total
    // (Contamos cuántos tickets hay con ID menor o igual al actual)
    $posStmt = $pdo->prepare('SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL AND id <= :id');
    $posStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $posStmt->execute();
    $position = (int)$posStmt->fetchColumn();

// CASO C: Ni ID ni Posición (Por defecto mostramos el primero)
} else {
    header('Location: ver_tickets.php?pos=1');
    exit;
}

// --- 3. PREPARAR DATOS PARA LA VISTA ---
$created = $ticket['created_at'] ?? '—';
$updated = $ticket['updated_at'] ?? null; // Puede ser null si nunca se editó

// Cálculos para la barra de navegación
$hasPrev = $position > 1;              // ¿Hay uno anterior?
$hasNext = $position < $totalTickets;  // ¿Hay uno siguiente?

$prevPos = $position - 1;
$nextPos = $position + 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Incidencia #<?= htmlspecialchars($ticket['id']) ?></title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
<div class="container">
    <div class="header">
        <h1>Incidencia #<?= htmlspecialchars($ticket['id']) ?></h1>
        <div class="nav">
            <a href="index.php">Inicio</a>
            <a href="lista_tickets.php">Listado</a>
            <a href="editar_ticket.php?id=<?= $id ?>">Editar</a>
        </div>
    </div>

    <div class="content wide">
        <div class="panel" style="padding:30px; margin-bottom:20px; text-align:left;">
            <h2 style="margin-top:0; color:var(--color-text);">
                <?= htmlspecialchars($ticket['titulo']) ?>
            </h2>
            
            <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: var(--radius-md); border-left: 4px solid var(--border-color);">
                <strong>Descripción:</strong><br>
                <p style="white-space: pre-wrap; margin-bottom:0;"><?= htmlspecialchars($ticket['descripcion']) ?></p>
            </div>

            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 0.95em;">
                <div>
                    <strong>Estado:</strong> 
                    <span class="status-<?= strtolower(str_replace(' ', '-', $ticket['estado'])) ?>">
                        <?= htmlspecialchars($ticket['estado']) ?>
                    </span>
                </div>
                <div>
                    <strong>Prioridad:</strong> 
                    <?= htmlspecialchars(ucfirst($ticket['prioridad'] ?? 'Normal')) ?>
                </div>
                <div>
                    <strong>Creado:</strong> <?= htmlspecialchars($created) ?>
                </div>
                <?php if ($updated): ?>
                <div>
                    <strong>Actualizado:</strong> <?= htmlspecialchars($updated) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap; justify-content:center; margin-bottom:30px;">
            <a href="lista_tickets.php" class="btn-secondary">Volver al listado</a>
            <a href="editar_ticket.php?id=<?= $id ?>" class="btn-primary">Editar Incidencia</a>
            
            <a href="borrar_ticket.php?id=<?= $id ?>" 
               class="btn-secondary" 
               style="background: #ffebeb; color: #dc3545; border: 1px solid #dc3545;"
               onclick="return confirm('¿Estás SEGURO de que quieres borrar esta incidencia?');">
               Borrar Incidencia
            </a>
        </div>

        <div class="pagination">
            <?php if ($hasPrev): ?>
                <a href="ver_tickets.php?pos=1" title="Ir al primero">« Primero</a>
                <a href="ver_tickets.php?pos=<?= $prevPos ?>" title="Anterior">‹ Anterior</a>
            <?php else: ?>
                <span style="opacity:0.5; cursor:not-allowed; padding:8px 14px;">« Primero</span>
                <span style="opacity:0.5; cursor:not-allowed; padding:8px 14px;">‹ Anterior</span>
            <?php endif; ?>

            <span style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
                Incidencia <strong><?= $position ?></strong> de <strong><?= $totalTickets ?></strong>
            </span>

            <?php if ($hasNext): ?>
                <a href="ver_tickets.php?pos=<?= $nextPos ?>" title="Siguiente">Siguiente ›</a>
                <a href="ver_tickets.php?pos=<?= $totalTickets ?>" title="Ir al último">Último »</a>
            <?php else: ?>
                <span style="opacity:0.5; cursor:not-allowed; padding:8px 14px;">Siguiente ›</span>
                <span style="opacity:0.5; cursor:not-allowed; padding:8px 14px;">Último »</span>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>