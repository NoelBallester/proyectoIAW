<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: login.php
// DESCRIPCIÓN: Página de inicio de sesión de usuarios.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

// Iniciamos sesión para poder usar variables $_SESSION
session_start();

// Cargamos dependencias básicas
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/theme.php';
// Nota: No incluimos auth.php aquí con require_login() porque 
// si no, entraríamos en un bucle infinito de redirecciones.

$error = null;

// 1. REDIRECCIÓN SI YA ESTÁ LOGUEADO
// Si el usuario ya tiene sesión, lo mandamos al panel principal.
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 2. GENERACIÓN DE TOKEN CSRF
// Si no existe un token de seguridad en la sesión, creamos uno nuevo.
// Esto evita ataques de falsificación de peticiones en el formulario.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. PROCESAR FORMULARIO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verificamos el token CSRF enviado por el formulario
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        // Si no coincide, bloqueamos la solicitud
        $error = 'Error de seguridad (CSRF). Recarga la página.';
    } else {
        // Limpiamos y recogemos los datos
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Por favor, introduce usuario y contraseña.';
        } else {
            // Conectamos a la BD
            $pdo = getPDO();
            
            // Buscamos al usuario por su nombre
            $stmt = $pdo->prepare('SELECT id, password FROM usuarios WHERE username = :u');
            $stmt->bindValue(':u', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            // 4. VERIFICACIÓN DE CONTRASEÑA
            // Usamos password_verify para comparar el texto plano con el hash de la BD
            if ($user && password_verify($password, $user['password'])) {
                
                // SEGURIDAD CRÍTICA: Regenerar ID de sesión
                // Esto evita ataques de "Fijación de Sesión".
                session_regenerate_id(true);
                
                // Guardamos el ID del usuario en la sesión (esto es lo que lo marca como logueado)
                $_SESSION['user_id'] = $user['id'];

                // MANTENIMIENTO: Si el algoritmo de hash ha mejorado en PHP,
                // actualizamos la contraseña en la BD automáticamente.
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
                    $upd->execute([$newHash, $user['id']]);
                }

                // Redirigimos al panel principal
                header('Location: index.php');
                exit;
            } else {
                // Mensaje genérico por seguridad (no decir si falla usuario o contraseña)
                $error = 'Credenciales incorrectas.';
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
    <title>Iniciar Sesión</title>
    <?= theme_styles() ?>
    <style>
        /* Ajuste específico para centrar verticalmente el login un poco más */
        body { align-items: center; min-height: 100vh; }
    </style>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
    <div class="panel narrow">
        <div class="header" style="padding: 30px; border-radius: 20px 20px 0 0;">
            <h1>Gestor de Incidencias</h1>
            <p style="opacity: 0.9; font-size: 0.9em;">Acceso restringido</p>
        </div>

        <div class="content">
            <?php if ($error): ?>
                <div class="error-box" style="text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Tu nombre de usuario" 
                        required 
                        autofocus
                        value="<?= htmlspecialchars($username ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Tu contraseña" 
                        required
                    >
                </div>

                <button type="submit" class="btn-primary" style="width:100%; margin-top: 10px;">
                    Entrar
                </button>
            </form>
            
            <div style="margin-top: 25px; text-align: center; font-size: 0.85em;">
                <a href="preferencias.php" style="color: var(--color-text); text-decoration: none; border-bottom: 1px dotted #ccc;">
                    Configurar Preferencias
                </a>
            </div>
        </div>
    </div>
</body>
</html>