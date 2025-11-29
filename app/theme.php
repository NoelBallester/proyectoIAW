<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: app/theme.php
// DESCRIPCIÓN: Lógica para gestionar el cambio de tema (Claro/Oscuro).
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

// Aseguramos que la sesión esté iniciada, aunque para leer cookies ($_COOKIE)
// no es estrictamente necesario, ayuda si decidimos guardar preferencias en sesión en el futuro.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Detecta qué tema quiere el usuario.
 * Lee la cookie 'tema'. Si no existe o tiene un valor raro, devuelve 'claro'.
 * * @return string 'claro' o 'oscuro'
 */
function current_theme(): string {
    // Usamos el operador de fusión null (??) para evitar errores si la cookie no existe
    $t = $_COOKIE['tema'] ?? 'claro';
    
    // SEGURIDAD: Validamos que el valor sea EXACTAMENTE uno de los permitidos.
    // Esto evita que alguien inyecte nombres de archivos raros en la cookie.
    return in_array($t, ['claro', 'oscuro'], true) ? $t : 'claro';
}

/**
 * Genera las etiquetas HTML <link> para cargar los CSS.
 * Carga siempre 'base.css' y luego el tema específico ('theme-light.css' o 'theme-dark.css').
 * * @return string Código HTML con los links a los CSS.
 */
function theme_styles(): string {
    $theme = current_theme();
    
    // Convertimos 'claro'/'oscuro' a los sufijos de archivo 'light'/'dark'
    $suffix = ($theme === 'oscuro') ? 'dark' : 'light';
    
    // Añadimos un parámetro de versión (?v=1.0) para "burlar" la caché del navegador
    // cuando hagamos cambios en el CSS.
    $version = '1.0'; 

    // Construimos las etiquetas HTML
    // 1. base.css (Estructura y diseño común)
    $html = '<link rel="stylesheet" href="css/base.css?v=' . $version . '">' . "\n";
    
    // 2. theme-X.css (Solo colores y variables)
    $html .= '    <link rel="stylesheet" href="css/theme-' . $suffix . '.css?v=' . $version . '">';
    
    return $html;
}

/**
 * Devuelve la clase CSS que debemos ponerle al <body>.
 * Útil si queremos aplicar estilos específicos en base.css usando selectores como:
 * body.theme-dark .mi-componente { ... }
 * * @return string 'theme-light' o 'theme-dark'
 */
function body_theme_class(): string {
    return 'theme-' . ((current_theme() === 'oscuro') ? 'dark' : 'light');
}
?>