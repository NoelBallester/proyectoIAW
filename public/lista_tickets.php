<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: lista_tickets.php
// DESCRIPCIÓN: Listado principal con búsqueda, filtros y paginación.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

require_once __DIR__ . '/../app/auth.php';
require_login(); 
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';

$pdo = getPDO();

// --- 1. CONFIGURACIÓN Y PARÁMETROS ---
$search = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = in_array($p = intval($_GET['per_page'] ?? 10), [5,10,25,50]) ? $p : 10;
$offset = ($page - 1) * $perPage;
$showDeleted = isset($_GET['show_deleted']) && $_GET['show_deleted'] === '1';

// --- 2. DETECCIÓN DE COLUMNAS ---
// Verificamos columnas existentes para evitar errores si la BD cambia
$columns = $pdo->query("SHOW COLUMNS FROM tickets")->fetchAll(PDO::FETCH_COLUMN, 0);
$c = [
    'titulo'  => in_array('titulo', $columns) ? 'titulo' : 'title',
    'desc'    => in_array('descripcion', $columns) ? 'descripcion' : 'description',
    'status'  => in_array('estado', $columns) ? 'estado' : 'status',
    'updated' => in_array('updated_at', $columns) ? 'updated_at' : 'actualizado',
    'created' => in_array('created_at', $columns) ? 'created_at' : 'creado',
    'deleted' => in_array('deleted_at', $columns) ? 'deleted_at' : (in_array('eliminado', $columns) ? 'eliminado' : null)
];

// --- 3. CONSTRUCCIÓN CONSULTA SQL ---
$select = "id, {$c['titulo']} as titulo, {$c['desc']} as descripcion, {$c['status']} as estado, " .
          "COALESCE({$c['updated']}, {$c['created']}) as fecha";
if ($c['deleted']) $select .= ", {$c['deleted']} as deleted_at";

$sql = "SELECT $select FROM tickets";
$where = [];
$params = [];

// Filtros
if (!$showDeleted && $c['deleted']) {
    $where[] = "{$c['deleted']} IS NULL";
}

// Búsqueda Segura (Parámetros separados :q1, :q2 para compatibilidad total)
if ($search) {
    $where[] = "({$c['titulo']} LIKE :q1 OR {$c['desc']} LIKE :q2)";
    $params[':q1'] = "%$search%";
    $params[':q2'] = "%$search%";
}

if ($where) $sql .= " WHERE " . implode(' AND ', $where);

// Orden y Paginación
$sql .= " ORDER BY " . ($c['updated'] ?: $c['created']) . " DESC LIMIT :limit OFFSET :offset";

// --- 4. EJECUCIÓN SEGURA (PDO) ---
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- 5. TOTAL PARA PAGINACIÓN ---
$countSql = "SELECT COUNT(*) FROM tickets" . ($where ? " WHERE " . implode(' AND ', $where) : "");
$stmt = $pdo->prepare($countSql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->execute();
$totalPages = max(1, ceil($stmt->fetchColumn() / $perPage));

// Helper para URLs
function url($p) {
    return '?' . http_build_query(array_merge($_GET, ['page' => max(1, (int)$p)]));
}
?>
<!DOCTYPE html>
<html lang="es"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Incidencias</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
<div class="container">
    <div class="header">
        <h1>Incidencias</h1>
        <div class="nav">
            <a href="index.php">Inicio</a>
            <a href="editar_ticket.php">Nueva</a>
            <a href="preferencias.php">Preferencias</a>
        </div>
    </div>
    
    <div class="content wide">
        <form method="GET" class="search-box" style="display:flex; gap:10px; margin-bottom: 20px;">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar..." style="flex-grow:1;">
            <select name="per_page" onchange="this.form.submit()">
                <?php foreach ([5,10,25,50] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $opt == $perPage ? 'selected' : '' ?>><?= $opt ?>/pág</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-primary">Buscar</button>
        </form>
        
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Descripción</th>
                        <th width="120">Estado</th>
                        <th width="160">Fecha</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$tickets): ?>
                        <tr><td colspan="5" style="text-align:center; padding:20px;">Sin resultados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $t): 
                            // PREVENCIÓN DE ERRORES (Anti-Crash)
                            $id = (string)($t['id'] ?? '');
                            $titulo = (string)($t['titulo'] ?? '(Sin título)');
                            $descRaw = (string)($t['descripcion'] ?? '');
                            $estado = (string)($t['estado'] ?? 'desconocido');
                            $fecha = (string)($t['fecha'] ?? '');
                            // Cortar texto de forma segura
                            $descCorta = strlen($descRaw) > 80 ? substr($descRaw, 0, 80) . '...' : $descRaw;
                        ?>
                        <tr class="<?= !empty($t['deleted_at']) ? 'deleted-row' : '' ?>">
                            <td><?= htmlspecialchars($id) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($titulo) ?></strong><br>
                                <small style="color:#666;"><?= htmlspecialchars($descCorta) ?></small>
                            </td>
                            <td>
                                <span class="status-<?= strtolower(str_replace(' ', '-', $estado)) ?>">
                                    <?= htmlspecialchars($estado) ?>
                                </span>
                            </td>
                            <td style="font-size:0.9em;"><?= htmlspecialchars($fecha) ?></td>
                            <td>
                                <a href="editar_ticket.php?id=<?= $id ?>" class="btn-secondary" style="padding:4px 10px; font-size:0.8em;">Editar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= url($page - 1) ?>" class="btn-secondary">«</a>
            <?php endif; ?>
            <span style="padding:8px;">Página <?= $page ?> de <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="<?= url($page + 1) ?>" class="btn-secondary">»</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>