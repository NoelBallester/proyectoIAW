/* =================================================================
   PROYECTO: Gestión de Incidencias
   FICHERO: sql/seed.sql
   DESCRIPCIÓN: Datos de prueba iniciales (Seed).
   ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
   ================================================================= */

USE inventario_iaw;

-- 1. INSERTAR USUARIO ADMIN
-- Usuario: admin
-- Contraseña: 1234 (Hash generado con BCRYPT)
INSERT INTO usuarios (username, password) VALUES 
('admin', '$2y$10$8CnL.TT/YyjHR5ZZ71DciOXlgMWJr2rzNTstfUqHHnBmnBNlUmUm2');

-- 2. INSERTAR 50 TICKETS DE PRUEBA
-- Datos variados para probar paginación y filtros.

INSERT INTO tickets (titulo, descripcion, prioridad, estado, created_at) VALUES
('Fallo en impresora HP', 'La impresora de RRHH no conecta en red.', 'alta', 'abierta', NOW()),
('Pantalla parpadea', 'El monitor del puesto 4 hace cosas raras.', 'media', 'en progreso', NOW()),
('Instalar Office 2021', 'Necesario para el nuevo becario.', 'baja', 'cerrada', NOW()),
('No funciona internet', 'El cable de red parece roto en sala 2.', 'alta', 'abierta', NOW()),
('Ratón roto', 'Solicito cambio de ratón inalámbrico.', 'baja', 'abierta', NOW()),
('Actualizar servidor', 'Parche de seguridad pendiente en Ubuntu.', 'alta', 'en progreso', NOW()),
('Recuperar contraseña', 'Usuario bloqueado en el dominio.', 'media', 'cerrada', NOW()),
('Error 404 en la web', 'La página de contacto no carga.', 'alta', 'abierta', NOW()),
('PC muy lento', 'Posible virus en el equipo de contabilidad.', 'media', 'en progreso', NOW()),
('Teclado sucio', 'Solicitud de limpieza o cambio.', 'baja', 'cerrada', NOW()),

-- Bloque 2
('Solicitud VPN', 'Configurar acceso remoto para Juan.', 'media', 'abierta', NOW()),
('Backup fallido', 'El log indica error de escritura en disco.', 'alta', 'abierta', NOW()),
('Cambio de toner', 'Impresora láser planta 2.', 'baja', 'cerrada', NOW()),
('Wifi lento', 'Poca señal en la sala de reuniones.', 'media', 'en progreso', NOW()),
('Licencia caducada', 'El antivirus pide renovación.', 'alta', 'abierta', NOW()),
('Ruido en la torre', 'El ventilador hace mucho ruido.', 'baja', 'abierta', NOW()),
('No imprime a color', 'Configuración de drivers incorrecta.', 'media', 'cerrada', NOW()),
('Solicitud monitor extra', 'Para desarrollo de software.', 'baja', 'abierta', NOW()),
('Fallo de DNS', 'No resuelven dominios internos.', 'alta', 'en progreso', NOW()),
('Cambio de sitio', 'Mover equipos a la nueva oficina.', 'media', 'abierta', NOW()),

-- Bloque 3
('Error en Excel', 'Se cierra al abrir macros.', 'media', 'abierta', NOW()),
('Proyector fundido', 'Lámpara del proyector agotada.', 'media', 'cerrada', NOW()),
('Cable HDMI roto', 'Sustitución urgente para reunión.', 'alta', 'abierta', NOW()),
('Altavoces no suenan', 'Problema de drivers de audio.', 'baja', 'cerrada', NOW()),
('Webcam no detectada', 'Zoom no reconoce la cámara.', 'media', 'en progreso', NOW()),
('Disco lleno', 'Servidor de ficheros al 99%.', 'alta', 'abierta', NOW()),
('Crear cuenta de correo', 'Nuevo empleado en marketing.', 'baja', 'abierta', NOW()),
('Spam masivo', 'Revisar filtros antispam.', 'alta', 'en progreso', NOW()),
('Permisos de carpeta', 'Acceso denegado a /compartido.', 'media', 'cerrada', NOW()),
('Ratón sin pilas', 'Solicitud de pilas AA.', 'baja', 'cerrada', NOW()),

-- Bloque 4
('Fuente de alimentación', 'Huele a quemado en el PC 03.', 'alta', 'abierta', NOW()),
('Actualizar Java', 'Requerido para la web de hacienda.', 'media', 'en progreso', NOW()),
('Error al escanear', 'Escáner no envía a carpeta.', 'media', 'abierta', NOW()),
('Teclado numérico fallo', 'No marcan los números.', 'baja', 'cerrada', NOW()),
('Monitor sin señal', 'Revisar cable VGA.', 'baja', 'abierta', NOW()),
('Instalar Photoshop', 'Licencia adquirida para diseño.', 'media', 'abierta', NOW()),
('Fallo de tarjeta gráfica', 'Rayas en la pantalla.', 'alta', 'en progreso', NOW()),
('Router colgado', 'Necesita reinicio manual.', 'alta', 'cerrada', NOW()),
('Móvil de empresa', 'Configurar correo en Android.', 'media', 'abierta', NOW()),
('Tablet bloqueada', 'Olvido de patrón de desbloqueo.', 'baja', 'abierta', NOW()),

-- Bloque 5
('Silla rota', 'No es informático pero lo reportan aquí.', 'baja', 'cerrada', NOW()),
('Luz parpadea', 'Fluorescente sobre el rack.', 'baja', 'abierta', NOW()),
('Servidor SQL lento', 'Optimizar consultas.', 'alta', 'en progreso', NOW()),
('Error de certificado SSL', 'Web corporativa no segura.', 'alta', 'abierta', NOW()),
('Puerto USB roto', 'Frontal de la torre hundido.', 'media', 'cerrada', NOW()),
('Instalar 7zip', 'Compresor de archivos.', 'baja', 'abierta', NOW()),
('Pantalla azul (BSOD)', 'El equipo se reinicia solo.', 'alta', 'en progreso', NOW()),
('No abre PDF', 'Reinstalar Adobe Reader.', 'media', 'abierta', NOW()),
('Fallo de sincronización', 'OneDrive no actualiza.', 'media', 'cerrada', NOW()),
('Fin de soporte', 'Planificar migración de Windows 10.', 'alta', 'abierta', NOW());