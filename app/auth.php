<?php
session_start();

/**
 * Requiere que el usuario esté autenticado. Si no, redirige a login.php
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Devuelve el id del usuario actual o null si no hay sesión.
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}