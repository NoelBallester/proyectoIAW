<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: index.php
// DESCRIPCIÓN: Panel de control (Dashboard) principal.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

require_once __DIR__ . '/../app/auth.php';
require_login(); // Verificamos que el usuario esté logueado
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';

// 1. OBTENER DATOS DEL USUARIO
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT username FROM usuarios WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();
$username = $user['username'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
    <div class="container">
        
        <div class="header">
            <h1>Gestor de Incidencias</h1>
            <div class="user-info">
                Bienvenido, <strong><?= htmlspecialchars($username) ?></strong>
            </div>
            
            <div class="nav">
                <a href="lista_tickets.php">Incidencias</a>
                <a href="editar_ticket.php">Nueva</a>
                <a href="crear_usuario.php">Usuarios</a>
                <a href="preferencias.php">Preferencias</a>
            </div>
        </div>

        <div class="content">
            <div class="grid cards">
                <a href="lista_tickets.php" class="card primary">
                    <span class="icon">&equiv;</span>
                    <div class="title">Listado</div>
                    <div class="desc">Ver y gestionar incidencias</div>
                </a>

                <a href="editar_ticket.php" class="card">
                    <span class="icon">+</span>
                    <div class="title">Crear Incidencia</div>
                    <div class="desc">Registrar nueva tarea</div>
                </a>

                <a href="crear_usuario.php" class="card">
                    <span class="icon">@</span>
                    <div class="title">Usuarios</div>
                    <div class="desc">Registrar nuevos administradores</div>
                </a>
                
                <a href="preferencias.php" class="card">
                    <span class="icon">~</span>
                    <div class="title">Preferencias</div>
                    <div class="desc">Cambiar tema visual</div>
                </a>
            </div>

            <div style="text-align:center; margin-top:40px;">
                <form action="logout.php" method="post">
                    <button type="submit" class="btn-secondary" style="border:1px solid #ccc;">
                        Cerrar Sesión
                    </button>
                </form>
            </div>

            <footer>
                &copy; <?= date('Y') ?> Gestor de Incidencias
            </footer>
        </div>
    </div>
</body>
</html>