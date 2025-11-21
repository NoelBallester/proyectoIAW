<?php
// Cargar dependencias
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';
// CSRF helper
require_once __DIR__ . '/../app/csrf.php';

$pdo = getPDO();

// Parámetros
$search = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Listado de incidencias (tabla tickets)
$sql = "SELECT id, titulo, descripcion, prioridad, estado, created_at FROM tickets WHERE deleted_at IS NULL";
$params = [];
if ($search) {
    $sql .= " AND (titulo LIKE ? OR descripcion LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

// Conteo total para paginación
$countSql = "SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL";
if ($search) {
    $countSql .= " AND (titulo LIKE ? OR descripcion LIKE ?)";
}
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));
?>
<!DOCTYPE html>
<html lang="es"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Listado de incidencias</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
<div class="container">
    <div class="header">
        <h1>📋 Incidencias</h1>
        <div class="nav">
            <a href="index.php">🏠 Inicio</a>
            <a href="editar_ticket.php">➕ Nueva</a>
            <a href="preferencias.php">⚙️ Preferencias</a>
        </div>
    </div>
    <div class="content">
        <form method="GET" class="search-box" style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por título o descripción">
            <button type="submit" class="btn-primary" style="flex:0 0 auto;">🔍 Buscar</button>
            <a href="editar_ticket.php" class="btn-secondary" style="flex:0 0 auto;">➕ Crear</a>
        </form>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$tickets): ?>
                    <tr><td colspan="6" style="text-align:center; opacity:.7;">No hay incidencias</td></tr>
                <?php endif; ?>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['id']) ?></td>
                        <td><?= htmlspecialchars($t['titulo']) ?></td>
                        <td><?= htmlspecialchars(mb_strimwidth($t['descripcion'],0,80,'…')) ?></td>
                        <td class="status"><?= htmlspecialchars($t['estado']) ?></td>
                        <td><?= htmlspecialchars($t['created_at']) ?></td>
                        <td class="actions-inline">
                            <a href="ver_tickets.php?id=<?= urlencode($t['id']) ?>">Ver</a> |
                            <a href="editar_ticket.php?id=<?= urlencode($t['id']) ?>">Editar</a> |
                            <form action="borrar_ticket.php" method="post" onsubmit="return confirm('¿Seguro?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
                                <button type="submit">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p == $page): ?>
                    <strong><?= $p ?></strong>
                <?php else: ?>
                    <a href="?q=<?= urlencode($search) ?>&page=<?= $p ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <footer>Mostrando página <?= $page ?> de <?= $totalPages ?> • Total: <?= $total ?> incidencias</footer>
    </div>
</div>
</body>
</html>