<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: app/csrf.php
// DESCRIPCIÓN: Funciones de seguridad contra ataques CSRF.
//              (Cross-Site Request Forgery - Falsificación de Petición en Sitios Cruzados)
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

// 1. INICIO DE SESIÓN
// El token CSRF se guarda en la sesión del usuario, así que necesitamos que esté activa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Genera y devuelve el token CSRF actual.
 * Si no existe uno, crea uno nuevo aleatorio y seguro.
 * * @return string El token hexadecimal.
 */
function csrf_token(): string {
    // Si la sesión no tiene token, creamos uno.
    if (empty($_SESSION['csrf_token'])) {
        // random_bytes(32) genera 32 bytes de aleatoriedad criptográfica segura.
        // bin2hex lo convierte a una cadena legible (64 caracteres).
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Genera el campo HTML <input type="hidden"> listo para pegar en formularios.
 * Esto facilita la vida: solo escribes <?= csrf_field() ?> en el HTML.
 * * @return string HTML del input oculto.
 */
function csrf_field(): string {
    // Usamos htmlspecialchars para evitar romper el HTML si el token tuviera caracteres raros (aunque es hex).
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Compara un token recibido (normalmente por POST) con el de la sesión.
 * * @param string|null $token El token a comprobar. Si es null, busca en $_POST.
 * @return bool True si es válido, False si es un ataque o error.
 */
function validate_csrf(?string $token = null): bool {
    // Si no nos pasan un token específico, lo buscamos en el formulario enviado (POST)
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? '';
    }
    
    // Recuperamos el token real de la sesión
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    // Validaciones básicas: tienen que ser strings y no estar vacíos
    if (!is_string($token) || $token === '' || $sessionToken === '') {
        return false;
    }

    // SEGURIDAD: Usamos hash_equals en lugar de '=='.
    // Esto evita "Ataques de Tiempo" (Timing Attacks), donde un hacker podría
    // adivinar el token midiendo cuánto tarda el servidor en comparar las letras.
    return hash_equals($sessionToken, $token);
}

/**
 * Función "portero": Verifica el token automáticamente en peticiones POST.
 * Si el token falla, detiene el script inmediatamente con un error 403.
 */
function check_csrf(): void {
    // Solo comprobamos CSRF si se están enviando datos (método POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!validate_csrf()) {
            // Si la validación falla, cortamos el acceso.
            http_response_code(403); // Forbidden (Prohibido)
            die("Error de seguridad: Token CSRF inválido o expirado. Por favor, recarga la página e inténtalo de nuevo.");
        }
    }
}
?>