<?php
// Cargar dependencias
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
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
    <title>Listado de incidencias</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .pagination a { margin: 0 5px; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Listado de incidencias</h1>

    <form method="GET">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar">
        <button type="submit">Buscar</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Creado</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($tickets as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['id']) ?></td>
            <td><?= htmlspecialchars($t['titulo']) ?></td>
            <td><?= htmlspecialchars($t['descripcion']) ?></td>
            <td><?= htmlspecialchars($t['estado']) ?></td>
            <td><?= htmlspecialchars($t['created_at']) ?></td>
            <td>
                <a href="ver_tickets.php?id=<?= urlencode($t['id']) ?>">Ver</a> |
                <a href="editar_ticket.php?id=<?= urlencode($t['id']) ?>">Editar</a> |
                <form action="borrar_ticket.php" method="post" style="display:inline" onsubmit="return confirm('¿Seguro?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
                    <button type="submit">Borrar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p == $page): ?>
                <strong><?= $p ?></strong>
            <?php else: ?>
                <a href="?q=<?= urlencode($search) ?>&page=<?= $p ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>  
</body>
</html>