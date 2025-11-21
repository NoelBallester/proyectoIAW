-- 1. PREPARACIÓN DE LA BASE DE DATOS
-- ---------------------------------------------------------
DROP DATABASE IF EXISTS inventario_iaw;

CREATE DATABASE inventario_iaw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE inventario_iaw;


-- 2. CREACIÓN DE TABLAS
-- ---------------------------------------------------------

-- Tabla de Tickets (incidencias)
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('baja','media','alta') DEFAULT 'media',
    estado ENUM('abierta','en progreso','cerrada') DEFAULT 'abierta',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

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

-- Auditoría de tickets
CREATE TABLE IF NOT EXISTS ticket_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (ticket_id)
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
