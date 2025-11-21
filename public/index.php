<?php
// Carga de utilidades de autenticación (ruta corregida)
require_once __DIR__ . '/../app/auth.php';
require_login();
?>
<!DOCTYPE html>
<html>
    <head><title>Panel</title></head>
    <body>
        <h1>Bienvenido al panel</h1>
        <p><a href="logout.php">Cerrar sesión</a></p>
    </body>
</html>