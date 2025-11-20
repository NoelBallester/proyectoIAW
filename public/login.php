<?php
require_once 'pdo.php';
require_once 'auth.php';

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
            $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Evitar fijación de sesión
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];

                // Rehashear si el algoritmo cambió
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
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
<html>
<head><title>Login</title></head>
<body>
    <h1>Inicia sesión</h1>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label>Usuario: <input name="username" required value="<?= htmlspecialchars($username) ?>"></label><br>
        <label>Contraseña: <input type="password" name="password" required></label><br>
        <button type="submit">Entrar</button>
    </form>
    <?php if ($error): ?>
        <p style="color:yellow;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</body>
</html>