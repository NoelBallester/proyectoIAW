<?php
require_once __DIR__ . '/../app/auth.php';
require_login(); // obliga a estar logueado
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel principal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        nav a { margin-right: 1em; }
    </style>
</head>
<body>
    <h1>Bienvenido al gestor de incidencias</h1>

    <nav>
        <a href="tickets_list.php">Listado de incidencias</a>
        <a href="tickets_form.php">Crear nueva incidencia</a>
        <a href="preferencias.php">Preferencias</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <p>Has iniciado sesión correctamente. Usa el menú para gestionar incidencias.</p>
</body>
</html>