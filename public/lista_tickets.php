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
// Selector de tamaño de página (permitidos)
$allowedPerPage = [5,10,25,50];
$perPage = intval($_GET['per_page'] ?? 10);
if (!in_array($perPage, $allowedPerPage, true)) { $perPage = 10; }
$showDeleted = isset($_GET['show_deleted']) && $_GET['show_deleted'] === '1';
$offset = ($page - 1) * $perPage;

// Listado de incidencias (tabla tickets)
$sql = "SELECT id, titulo, descripcion, prioridad, estado, created_at, deleted_at FROM tickets";
$where = [];
if (!$showDeleted) { $where[] = "deleted_at IS NULL"; }
if ($search) { $where[] = "(titulo LIKE ? OR descripcion LIKE ?)"; }
if ($where) { $sql .= " WHERE " . implode(' AND ', $where); }
// Parámetros
$params = [];
if ($search) {
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

// Conteo total para paginación
// Conteo total para paginación (aplica mismos filtros)
$countSql = "SELECT COUNT(*) FROM tickets";
$countWhere = [];
if (!$showDeleted) { $countWhere[] = "deleted_at IS NULL"; }
if ($search) { $countWhere[] = "(titulo LIKE ? OR descripcion LIKE ?)"; }
if ($countWhere) { $countSql .= " WHERE " . implode(' AND ', $countWhere); }
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
        <h1> Incidencias</h1>
        <div class="nav">
            <a href="index.php"> Inicio</a>
            <a href="editar_ticket.php"> Nueva</a>
            <a href="preferencias.php"> Preferencias</a>
        </div>
    </div>
    <div class="content wide">
        <form method="GET" class="search-box" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por título o descripción">
            <select name="per_page" style="flex:0 0 auto;">
                <?php foreach ($allowedPerPage as $opt): ?>
                    <option value="<?= $opt ?>" <?= $opt === $perPage ? 'selected' : '' ?>><?= $opt ?>/página</option>
                <?php endforeach; ?>
            </select>
            <label style="display:flex; gap:4px; align-items:center; font-size:.85rem;">
                <input type="checkbox" name="show_deleted" value="1" <?= $showDeleted ? 'checked' : '' ?>> Mostrar borrados
            </label>
            <button type="submit" class="btn-primary" style="flex:0 0 auto;"> Buscar</button>
            <a href="editar_ticket.php" class="btn-secondary" style="flex:0 0 auto;"> Crear</a>
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
                        <td class="status">
                            <?= htmlspecialchars($t['estado']) ?>
                            <?php if ($t['deleted_at']): ?>
                                <span style="display:inline-block; padding:2px 6px; border-radius:8px; background:#cc3333; color:#fff; font-size:.65rem; margin-left:4px;">BORRADO</span>
                            <?php endif; ?>
                        </td>
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
        <?php
        // Helper para construir URL manteniendo filtros
        function build_list_url($pageNumber) {
            $params = [
                'q' => $_GET['q'] ?? '',
                'page' => $pageNumber,
                'per_page' => $_GET['per_page'] ?? '',
            ];
            if (isset($_GET['show_deleted'])) { $params['show_deleted'] = '1'; }
            return '?' . http_build_query($params);
        }
        ?>
        <div class="pagination" style="display:flex; flex-wrap:wrap; gap:6px; align-items:center;">
            <?php if ($page > 1): ?>
                <a href="<?= build_list_url(1) ?>"></a>
                <a href="<?= build_list_url($page - 1) ?>"></a>
            <?php endif; ?>
            <?php
            // Mostrar rango compacto (máx 9 páginas visibles)
            $window = 4; // páginas a cada lado
            $start = max(1, $page - $window);
            $end = min($totalPages, $page + $window);
            if ($start > 1) {
                echo '<a href="' . build_list_url(1) . '">1</a>';
                if ($start > 2) { echo '<span style="opacity:.6;">…</span>'; }
            }
            for ($p = $start; $p <= $end; $p++) {
                if ($p == $page) {
                    echo '<strong>' . $p . '</strong>';
                } else {
                    echo '<a href="' . build_list_url($p) . '">' . $p . '</a>';
                }
            }
            if ($end < $totalPages) {
                if ($end < $totalPages - 1) { echo '<span style="opacity:.6;">…</span>'; }
                echo '<a href="' . build_list_url($totalPages) . '">' . $totalPages . '</a>';
            }
            ?>
            <?php if ($page < $totalPages): ?>
                <a href="<?= build_list_url($page + 1) ?>"></a>
                <a href="<?= build_list_url($totalPages) ?>"></a>
            <?php endif; ?>
        </div>
        <footer>
            Mostrando página <?= $page ?> de <?= $totalPages ?> • Total: <?= $total ?> incidencias
            • Tamaño página: <?= $perPage ?>
            <?php if ($total <= 1): ?>
                <div style="margin-top:6px; font-size:.8rem; opacity:.75;">(Crea más incidencias para ver la paginación en acción)</div>
            <?php endif; ?>
            <?php if ($showDeleted): ?>
                <div style="margin-top:4px; font-size:.7rem; color:#c33;">Mostrando también incidencias borradas (soft delete).</div>
            <?php endif; ?>
        </footer>
    </div>
</div>
</body>
</html>
