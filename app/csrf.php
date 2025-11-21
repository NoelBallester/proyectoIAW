<?php
// Helpers CSRF
// Proporciona: csrf_token(), csrf_field(), validate_csrf(), check_csrf()

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Devuelve el token CSRF almacenado en la sesión, creando uno nuevo si es necesario.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Devuelve el campo HTML hidden para incluir en formularios.
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Valida un token CSRF dado contra el almacenado en sesión.
 * Si no se pasa token, se comprueban POST['csrf_token'] y POST['csrf'] para compatibilidad.
 * Devuelve true si el token es válido, false en caso contrario.
 */
function validate_csrf(?string $token = null): bool {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_POST['csrf'] ?? '';
    }
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!is_string($token) || $token === '' || $sessionToken === '') return false;
    return hash_equals($sessionToken, $token);
}

/**
 * Verifica el token CSRF en peticiones POST y termina la ejecución con 403 si es inválido.
 * Mantiene comportamiento sencillo para puntos de entrada que esperan salida directa.
 */
function check_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!validate_csrf()) {
            http_response_code(403);
            echo "Token CSRF inválido.";
            exit;
        }
    }
}
