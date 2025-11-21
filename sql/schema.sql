-- 1. PREPARACIÓN DE LA BASE DE DATOS
-- ---------------------------------------------------------
DROP DATABASE IF EXISTS inventario_iaw;

CREATE DATABASE inventario_iaw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

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

-- Tabla de Tickets (Soporte técnico)
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('baja','media','alta') DEFAULT 'media',
    status ENUM('abierta','cerrada') DEFAULT 'abierta',
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    eliminado TIMESTAMP NULL DEFAULT NULL
);

-- 3. GESTIÓN DE USUARIOS DE MYSQL (ACCESO REMOTO)
-- ---------------------------------------------------------

CREATE USER IF NOT EXISTS 'NoelYAngela'@'%' IDENTIFIED BY 'IAWAN';

GRANT ALL PRIVILEGES ON inventario_iaw.* TO 'NoelYAngela'@'%';

FLUSH PRIVILEGES;
-- 4. INSERTAR USUARIO DE PRUEBA PARA LA APLICACIÓN WEB
-- ---------------------------------------------------------
-- Usuario: admin
-- Password: admin123
-- Hash generado con password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO usuarios (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');