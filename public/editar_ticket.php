<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';

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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Editar' : 'Crear' ?> Incidencia - Gestor de Incidencias</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
    <div class="container">
        <div class="header">
            <h1><?= $id ? ' Editar' : ' Crear' ?> Incidencia</h1>
            <div class="nav">
                <a href="index.php"> Inicio</a>
                <a href="lista_tickets.php"> Listado</a>
                <a href="preferencias.php"> Preferencias</a>
            </div>
        </div>

    <div class="content wide form-wrapper">
            <?php if ($errores): ?>
                <div class="error-box"><strong> Errores:</strong>
                    <?php foreach ($errores as $e): ?><p>• <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="titulo"> Título *</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Ingresa el título" value="<?= htmlspecialchars($titulo) ?>" required>
                </div>

                <div class="form-group">
                    <label for="descripcion"> Descripción *</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Describe la incidencia" required><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prioridad"> Prioridad</label>
                        <select id="prioridad" name="prioridad">
                            <option value="baja" <?= $prioridad === 'baja' ? 'selected' : '' ?>> Baja</option>
                            <option value="media" <?= $prioridad === 'media' ? 'selected' : '' ?>> Media</option>
                            <option value="alta" <?= $prioridad === 'alta' ? 'selected' : '' ?>> Alta</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado"> Estado</label>
                        <select id="estado" name="estado">
                            <option value="abierta" <?= $estado === 'abierta' ? 'selected' : '' ?>> Abierta</option>
                            <option value="en progreso" <?= $estado === 'en progreso' ? 'selected' : '' ?>> En Progreso</option>
                            <option value="cerrada" <?= $estado === 'cerrada' ? 'selected' : '' ?>> Cerrada</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width:100%"><?= $id ? ' Actualizar Incidencia' : ' Crear Incidencia' ?></button>
            </form>
            <a href="lista_tickets.php" class="btn-secondary" style="margin-top:20px">← Volver al listado</a>
        </div>
    </div>
</body>
</html>
