<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: editar_ticket.php
// DESCRIPCIÓN: Formulario único para CREAR o EDITAR una incidencia.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

require_once __DIR__ . '/../app/auth.php';
require_login(); // Solo usuarios autenticados pueden crear/editar
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';

$pdo = getPDO();  

// Inicializamos variables vacías por defecto (para el modo "Crear")
$errores = [];
$titulo = '';
$descripcion = '';
$prioridad = 'media';
$estado = 'abierta';

// 1. RECOGIDA DEL ID (Modo Edición)
// Usamos filter_input para validar que el ID sea un número entero seguro.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// 2. LÓGICA DE CARGA (MÉTODO GET)
// Si hay un ID y NO estamos enviando el formulario, cargamos los datos de la BD.
if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id AND deleted_at IS NULL");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        // Si el ticket no existe o está borrado, mostramos error
        die("Error: La incidencia no existe o ha sido eliminada.");
    }
    
    // Rellenamos las variables con los datos de la base de datos
    $titulo = $ticket['titulo'];
    $descripcion = $ticket['descripcion'];
    $prioridad = $ticket['prioridad'];
    $estado = $ticket['estado'];
}

// 3. LÓGICA DE GUARDADO (MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos y limpiamos los datos del formulario
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $estado = $_POST['estado'] ?? 'abierta';

    // VALIDACIONES
    // Comprobamos que no haya campos vacíos
    if (empty($titulo)) {
        $errores[] = "El título es obligatorio.";
    }
    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    }
    // Validamos que los valores de los select sean correctos (White-list)
    if (!in_array($prioridad, ['baja', 'media', 'alta'])) {
        $errores[] = "La prioridad seleccionada no es válida.";
    }
    if (!in_array($estado, ['abierta', 'en progreso', 'cerrada'])) {
        $errores[] = "El estado seleccionado no es válido.";
    }

    // SI NO HAY ERRORES, GUARDAMOS EN LA BD
    if (empty($errores)) {
        if ($id) {
            // --- ACTUALIZAR (UPDATE) ---
            $sql = "UPDATE tickets SET titulo = :tit, descripcion = :desc, prioridad = :prio, estado = :est, updated_at = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            // --- INSERTAR (INSERT) ---
            // Nota: created_at se pone a NOW(), updated_at se deja NULL al crear
            $sql = "INSERT INTO tickets (titulo, descripcion, prioridad, estado, created_at) VALUES (:tit, :desc, :prio, :est, NOW())";
            $stmt = $pdo->prepare($sql);
        }

        // Asignamos los valores comunes
        $stmt->bindValue(':tit', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':desc', $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(':prio', $prioridad, PDO::PARAM_STR);
        $stmt->bindValue(':est', $estado, PDO::PARAM_STR);
        
        $stmt->execute();

        // Redirigimos al listado tras guardar
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
    <title><?= $id ? 'Editar' : 'Crear' ?> Incidencia</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
    <div class="container">
        <div class="header">
            <h1><?= $id ? 'Editar' : 'Crear' ?> Incidencia</h1>
            <div class="nav">
                <a href="index.php">Inicio</a>
                <a href="lista_tickets.php">Listado</a>
                <a href="preferencias.php">Preferencias</a>
            </div>
        </div>

        <div class="content wide">
            <?php if ($errores): ?>
                <div class="error-box">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul style="margin: 5px 0 0 20px; padding:0;">
                        <?php foreach ($errores as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Ej: Fallo en la impresora" value="<?= htmlspecialchars($titulo) ?>" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Explica detalladamente qué ocurre..." required><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prioridad">Prioridad</label>
                        <select id="prioridad" name="prioridad">
                            <option value="baja" <?= $prioridad === 'baja' ? 'selected' : '' ?>>Baja</option>
                            <option value="media" <?= $prioridad === 'media' ? 'selected' : '' ?>>Media</option>
                            <option value="alta" <?= $prioridad === 'alta' ? 'selected' : '' ?>>Alta</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="abierta" <?= $estado === 'abierta' ? 'selected' : '' ?>>Abierta</option>
                            <option value="en progreso" <?= $estado === 'en progreso' ? 'selected' : '' ?>>En Progreso</option>
                            <option value="cerrada" <?= $estado === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width:100%; margin-top:10px;">
                    <?= $id ? 'Actualizar Incidencia' : 'Guardar Incidencia' ?>
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center;">
                <a href="lista_tickets.php" class="btn-secondary">Cancelar y volver</a>
            </div>
        </div>
    </div>
</body>
</html>