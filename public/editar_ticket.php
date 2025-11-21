<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';

$pdo = getPDO();  
$errores = [];
$titulo = '';
$descripcion = '';
$prioridad = 'media';
$estado = 'abierta';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Si viene un ID, cargar el ticket existente
if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        http_response_code(404);
        echo "El ticket no ha sido encontrado.";
        exit;
    }
    // Pre-cargar campos del ticket
    $titulo = $ticket['titulo'] ?? '';
    $descripcion = $ticket['descripcion'] ?? '';
    $prioridad = $ticket['prioridad'] ?? 'media';
    $estado = $ticket['estado'] ?? 'abierta';
}

// Si se envia el formulario:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $estado = $_POST['estado'] ?? 'abierta';

    // Validaciones
    if ($titulo === '') {
        $errores[] = "El título es obligatorio.";
    }
    if ($descripcion === '') {
        $errores[] = "La descripción es obligatoria.";
    }
    if (!in_array($prioridad, ['baja', 'media', 'alta'])) $errores[] = "Prioridad inválida.";
    if (!in_array($estado, ['abierta', 'en progreso', 'cerrada'])) $errores[] = "Estado inválido.";

    // Si no hay errores, insertar o actualizar
    if (empty($errores)) {
        if ($id) {
            // Actualizar
            $stmt = $pdo->prepare("UPDATE tickets SET titulo = ?, descripcion = ?, prioridad = ?, estado = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$titulo, $descripcion, $prioridad, $estado, $id]);
        } else {
            // Insertar nuevo
            $stmt = $pdo->prepare("INSERT INTO tickets (titulo, descripcion, prioridad, estado, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$titulo, $descripcion, $prioridad, $estado]);
        }
        header('Location: lista_tickets.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $id ? 'Editar Ticket' : 'Crear Ticket' ?> Incidencias </title>
</head>
<body>
<h1><?= $id ? 'Editar Ticket' : 'Crear Ticket' ?> Incidencias</h1>
<?php if ($errores): ?>
    <?php foreach ($errores as $e): ?>
        <p style="color:red"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST">
    <label>Título: <input type="text" name="titulo" value="<?= htmlspecialchars($titulo) ?>"></label><br>
    <label>Descripción:<br>
        <textarea name="descripcion" rows="5" cols="50"><?= htmlspecialchars($descripcion) ?></textarea>
    </label><br>
    <label>Prioridad:
        <select name="prioridad">
            <option value="baja" <?= $prioridad === 'baja' ? 'selected' : '' ?>>Baja</option>
            <option value="media" <?= $prioridad === 'media' ? 'selected' : '' ?>>Media</option>
            <option value="alta" <?= $prioridad === 'alta' ? 'selected' : '' ?>>Alta</option>
        </select>
    </label><br>
    <label>Estado:
        <select name="estado">
            <option value="abierta" <?= $estado === 'abierta' ? 'selected' : '' ?>>Abierta</option>
            <option value="en progreso" <?= $estado === 'en progreso' ? 'selected' : '' ?>>En Progreso</option>
            <option value="cerrada" <?= $estado === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
        </select>
    </label><br>
    <button type="submit"><?= $id ? 'Actualizar' : 'Crear' ?> Ticket</button>
</form>
<p><a href="lista_tickets.php">Volver al listado de incidencias</a></p>
</body>
</html>