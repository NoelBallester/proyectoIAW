<?php
sesion_start() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user_id() {
return $_SESSION['user_id'] ?? null;
}