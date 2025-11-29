<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: app/pdo.php
// DESCRIPCIÓN: Conexión centralizada a la base de datos MySQL.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

// 1. CONFIGURACIÓN DE LA BASE DE DATOS
$host = '127.0.0.1';      // IP del servidor de la BD (localhost)
$port = '3306';           // Puerto estándar de MySQL
$db   = 'inventario_iaw'; // Nombre de la base de datos
$user = 'NoelYAngela';    // Usuario creado en MySQL
$pass = 'IAWAN';          // Contraseña del usuario
$charset = 'utf8mb4';     // Juego de caracteres (soporta tildes y eñes)

// DSN (Data Source Name): La cadena de conexión que necesita PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

// 2. OPCIONES DE PDO
$options = [
    // Si hay un error SQL, lanza una "Excepción" para que PHP pare y avise
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Los datos vienen como un Array Asociativo ['columna' => 'valor']
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // DESACTIVAMOS la emulación. Seguridad critica contra Inyección SQL.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 3. INTENTO DE CONEXIÓN
try {
    // Creamos la nueva instancia de PDO (abrimos la conexión)
    $pdo = new PDO($dsn, $user, $pass, $options);
    
} catch (\PDOException $e) {
    // Si falla, mostramos mensaje de error sin adornos
    die("Error Critico: No se pudo conectar a la Base de Datos. Detalle: " . $e->getMessage());
}

/**
 * Función auxiliar para obtener la conexión desde otros archivos.
 * Devuelve el objeto $pdo activo.
 */
function getPDO() {
    global $pdo;
    return $pdo;
}
?>