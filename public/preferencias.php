<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: preferencias.php
// DESCRIPCIÓN: Permite al usuario cambiar el tema (Claro/Oscuro).
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

require_once __DIR__ . '/../app/auth.php';
require_login(); // Protegemos la página: solo usuarios registrados
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/theme.php';

// 1. PROCESAMIENTO DEL FORMULARIO
// Si el usuario ha enviado el formulario (método POST), guardamos su elección.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificamos el token CSRF para evitar ataques de falsificación de peticiones
    check_csrf();

    // Recogemos el dato. Si no viene nada, asumimos 'claro' por defecto.
    $tema = $_POST['tema'] ?? 'claro';

    // VALIDACIÓN DE SEGURIDAD:
    // Comprobamos que el valor enviado sea exactamente uno de los permitidos.
    // Nunca confiamos en lo que envía el navegador, podrían haberlo manipulado.
    if (!in_array($tema, ['claro', 'oscuro'], true)) {
        $tema = 'claro';
    }

    // 2. GUARDAR PREFERENCIA (COOKIE)
    // Usamos setcookie() para guardar la elección en el navegador del usuario.
    // - Nombre: 'tema'
    // - Valor: 'claro' u 'oscuro'
    // - Expiración: time() + 30 días (3600 seg * 24 horas * 30 días)
    // - Ruta: '/' para que esté disponible en toda la web
    setcookie('tema', $tema, time() + 3600 * 24 * 30, '/');

    // Redirigimos al inicio para que se recargue la página y se apliquen los cambios visuales
    header('Location: index.php');
    exit;
}

// 3. LEER PREFERENCIA ACTUAL
// Leemos la cookie actual para pre-seleccionar la opción en el formulario.
// Si no existe la cookie, usamos 'claro' por defecto.
$temaActual = $_COOKIE['tema'] ?? 'claro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Preferencias</title>
    <?= theme_styles() ?>
</head>
<body class="<?= htmlspecialchars(body_theme_class()) ?>">
<div class="container">
    <div class="header">
        <h1>Preferencias</h1>
        <div class="nav">
            <a href="index.php">Inicio</a>
            <a href="lista_tickets.php">Incidencias</a>
            <a href="editar_ticket.php">Nueva</a>
        </div>
    </div>
    
    <div class="content">
        <form method="POST" class="form-wrapper">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="tema">Selecciona el tema visual:</label>
                <select name="tema" id="tema">
                    <option value="claro" <?= $temaActual === 'claro' ? 'selected' : '' ?>>Claro (Default)</option>
                    <option value="oscuro" <?= $temaActual === 'oscuro' ? 'selected' : '' ?>>Oscuro (Dark Mode)</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary">Guardar Preferencia</button>
        </form>
        
        <footer style="margin-top:30px;">
            Tu tema actual es: <strong><?= htmlspecialchars(ucfirst($temaActual)) ?></strong>
        </footer>
    </div>
</div>
</body>
</html>