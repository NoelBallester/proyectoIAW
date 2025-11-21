<?php
// app/theme.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Devuelve el tema actual basado en cookie 'tema' (claro|oscuro)
 */
function current_theme(): string {
    $t = $_COOKIE['tema'] ?? 'claro';
    return in_array($t, ['claro','oscuro'], true) ? $t : 'claro';
}

/**
 * Imprime las hojas de estilo necesarias
 */
function theme_styles(): string {
    $theme = current_theme();
    $suffix = $theme === 'oscuro' ? 'dark' : 'light';
    $version = 'v1'; // para caché busting si se desea
    $links = [];
    $links[] = '<link rel="stylesheet" href="css/base.css?'.$version.'">';
    $links[] = '<link rel="stylesheet" href="css/theme-'.$suffix.'.css?'.$version.'">';
    return implode("\n", $links);
}

/**
 * Clase que se añade al body para reglas específicas
 */
function body_theme_class(): string {
    return 'theme-' . (current_theme() === 'oscuro' ? 'dark' : 'light');
}
