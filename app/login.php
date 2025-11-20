<?php  
require_once 'pdo.php';
require_once 'auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = ?');
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h1>Inicia sesión</h1>
    <form method="POST">
        <label>Usuario: <input name="username" required></label><br>
        <label>Contraseña: <input type="password" name="password" required></label><br>
        <button type="submit">Entrar</button>
    </form>
    <?php if ($error): ?>
        <p style="color:yellow;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</body>
</html>
