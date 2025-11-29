<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: app/auth.php
// DESCRIPCIÓN: Funciones para gestión de sesión y autenticación.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

// 1. INICIO DE SESIÓN SEGURO
// Antes de llamar a session_start(), comprobamos si la sesión ya está activa.
// Esto evita errores de tipo "Notice: A session had already been started".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario ha iniciado sesión.
 * Si no está autenticado, lo redirige forzosamente al login.
 */
function require_login() {
    // Si no existe la variable 'user_id' en la sesión, es que no está logueado.
    if (!isset($_SESSION['user_id'])) {
        // Redirigimos al formulario de entrada
        header('Location: login.php');
        
        // IMPORTANTE: Usar exit después de header para detener la ejecución del script inmediatamente.
        // Si no, el código que haya debajo se seguiría ejecutando en segundo plano, lo cual es un fallo de seguridad.
        exit;
    }
}

/**
 * Devuelve el ID del usuario actual conectado.
 * Útil para registrar auditorías o filtrar datos propios.
 * * @return int|null Devuelve el ID o null si no hay sesión.
 */
function current_user_id() {
    // Usamos el operador '??' (null coalescing)
    // Si $_SESSION['user_id'] existe, lo devuelve. Si no, devuelve null.
    return $_SESSION['user_id'] ?? null;
}
?>