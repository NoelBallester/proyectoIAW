<?php
// Rutas corregidas hacia la carpeta app
session_start();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/theme.php';

$error = null;

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Asegurar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Comprobación básica CSRF
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        http_response_code(400);
        $error = 'Solicitud inválida.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Usuario y contraseña son requeridos.';
        } else {
            $pdo = getPDO();
            $stmt = $pdo->prepare('SELECT id, password FROM usuarios WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Evitar fijación de sesión
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];

                // Rehashear si el algoritmo cambió
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
                    $upd->execute([$newHash, $user['id']]);
                }

                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
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
    <title>Iniciar Sesión - Gestor de Incidencias</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
    <div class="login-container panel narrow">
        <div class="login-header">
            <h1> Gestor de Incidencias</h1>
            <p class="subheader">Inicia sesión para continuar</p>
        </div>

        <div class="login-body content">
            <?php if ($error): ?>
                <div class="error-box"> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div class="form-group">
                    <label for="username"> Usuario</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Ingresa tu usuario" 
                        required 
                        autofocus
                        value="<?= htmlspecialchars($username) ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password"> Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ingresa tu contraseña" 
                        required
                    >
                </div>

                <button type="submit" class="btn-primary" style="width:100%"> Iniciar Sesión</button>
            </form>
            <div class="nav" style="margin-top:25px;">
                <a href="preferencias.php"> Preferencias</a>
            </div>
        </div>
    </div>
</body>
</html>
