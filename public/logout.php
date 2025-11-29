<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: logout.php
// DESCRIPCIÓN: Cierra la sesión del usuario de forma segura y completa.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

// 1. RECUPERAR LA SESIÓN EXISTENTE
// Necesitamos "unirnos" a la sesión actual para poder destruirla.
session_start();

// 2. LIMPIAR VARIABLES DE MEMORIA
// Vaciamos el array $_SESSION para que no quede ningún dato (como user_id)
// disponible en memoria durante lo que reste de ejecución de este script.
$_SESSION = [];

// 3. BORRAR LA COOKIE DE SESIÓN (EN EL NAVEGADOR DEL CLIENTE)
// Esto es importante de cara a la seguridad. Si solo destruimos la sesión en el servidor,
// el navegador del usuario seguiría teniendo la cookie con el ID antiguo.
// Aquí forzamos a que esa cookie caduque en el pasado (time - 42000).
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. DESTRUIR LA SESIÓN (EN EL SERVIDOR)
// Esto borra físicamente el archivo de sesión que PHP guarda en el disco del servidor.
session_destroy();

// 5. REDIRECCIÓN FINAL
// Mandamos al usuario de vuelta a la pantalla de login para que vuelva a entrar si quiere.
header("Location: login.php");
exit;
?>