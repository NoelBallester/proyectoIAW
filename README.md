# Proyecto IAW - Gestión de incidencias.
## Hecho por: Noel Ballester Baños y Ángela Navarro Nieto 

## Estructura de proyecto
![Estructura IAW](image-2.png)

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
git clone https://github.com/NoelBallester/proyectoIAW
cd proyectoIAW
bash deploy.sh (Este script en bash copia los ficheros del repositorio en tu directorio del servidor apache.)
Abre el navegador y escribe "localhost"
##### !! IMPORTANTE TENER SERVICIO DE APACHE Y COMPROBAR QUE TIENES CREADO EL VIRTULHOST PARA EL REPOSITORIO !!
 

### Crea la base de datos MySQL
mysql -u root -p
CREATE DATABASE inventario_iaw;

## Añade los archivos SQL:
mysql -u root -p inventario_iaw < sql/schema.sql
mysql -u root -p inventario_iaw < sql/seed.sql
