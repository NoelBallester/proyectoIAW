<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: crear_usuario.php
// DESCRIPCIÓN: Formulario para registrar nuevos usuarios administradores.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

require_once __DIR__ . '/../app/auth.php';
require_login(); // Solo usuarios ya logueados pueden crear otros (Seguridad)
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';
require_once __DIR__ . '/../app/csrf.php';

$pdo = getPDO();
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verificar Seguridad CSRF
    check_csrf();

    $username = trim($_POST['username'] ?? '');
    $pass1 = $_POST['pass1'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';

    // 2. Validaciones básicas
    if (empty($username) || empty($pass1) || empty($pass2)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($pass1 !== $pass2) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($pass1) < 4) {
        $error = "La contraseña debe tener al menos 4 caracteres.";
    } else {
        // 3. Comprobar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = :u");
        $stmt->bindValue(':u', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            $error = "El usuario '$username' ya existe.";
        } else {
            // 4. Crear usuario con contraseña encriptada (Hash)
            // password_hash es vital para no guardar texto plano
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            
            $insert = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (:u, :p)");
            $insert->bindValue(':u', $username, PDO::PARAM_STR);
            $insert->bindValue(':p', $hash, PDO::PARAM_STR);
            
            if ($insert->execute()) {
                $success = "Usuario '$username' creado correctamente.";
                // Limpiamos campos para no reenviar
                $username = ''; 
            } else {
                $error = "Error al guardar en la base de datos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
    <div class="container">
        <div class="header">
            <h1>Crear Nuevo Usuario</h1>
            <div class="nav">
                <a href="index.php">Inicio</a>
                <a href="lista_tickets.php">Incidencias</a>
            </div>
        </div>

        <div class="content">
            <?php if ($error): ?>
                <div class="error-box"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="error-box" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form-wrapper panel narrow" style="margin: 0 auto;">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" name="username" id="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="pass1">Contraseña</label>
                    <input type="password" name="pass1" id="pass1" required>
                </div>

                <div class="form-group">
                    <label for="pass2">Repetir Contraseña</label>
                    <input type="password" name="pass2" id="pass2" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Registrar Usuario</button>
            </form>
            
            <div style="text-align:center; margin-top:20px;">
                <a href="index.php" class="btn-secondary">Volver al Panel</a>
            </div>
        </div>
    </div>
</body>
</html>