<?php
require_once 'auth.php';
require_login();
require_once 'pdo.php';

$pdo = getPDO();
// Parámetros
$search = $_GET['q'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Consulta principal
$sql = "SELECT * FROM tickets WHERE deleted_at IS NULL";
$params = [];
if ($search) {
    $sql .= "AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

// Total para paginacion
$countSql = "SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL";
if ($search) {
    $countSql .= " AND (title LIKE ? OR description LIKE ?)";
}
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html> 
<head>
    <title>Listado de incidencias</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #eceb9cff; padding: 8px; text-align: left; }
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
                <th>Creado en</th>
            </tr>
            <?php foreach ($tickets as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['id']) ?></td>
                <td><?= htmlspecialchars($t['titulo']) ?></td>
                <td><?= htmlspecialchars($t['descripcion']) ?></td>
                <td><?= htmlspecialchars($t['estado']) ?></td>
                <td><?= htmlspecialchars($t['creado']) ?></td>
                      <a href="ver_ticket.php?id=<?= $t['id'] ?>">Ver</a>
                      <a href="editar_ticket.php?id=<?= $t['id'] ?>">Editar</a>
                      <a href="borrar_ticket.php?id=<?= $t['id'] ?>" onclick="return confirm('¿Estás seguro de que quieres borrar este ticket?')">Borrar</a>
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