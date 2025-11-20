<?php
// app/pdo.php

// Configuración de la base de datos
$host = '%';
$db   = 'inventario_iaw';
$user = 'NoelYAngela';
$pass = 'IAWAN';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Para manejar errores como excepciones 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Para recibir arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Seguridad real en consultas preparadas
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En producción no mostraríamos el error real, pero para desarrollo ayuda
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

/**
 * Devuelve la instancia PDO creada arriba.
 * Función auxiliar para usar en otras partes del proyecto.
 */
function getPDO() {
    global $pdo;
    return $pdo;
}
?>