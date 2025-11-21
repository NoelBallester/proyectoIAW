<?php
// Cargar dependencias
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
// CSRF helper (se carga más abajo si existe)
if (file_exists(__DIR__ . '/../app/csrf.php')) {
    require_once __DIR__ . '/../app/csrf.php';
}

$pdo = getPDO();

// Parámetros
$search = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// La base de datos actual tiene tabla 'items' (según schema.sql). Adaptamos listado.
// Campos: id, nombre, categoria, ubicacion, stock, created_at
$itemsSql = "SELECT * FROM items";
$params = [];
if ($search) {
    $itemsSql .= " WHERE (nombre LIKE ? OR categoria LIKE ? OR ubicacion LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$itemsSql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($itemsSql);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Conteo total para paginación
$countSql = "SELECT COUNT(*) FROM items";
if ($search) {
    $countSql .= " WHERE (nombre LIKE ? OR categoria LIKE ? OR ubicacion LIKE ?)";
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
    <h1>Listado de ítems</h1>

    <form method="GET">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar">
        <button type="submit">Buscar</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Ubicación</th>
            <th>Stock</th>
            <th>Creado</th>
        </tr>
        <?php foreach ($items as $it): ?>
        <tr>
            <td><?= htmlspecialchars($it['id']) ?></td>
            <td><?= htmlspecialchars($it['nombre']) ?></td>
            <td><?= htmlspecialchars($it['categoria']) ?></td>
            <td><?= htmlspecialchars($it['ubicacion']) ?></td>
            <td><?= htmlspecialchars($it['stock']) ?></td>
            <td><?= htmlspecialchars($it['created_at']) ?></td>
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