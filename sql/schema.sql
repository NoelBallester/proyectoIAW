/* =================================================================
   PROYECTO: Gestión de Incidencias
   FICHERO: sql/schema.sql
   DESCRIPCIÓN: Estructura de la base de datos (Tablas e índices).
   ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
   ================================================================= */

-- 1. PREPARACIÓN DE LA BASE DE DATOS
-- ---------------------------------------------------------
-- Borramos la BD si existe para empezar de cero (útil en desarrollo)
DROP DATABASE IF EXISTS inventario_iaw;
CREATE DATABASE inventario_iaw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_iaw;

-- 2. CREACIÓN DE TABLAS
-- ---------------------------------------------------------

-- TABLA DE USUARIOS
-- Guardará los administradores con acceso al panel.
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hash de la contraseña
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLA DE TICKETS (INCIDENCIAS)
-- Esta es la definición correcta que coincide con tu código PHP (titulo, descripcion, etc.)
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('baja','media','alta') DEFAULT 'media',
    estado ENUM('abierta','en progreso','cerrada') DEFAULT 'abierta',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    -- Soft Delete: Si tiene fecha, está borrado. Si es NULL, está activo.
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- TABLA DE AUDITORÍA
-- Registra quién hizo qué. Vital para el requisito de "Transacción".
CREATE TABLE ticket_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    action VARCHAR(50) NOT NULL, -- Ej: 'borrado'
    user_id INT NOT NULL,        -- ID del usuario que hizo la acción
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Relaciones (Foreign Keys)
    FOREIGN KEY (user_id) REFERENCES usuarios(id)
    -- No vinculamos ticket_id con FK estricta para permitir guardar logs de tickets borrados físicamente si fuera necesario
) ENGINE=InnoDB;

-- 3. GESTIÓN DE USUARIOS DE MYSQL
-- ---------------------------------------------------------
-- Crea el usuario para la conexión PDO
CREATE USER IF NOT EXISTS 'NoelYAngela'@'%' IDENTIFIED BY 'IAWAN';
GRANT ALL PRIVILEGES ON inventario_iaw.* TO 'NoelYAngela'@'%';
FLUSH PRIVILEGES;