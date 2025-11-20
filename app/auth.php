<?php
// PHP  Gestion de Sesiones
// Comentario añadido para crear un cambio y permitir el commit solicitado.
sesion_start() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user_id() {
return $_SESSION['user_id'] ?? null;
}