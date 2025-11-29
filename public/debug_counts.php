<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';

$pdo = getPDO();

// Capturar errores de forma simple
$err = null;
try {
    $total = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
    $activos = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL")->fetchColumn();
    $borrados = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE deleted_at IS NOT NULL")->fetchColumn();

    // Traer algunas filas para muestreo
    $sample = $pdo->query("SELECT id, titulo, deleted_at FROM tickets ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

    // Detectar si existen columnas de la segunda definición (title/description)
    $columns = $pdo->query("SHOW COLUMNS FROM tickets")->fetchAll(PDO::FETCH_COLUMN, 0);
    $tieneDuplicado = in_array('title', $columns, true) || in_array('eliminado', $columns, true);
} catch (Exception $e) {
    $err = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Debug counts</title>
<?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
<div class="container">
  <div class="header">
    <h1> Debug Tickets</h1>
    <div class="nav">
      <a href="lista_tickets.php"> Listado</a>
      <a href="index.php"> Inicio</a>
    </div>
  </div>
  <div class="content wide">
    <?php if ($err): ?>
      <div class="error-box">Error: <?= htmlspecialchars($err) ?></div>
    <?php else: ?>
      <p><strong>Total filas:</strong> <?= $total ?></p>
      <p><strong>Activos (deleted_at IS NULL):</strong> <?= $activos ?></p>
      <p><strong>Borrados (deleted_at IS NOT NULL):</strong> <?= $borrados ?></p>
      <?php if ($tieneDuplicado): ?>
        <div class="error-box" style="background:#ffd9a6; color:#663a00;"> Se detectan columnas de una segunda definición de la tabla (title/eliminado). Revisa tu schema para no mezclar estructuras.</div>
      <?php endif; ?>
      <h2>Muestra últimas 10</h2>
      <table class="table">
        <thead><tr><th>ID</th><th>Título</th><th>deleted_at</th></tr></thead>
        <tbody>
        <?php foreach ($sample as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['titulo'] ?? '[NULL]') ?></td>
            <td><?= htmlspecialchars($row['deleted_at'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
