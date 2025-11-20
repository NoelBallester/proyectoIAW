-- 1. PREPARACIÓN DE LA BASE DE DATOS
-- ---------------------------------------------------------
-- Borramos la BD si existe para empezar de cero (útil para resetear)
DROP DATABASE IF EXISTS inventario_iaw;

-- Creamos la BD con cotejamiento utf8mb4 (soporta emojis y acentos perfectamente)
CREATE DATABASE inventario_iaw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleccionamos la base de datos para ejecutar lo siguiente dentro de ella
USE inventario_iaw;


-- 2. CREACIÓN DE TABLAS
-- ---------------------------------------------------------

-- Tabla de Usuarios (para el login de la aplicación web)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- ¡Importante! Aquí guardaremos el HASH, nunca la clave real
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Items (El inventario en sí)
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50),
    ubicacion VARCHAR(50),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Auditoría (Requisito obligatorio para registrar borrados)
CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accion VARCHAR(255) NOT NULL, -- Descripción de lo que pasó (Ej: "Borrado item 5")
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- 3. GESTIÓN DE USUARIOS DE MYSQL (ACCESO REMOTO)
-- ---------------------------------------------------------
-- Esto crea un usuario 'NoelYA' que se puede conectar desde CUALQUIER IP ('%')
-- Cambia 'IAWAN' por la contraseña que tú quieras usar en tu proyecto

CREATE USER IF NOT EXISTS 'NoelYAngela'@'%' IDENTIFIED BY 'IAWAN';

-- Le damos permisos TOTALES, pero SOLO sobre esta base de datos (seguridad básica)
GRANT ALL PRIVILEGES ON inventario_iaw.* TO 'NoelYA'@'%';

-- Aplicamos los cambios de permisos inmediatamente
FLUSH PRIVILEGES;