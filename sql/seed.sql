use inventario_iaw;
-- INSERTAR USUARIO DE PRUEBA PARA LA APLICACIÓN WEB
-- ---------------------------------------------------------
-- Usuario: admin
-- Password: admin123
-- Hash generado con password_hash('admin123', PASSWORD_DEFAULT) y verificado
INSERT INTO usuarios (username, password) VALUES 
('admin', '$2y$10$/tcIacKZhwqpQyvtHMmVsOQWwK4yrk9JtDs4GFFWdVN7gEErfYsda');

-- 50 tickets de prueba
INSERT INTO tickets (titulo, descripcion, prioridad, estado) VALUES
('Ticket 1', 'Descripción de prueba 1', 'baja', 'abierta'),
('Ticket 2', 'Descripción de prueba 2', 'media', 'abierta'),
('Ticket 3', 'Descripción de prueba 3', 'alta', 'cerrada'),
('Ticket 4', 'Descripción de prueba 4', 'baja', 'abierta'),
('Ticket 5', 'Descripción de prueba 5', 'media', 'cerrada'),
('Ticket 6', 'Descripción de prueba 6', 'alta', 'abierta'),
('Ticket 7', 'Descripción de prueba 7', 'baja', 'abierta'),
('Ticket 8', 'Descripción de prueba 8', 'media', 'abierta'),
('Ticket 9', 'Descripción de prueba 9', 'alta', 'cerrada'),
('Ticket 10', 'Descripción de prueba 10', 'baja', 'abierta'),
('Ticket 11', 'Descripción de prueba 11', 'media', 'abierta'),
('Ticket 12', 'Descripción de prueba 12', 'alta', 'cerrada'),
('Ticket 13', 'Descripción de prueba 13', 'baja', 'abierta'),
('Ticket 14', 'Descripción de prueba 14', 'media', 'cerrada'),
('Ticket 15', 'Descripción de prueba 15', 'alta', 'abierta'),
('Ticket 16', 'Descripción de prueba 16', 'baja', 'abierta'),
('Ticket 17', 'Descripción de prueba 17', 'media', 'abierta'),
('Ticket 18', 'Descripción de prueba 18', 'alta', 'cerrada'),
('Ticket 19', 'Descripción de prueba 19', 'baja', 'abierta'),
('Ticket 20', 'Descripción de prueba 20', 'media', 'cerrada'),
('Ticket 21', 'Descripción de prueba 21', 'alta', 'abierta'),
('Ticket 22', 'Descripción de prueba 22', 'baja', 'abierta'),
('Ticket 23', 'Descripción de prueba 23', 'media', 'abierta'),
('Ticket 24', 'Descripción de prueba 24', 'alta', 'cerrada'),
('Ticket 25', 'Descripción de prueba 25', 'baja', 'abierta'),
('Ticket 26', 'Descripción de prueba 26', 'media', 'cerrada'),
('Ticket 27', 'Descripción de prueba 27', 'alta', 'abierta'),
('Ticket 28', 'Descripción de prueba 28', 'baja', 'abierta'),
('Ticket 29', 'Descripción de prueba 29', 'media', 'abierta'),
('Ticket 30', 'Descripción de prueba 30', 'alta', 'cerrada'),
('Ticket 31', 'Descripción de prueba 31', 'baja', 'abierta'),
('Ticket 32', 'Descripción de prueba 32', 'media', 'cerrada'),
('Ticket 33', 'Descripción de prueba 33', 'alta', 'abierta'),
('Ticket 34', 'Descripción de prueba 34', 'baja', 'abierta'),
('Ticket 35', 'Descripción de prueba 35', 'media', 'abierta'),
('Ticket 36', 'Descripción de prueba 36', 'alta', 'cerrada'),
('Ticket 37', 'Descripción de prueba 37', 'baja', 'abierta'),
('Ticket 38', 'Descripción de prueba 38', 'media', 'cerrada'),
('Ticket 39', 'Descripción de prueba 39', 'alta', 'abierta'),
('Ticket 40', 'Descripción de prueba 40', 'baja', 'abierta'),
('Ticket 41', 'Descripción de prueba 41', 'media', 'abierta'),
('Ticket 42', 'Descripción de prueba 42', 'alta', 'cerrada'),
('Ticket 43', 'Descripción de prueba 43', 'baja', 'abierta'),
('Ticket 44', 'Descripción de prueba 44', 'media', 'cerrada'),
('Ticket 45', 'Descripción de prueba 45', 'alta', 'abierta'),
('Ticket 46', 'Descripción de prueba 46', 'baja', 'abierta'),
('Ticket 47', 'Descripción de prueba 47', 'media', 'abierta'),
('Ticket 48', 'Descripción de prueba 48', 'alta', 'cerrada'),
('Ticket 49', 'Descripción de prueba 49', 'baja', 'abierta'),
('Ticket 50', 'Descripción de prueba 50', 'media', 'cerrada');