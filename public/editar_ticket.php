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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Editar' : 'Crear' ?> Incidencia - Gestor de Incidencias</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2em;
            font-weight: 700;
        }

        .content {
            padding: 40px;
        }

        .error-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .error-box p {
            margin: 5px 0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1em;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-secondary {
            display: inline-block;
            padding: 12px 25px;
            background: #f0f0f0;
            color: #666;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
            color: #333;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= $id ? '✏️ Editar' : '➕ Crear' ?> Incidencia</h1>
        </div>

        <div class="content">
            <?php if ($errores): ?>
                <div class="error-box">
                    <strong>⚠️ Se encontraron errores:</strong>
                    <?php foreach ($errores as $e): ?>
                        <p>• <?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="titulo">📝 Título *</label>
                    <input 
                        type="text" 
                        id="titulo" 
                        name="titulo" 
                        placeholder="Ingresa el título de la incidencia"
                        value="<?= htmlspecialchars($titulo) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="descripcion">📄 Descripción *</label>
                    <textarea 
                        id="descripcion" 
                        name="descripcion" 
                        placeholder="Describe la incidencia en detalle"
                        required
                    ><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prioridad">🔥 Prioridad</label>
                        <select id="prioridad" name="prioridad">
                            <option value="baja" <?= $prioridad === 'baja' ? 'selected' : '' ?>>🟢 Baja</option>
                            <option value="media" <?= $prioridad === 'media' ? 'selected' : '' ?>>🟡 Media</option>
                            <option value="alta" <?= $prioridad === 'alta' ? 'selected' : '' ?>>🔴 Alta</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado">📊 Estado</label>
                        <select id="estado" name="estado">
                            <option value="abierta" <?= $estado === 'abierta' ? 'selected' : '' ?>>🆕 Abierta</option>
                            <option value="en progreso" <?= $estado === 'en progreso' ? 'selected' : '' ?>>⏳ En Progreso</option>
                            <option value="cerrada" <?= $estado === 'cerrada' ? 'selected' : '' ?>>✅ Cerrada</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <?= $id ? '💾 Actualizar Incidencia' : '✨ Crear Incidencia' ?>
                </button>
            </form>

            <a href="lista_tickets.php" class="btn-secondary">← Volver al listado</a>
        </div>
    </div>
</body>
</html>