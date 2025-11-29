# Proyecto IAW - Gestión de incidencias.
## Hecho por: Noel Ballester Baños y Ángela Navarro Nieto 

## Estructura de proyecto
app/
 ├── pdo.php          # Conexión PDO
 ├── auth.php         # Gestión de sesión y protección
 ├── csrf.php         # Tokens CSRF
 └── utils.php        # Validaciones y helpers

public/
 ├── login.php        # Login
 ├── logout.php       # Logout
 ├── index.php        # Panel principal
 ├── lista_tickets.php # Listado con búsqueda y paginación
 ├── editar_ticket.php # Formulario de tickets
 ├── ver_tickets.php # Detalles de tickets
 ├── borrar_ticket.php # Borrado con auditoría
 └── preferencias.php # Tema visual por cookie

sql/
 ├── schema.sql       # Tablas e índices
 └── seed.sql         # Datos de ejemplo (≥50)

### Instalación en Ubuntu 
gir clone https://github.com/NoelBallester/proyectoIAW
cd proyectoIAW

### Crea la base de datos MySQL
mysql -u root -p
CREATE DATABASE iaw;

Añade los archivos SQL:
mysql -u root -p iaw < sql/schema.sql
mysql -u root -p iaw < sql/seed.sql
